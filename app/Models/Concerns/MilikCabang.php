<?php

namespace App\Models\Concerns;

use App\Models\Cabang;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait MilikCabang — isolasi data multi-tenant.
 *
 * Setiap model yang memakai trait ini otomatis:
 *   1. Difilter berdasarkan `cabang_id` user yang sedang login (global scope)
 *   2. Diisi `cabang_id` otomatis saat create
 *
 * Cara bypass scope (khusus super-admin / laporan lintas cabang):
 *   Transaksi::tanpaScopeCabang()->get()
 *   Transaksi::whereCabang($id)->get()
 *
 * PENTING: global scope hanya aktif kalau ada user login. Di console/queue
 * (tidak ada auth), scope tidak jalan — supaya seeder & cron tetap bisa
 * mengakses semua data. Gunakan `tanpaScopeCabang()` untuk mengakses
 * lintas cabang secara eksplisit.
 */
trait MilikCabang
{
    public static function bootMilikCabang(): void
    {
        // 1. Filter otomatis saat membaca
        static::addGlobalScope('cabang', function (Builder $builder) {
            if (! auth()->check()) {
                return; // console / queue / seeder — jangan filter
            }

            $cabangId = auth()->user()->cabang_id ?? null;

            // Super-admin (tanpa cabang) boleh lihat semua
            if ($cabangId === null) {
                return;
            }

            $builder->where(
                $builder->getModel()->getTable() . '.cabang_id',
                $cabangId
            );
        });

        // 2. Isi otomatis saat membuat data baru
        static::creating(function (Model $model) {
            if ($model->cabang_id === null && auth()->check()) {
                $model->cabang_id = auth()->user()->cabang_id ?? null;
            }
        });
    }

    /**
     * Bypass filter cabang — untuk super-admin atau laporan lintas cabang.
     */
    public function scopeTanpaScopeCabang(Builder $query): Builder
    {
        return $query->withoutGlobalScope('cabang');
    }

    /**
     * Filter eksplisit ke satu cabang tertentu.
     */
    public function scopeWhereCabang(Builder $query, $cabangId): Builder
    {
        return $query->withoutGlobalScope('cabang')
                     ->where($this->getTable() . '.cabang_id', $cabangId);
    }

    /**
     * Relasi ke cabang.
     */
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }
}
