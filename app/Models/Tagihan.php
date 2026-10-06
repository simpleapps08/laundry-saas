<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';

    protected $fillable = [
        'langganan_id', 'cabang_id', 'nomor', 'jumlah', 'status',
        'jatuh_tempo', 'periode_mulai', 'periode_akhir',
        'dibayar_pada', 'metode_bayar', 'bukti_bayar', 'catatan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'jatuh_tempo' => 'date',
        'periode_mulai' => 'date',
        'periode_akhir' => 'date',
        'dibayar_pada' => 'datetime',
    ];

    public function langganan(): BelongsTo
    {
        return $this->belongsTo(Langganan::class, 'langganan_id');
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function scopeBelumBayar($query)
    {
        return $query->whereIn('status', ['belum_bayar', 'menunggu_verifikasi']);
    }

    public function sudahLunas(): bool
    {
        return $this->status === 'lunas';
    }

    public function terlambat(): bool
    {
        return ! $this->sudahLunas()
            && $this->jatuh_tempo !== null
            && $this->jatuh_tempo->isPast();
    }

    /**
     * Nomor tagihan berurutan: INV-YYYYMM-0001
     */
    public static function buatNomor(): string
    {
        $prefix = 'SUB-' . date('Ym') . '-';
        $terakhir = static::where('nomor', 'like', $prefix . '%')
            ->orderByDesc('nomor')->value('nomor');

        $urut = $terakhir ? ((int) substr($terakhir, -4)) + 1 : 1;

        return $prefix . str_pad((string) $urut, 4, '0', STR_PAD_LEFT);
    }
}
