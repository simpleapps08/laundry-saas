<?php

namespace App\Services;

use App\Models\Cabang;
use App\Models\Langganan;
use Illuminate\Support\Facades\Cache;

/**
 * FiturPaket — pusat pengecekan fitur & batas paket langganan.
 *
 * Pemakaian di controller:
 *   if (! FiturPaket::punya('telegram')) { abort(403); }
 *
 * Pemakaian di Blade:
 *   @fitur('telegram') ... @endfitur
 *
 * Hasil di-cache 5 menit per cabang supaya tidak query tiap render.
 */
class FiturPaket
{
    private const TTL = 300; // detik

    /**
     * Langganan aktif untuk sebuah cabang (atau null).
     */
    public static function langganan(?int $cabangId): ?Langganan
    {
        if ($cabangId === null) {
            return null;
        }

        return Cache::remember("langganan.cabang.{$cabangId}", self::TTL, function () use ($cabangId) {
            return Langganan::with('paket')
                ->where('cabang_id', $cabangId)
                ->orderByDesc('id')
                ->first();
        });
    }

    /**
     * Apakah fitur tersedia untuk cabang ini?
     *
     * Aturan aman:
     *  - tanpa cabang (super-admin)  -> selalu boleh (mereka mengelola semua)
     *  - tanpa langganan             -> TIDAK boleh (wajib berlangganan)
     *  - langganan tidak berlaku     -> TIDAK boleh
     */
    public static function punya(string $fitur, ?int $cabangId = null): bool
    {
        $cabangId ??= auth()->user()->cabang_id ?? null;

        if ($cabangId === null) {
            return true; // super-admin
        }

        $langganan = self::langganan($cabangId);

        if ($langganan === null || ! $langganan->masihBerlaku()) {
            return false;
        }

        return (bool) $langganan->paket?->punya($fitur);
    }

    /**
     * Sisa kuota untuk jenis batas tertentu.
     * Mengembalikan -1 kalau tanpa batas.
     */
    public static function sisaKuota(string $jenis, ?int $cabangId = null): int
    {
        $cabangId ??= auth()->user()->cabang_id ?? null;

        if ($cabangId === null) {
            return -1;
        }

        $langganan = self::langganan($cabangId);
        $paket = $langganan?->paket;

        if ($paket === null) {
            return 0;
        }

        $batas = $paket->batas($jenis);

        if ($batas === -1) {
            return -1;
        }

        $terpakai = match ($jenis) {
            'cabang' => Cabang::where('id', $cabangId)->count(),
            'user' => \App\Models\User::where('cabang_id', $cabangId)->count(),
            'transaksi' => \App\Models\transaksi::where('cabang_id', $cabangId)
                ->whereYear('tanggal_masuk', date('Y'))
                ->whereMonth('tanggal_masuk', date('m'))
                ->count(),
            default => 0,
        };

        return max(0, $batas - $terpakai);
    }

    /**
     * Apakah masih boleh menambah data? (untuk cek sebelum create)
     */
    public static function bolehTambah(string $jenis, ?int $cabangId = null): bool
    {
        $sisa = self::sisaKuota($jenis, $cabangId);

        return $sisa === -1 || $sisa > 0;
    }

    /**
     * Bersihkan cache — panggil setelah langganan berubah.
     */
    public static function bersihkan(?int $cabangId = null): void
    {
        if ($cabangId === null) {
            Cache::flush();

            return;
        }

        Cache::forget("langganan.cabang.{$cabangId}");
    }
}
