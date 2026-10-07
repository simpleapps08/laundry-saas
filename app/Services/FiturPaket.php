<?php

namespace App\Services;

use App\Models\Cabang;
use App\Models\Langganan;
use App\Models\Merchant;
use App\Models\User;
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
 * Aturan isolasi (Tahap 4):
 *  - Paket acuan sebuah CABANG = langganan aktif cabang itu.
 *  - Paket acuan sebuah MERCHANT = paket TERTINGGI di antara langganan aktifnya
 *    (keputusan Boz: "ambil yang tertinggi").
 *  - Kuota cabang selalu dihitung di tingkat MERCHANT, bukan per cabang.
 *
 * Hasil di-cache 5 menit supaya tidak query tiap render.
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
     * Paket efektif untuk sebuah cabang (via merchant kalau ada).
     */
    public static function paketUntukCabang(?int $cabangId): ?\App\Models\Paket
    {
        if ($cabangId === null) {
            return null;
        }

        $langganan = self::langganan($cabangId);

        if ($langganan === null) {
            return null;
        }

        // Kalau cabang punya merchant, pakai paket tertinggi merchant
        // supaya SEMUA cabang merchant menikmati paket tertinggi.
        if ($langganan->merchant_id !== null) {
            $merchant = Merchant::find($langganan->merchant_id);
            if ($merchant) {
                return HargaLangganan::paketTertinggi($merchant) ?? $langganan->paket;
            }
        }

        return $langganan->paket;
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

        return (bool) self::paketUntukCabang($cabangId)?->punya($fitur);
    }

    /**
     * Sisa kuota untuk jenis batas tertentu.
     * Mengembalikan -1 kalau tanpa batas.
     *
     * PENTING: kuota 'cabang' dihitung di tingkat MERCHANT (bukan per cabang) —
     * kalau tidak, setiap cabang akan menganggap dirinya boleh nambah cabang.
     */
    public static function sisaKuota(string $jenis, ?int $cabangId = null): int
    {
        $cabangId ??= auth()->user()->cabang_id ?? null;

        if ($cabangId === null) {
            return -1;
        }

        $paket = self::paketUntukCabang($cabangId);

        if ($paket === null) {
            return 0;
        }

        $batas = $paket->batas($jenis);

        if ($batas === -1) {
            return -1;
        }

        // ── Khusus kuota CABANG: hitung di tingkat merchant ──────────────
        if ($jenis === 'cabang') {
            $langganan = self::langganan($cabangId);
            $merchantId = $langganan?->merchant_id;

            $terpakai = $merchantId !== null
                ? Cabang::where('merchant_id', $merchantId)->count()
                : Cabang::where('id', $cabangId)->count();

            return max(0, $batas - $terpakai);
        }

        $terpakai = match ($jenis) {
            'user' => User::where('cabang_id', $cabangId)->count(),
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

        // Bersihkan juga cache cabang lain milik merchant yang sama,
        // karena paket tertinggi merchant memengaruhi semua cabangnya.
        $langganan = Langganan::where('cabang_id', $cabangId)->first();
        if ($langganan?->merchant_id !== null) {
            Langganan::where('merchant_id', $langganan->merchant_id)
                ->pluck('cabang_id')
                ->each(fn ($cid) => Cache::forget("langganan.cabang.{$cid}"));
        }
    }
}
