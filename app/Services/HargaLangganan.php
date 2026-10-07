<?php

namespace App\Services;

use App\Models\Cabang;
use App\Models\DiskonLangganan;
use App\Models\Langganan;
use App\Models\Merchant;

/**
 * HargaLangganan — SATU SUMBER KEBENARAN perhitungan harga langganan.
 *
 * Model harga (keputusan Boz, Tahap 4 — Opsi C):
 *   harga = harga_paket × jumlah_cabang × (1 − diskon_persen/100)
 *
 * Diskon bertingkat diambil dari tabel `diskon_langganan` (diatur super-admin).
 *
 * Paket "tertinggi" per merchant:
 *   kalau merchant punya beberapa langganan dengan paket berbeda, yang dipakai
 *   sebagai paket acuan merchant adalah yang `urutan` paketnya paling tinggi.
 */
class HargaLangganan
{
    /**
     * Hitung harga satu langganan (satu cabang).
     */
    public static function hitung(Langganan $langganan, ?int $jumlahCabang = null): array
    {
        $paket = $langganan->paket;

        if ($paket === null) {
            return self::kosong();
        }

        $jumlahCabang = max(1, (int) ($jumlahCabang ?? $langganan->jumlah_cabang ?? 1));

        $hargaSatuan = (float) ($langganan->harga_disepakati
            ?? $paket->hargaUntuk($langganan->siklus));

        $subtotal = $hargaSatuan * $jumlahCabang;
        $persen   = DiskonLangganan::persenUntuk($jumlahCabang);
        $diskon   = round($subtotal * ($persen / 100), 2);
        $total    = round($subtotal - $diskon, 2);

        return [
            'harga_satuan' => round($hargaSatuan, 2),
            'jumlah_cabang' => $jumlahCabang,
            'subtotal' => round($subtotal, 2),
            'diskon_persen' => $persen,
            'diskon_nilai' => $diskon,
            'total' => $total,
            'siklus' => $langganan->siklus,
        ];
    }

    /**
     * Hitung total langganan sebuah merchant (semua cabangnya).
     *
     * @return array{baris: array, total: float, subtotal: float, diskon_nilai: float, jumlah_cabang: int, persen: float}
     */
    public static function hitungMerchant(Merchant $merchant): array
    {
        $langganans = Langganan::with('paket')
            ->where('merchant_id', $merchant->id)
            ->whereIn('status', ['trial', 'aktif'])
            ->get();

        $jumlahCabang = $langganans->count();

        if ($jumlahCabang === 0) {
            return [
                'baris' => [],
                'subtotal' => 0.0,
                'diskon_nilai' => 0.0,
                'diskon_persen' => 0.0,
                'total' => 0.0,
                'jumlah_cabang' => 0,
            ];
        }

        // Satu tingkatan diskon untuk SELURUH merchant (keputusan: diskon
        // dihitung dari jumlah cabang merchant, bukan per baris).
        $persen = DiskonLangganan::persenUntuk($jumlahCabang);

        $baris = [];
        $subtotal = 0.0;
        $total = 0.0;

        foreach ($langganans as $l) {
            $hargaSatuan = (float) ($l->harga_disepakati ?? $l->paket?->hargaUntuk($l->siklus) ?? 0);
            $diskonBaris = round($hargaSatuan * ($persen / 100), 2);
            $totalBaris  = round($hargaSatuan - $diskonBaris, 2);

            $baris[] = [
                'langganan_id' => $l->id,
                'cabang_id' => $l->cabang_id,
                'cabang' => $l->cabang?->nama ?? $l->cabang()->value('nama'),
                'paket' => $l->paket?->nama,
                'status' => $l->status,
                'siklus' => $l->siklus,
                'harga_satuan' => round($hargaSatuan, 2),
                'diskon_nilai' => $diskonBaris,
                'total' => $totalBaris,
            ];

            $subtotal += $hargaSatuan;
            $total    += $totalBaris;
        }

        return [
            'baris' => $baris,
            'subtotal' => round($subtotal, 2),
            'diskon_nilai' => round($subtotal - $total, 2),
            'diskon_persen' => $persen,
            'total' => round($total, 2),
            'jumlah_cabang' => $jumlahCabang,
        ];
    }

    /**
     * Harga satu paket untuk sejumlah cabang (dipakai kalkulator & pratinjau).
     */
    public static function hargaPaket(\App\Models\Paket $paket, string $siklus, int $jumlahCabang): array
    {
        $jumlahCabang = max(1, $jumlahCabang);
        $hargaSatuan = (float) $paket->hargaUntuk($siklus);
        $subtotal = round($hargaSatuan * $jumlahCabang, 2);
        $persen = DiskonLangganan::persenUntuk($jumlahCabang);
        $diskon = round($subtotal * ($persen / 100), 2);

        return [
            'harga_satuan' => round($hargaSatuan, 2),
            'jumlah_cabang' => $jumlahCabang,
            'subtotal' => $subtotal,
            'diskon_persen' => $persen,
            'diskon_nilai' => $diskon,
            'total' => round($subtotal - $diskon, 2),
        ];
    }

    /**
     * Paket acuan merchant = paket dengan `urutan` tertinggi di antara
     * langganan aktif merchant tsb. (Keputusan: "ambil yang tertinggi".)
     */
    public static function paketTertinggi(Merchant $merchant): ?\App\Models\Paket
    {
        return Langganan::with('paket')
            ->where('merchant_id', $merchant->id)
            ->whereIn('status', ['trial', 'aktif'])
            ->get()
            ->pluck('paket')
            ->filter()
            ->sortByDesc(fn ($p) => (int) $p->urutan)
            ->first();
    }

    /**
     * Persentase diskon untuk merchant (berdasarkan jml cabang langganannya).
     */
    public static function persenMerchant(Merchant $merchant): float
    {
        $jml = Langganan::where('merchant_id', $merchant->id)
            ->whereIn('status', ['trial', 'aktif'])
            ->count();

        return DiskonLangganan::persenUntuk(max(1, $jml));
    }

    private static function kosong(): array
    {
        return [
            'harga_satuan' => 0.0,
            'jumlah_cabang' => 0,
            'subtotal' => 0.0,
            'diskon_persen' => 0.0,
            'diskon_nilai' => 0.0,
            'total' => 0.0,
            'siklus' => null,
        ];
    }
}
