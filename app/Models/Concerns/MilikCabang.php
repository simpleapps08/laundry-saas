<?php

namespace App\Models\Concerns;

use App\Models\Cabang;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait MilikCabang — isolasi data multi-tenant (2 tingkat: merchant > cabang).
 *
 * Setiap model yang memakai trait ini otomatis:
 *   1. Difilter berdasarkan cabang yang boleh diakses user yang sedang login
 *   2. Diisi `cabang_id` otomatis saat create
 *
 * ── TAHAP 2: ISOLASI 2 TINGKAT ────────────────────────────────────────────
 *
 * Aturan filter (urut dari paling longgar ke paling ketat):
 *
 *   a. Tidak ada user login (console/queue/seeder)  -> TANPA filter
 *   b. Super-admin Javacom (role SuperAdmin)        -> TANPA filter (semua merchant)
 *   c. Owner merchant / user ber-merchant           -> HANYA cabang milik merchant-nya
 *      - kalau ada "cabang aktif" di session        -> dipersempit ke 1 cabang itu
 *   d. User tanpa merchant (data lama)              -> HANYA cabang_id miliknya
 *
 * Bypass eksplisit: `Model::tanpaScopeCabang()`, `Model::whereCabang($id)`.
 */
trait MilikCabang
{
    public static function bootMilikCabang(): void
    {
        // ---------- 1. Filter otomatis saat membaca ----------
        static::addGlobalScope('cabang', function (Builder $builder) {
            if (! auth()->check()) {
                return; // console / queue / seeder — jangan filter
            }

            $user = auth()->user();
            $table = $builder->getModel()->getTable();

            // (b) Super-admin Javacom -> tanpa filter
            if ($user->isSuperAdmin()) {
                return;
            }

            $cabangIds = self::cabangIdsUntukScope($user);

            if (empty($cabangIds)) {
                // Tidak boleh lihat apa pun — pakai kondisi mustahil
                $builder->whereRaw('1 = 0');
                return;
            }

            // Kalau cuma 1 cabang -> `where =` (lebih cepat, pakai index)
            if (count($cabangIds) === 1) {
                $builder->where($table . '.cabang_id', $cabangIds[0]);
                return;
            }

            $builder->whereIn($table . '.cabang_id', $cabangIds);
        });

        // ---------- 2. Isi otomatis saat membuat data baru ----------
        static::creating(function (Model $model) {
            if ($model->cabang_id !== null || ! auth()->check()) {
                return;
            }

            $model->cabang_id = self::cabangAktifUntukTulis();
        });
    }

    /**
     * Daftar cabang yang boleh dibaca user.
     *
     * @return array<int>
     */
    protected static function cabangIdsUntukScope($user): array
    {
        // Owner/user ber-merchant -> seluruh cabang merchant-nya,
        // dipersempit kalau ada "cabang aktif" di session.
        if ($user->merchant_id !== null) {
            $cabangAktif = session('cabang_aktif_id');

            if ($cabangAktif !== null && $user->bolehAksesCabang($cabangAktif)) {
                return [(int) $cabangAktif];
            }

            return array_map('intval', $user->cabangIds());
        }

        // (d) Data lama: user hanya punya satu cabang_id
        if ($user->cabang_id !== null) {
            return [(int) $user->cabang_id];
        }

        return [];
    }

    /**
     * Cabang tujuan saat menulis data baru.
     *
     * Prioritas: cabang aktif di session -> cabang_id user -> cabang pertama merchant.
     */
    protected static function cabangAktifUntukTulis(): ?int
    {
        $user = auth()->user();
        if ($user === null) {
            return null;
        }

        $cabangAktif = session('cabang_aktif_id');
        if ($cabangAktif !== null && $user->bolehAksesCabang($cabangAktif)) {
            return (int) $cabangAktif;
        }

        if ($user->cabang_id !== null) {
            return (int) $user->cabang_id;
        }

        // Owner merchant menulis tanpa "cabang aktif" -> pakai cabang pertama
        $ids = $user->cabangIds();

        return $ids[0] ?? null;
    }

    /** Bypass filter — untuk super-admin atau laporan lintas cabang. */
    public function scopeTanpaScopeCabang(Builder $query): Builder
    {
        return $query->withoutGlobalScope('cabang');
    }

    /** Filter eksplisit ke satu cabang tertentu. */
    public function scopeWhereCabang(Builder $query, $cabangId): Builder
    {
        return $query->withoutGlobalScope('cabang')
                     ->where($this->getTable() . '.cabang_id', $cabangId);
    }

    /** Filter eksplisit ke beberapa cabang (mis. seluruh merchant). */
    public function scopeWhereCabangIn(Builder $query, array $cabangIds): Builder
    {
        return $query->withoutGlobalScope('cabang')
                     ->whereIn($this->getTable() . '.cabang_id', $cabangIds);
    }

    /** Relasi ke cabang. */
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }
}
