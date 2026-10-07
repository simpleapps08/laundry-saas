<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

/**
 * DiskonLangganan — setelan diskon bertingkat per jumlah cabang.
 *
 * Diatur dari panel super-admin (/superadmin/setelan). Satu sumber kebenaran
 * untuk diskon; dipakai HargaLangganan saat menghitung tagihan.
 */
class DiskonLangganan extends Model
{
    protected $table = 'diskon_langganan';

    protected $fillable = [
        'min_cabang', 'diskon_persen', 'label', 'is_aktif', 'urutan',
    ];

    protected $casts = [
        'min_cabang' => 'integer',
        'diskon_persen' => 'decimal:2',
        'is_aktif' => 'boolean',
        'urutan' => 'integer',
    ];

    public const CACHE_KEY = 'diskon_langganan.semua';

    /**
     * Semua tingkatan aktif, urut dari min_cabang terkecil.
     * Di-cache karena dipanggil tiap perhitungan harga.
     */
    public static function semua(): \Illuminate\Support\Collection
    {
        return Cache::remember(self::CACHE_KEY, 300, function () {
            return static::where('is_aktif', true)
                ->orderBy('min_cabang')
                ->get();
        });
    }

    /**
     * Persentase diskon untuk sejumlah cabang.
     * Ambil tingkatan dengan min_cabang TERBESAR yang <= jumlah cabang.
     */
    public static function persenUntuk(int $jumlahCabang): float
    {
        $tingkat = static::semua()
            ->filter(fn ($d) => $jumlahCabang >= (int) $d->min_cabang)
            ->sortByDesc('min_cabang')
            ->first();

        return $tingkat ? (float) $tingkat->diskon_persen : 0.0;
    }

    public function bersihkanCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function bersihkanSemuaCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
