<?php

namespace App\Services;

use App\Models\Cabang;
use App\Models\transaksi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * MerchantDashboard — TAHAP 3.
 *
 * Menyusun angka agregat lintas cabang untuk:
 *   - Owner merchant  -> seluruh cabang milik merchant-nya
 *   - Admin cabang    -> 1 cabang
 *   - Super-admin     -> semua cabang
 *
 * Semua query memakai tanpaScopeCabang() lalu DIBATASI eksplisit ke daftar
 * cabang yang boleh dilihat. Ini menjaga isolasi tetap di satu tempat.
 */
class MerchantDashboard
{
    /**
     * Daftar cabang yang boleh dilihat user saat ini.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function cabangTersedia()
    {
        $user = Auth::user();

        if ($user === null) {
            return collect();
        }

        if ($user->isSuperAdmin()) {
            return Cabang::orderBy('nama')->get();
        }

        $ids = $user->cabangIds();

        return Cabang::whereIn('id', $ids)->orderBy('nama')->get();
    }

    /**
     * Ringkasan agregat untuk sekumpulan cabang.
     *
     * @param  array<int>  $cabangIds
     * @return array<string,mixed>
     */
    public static function ringkasan(array $cabangIds): array
    {
        if (empty($cabangIds)) {
            return self::kosong();
        }

        $q = fn () => transaksi::tanpaScopeCabang()->whereIn('cabang_id', $cabangIds);

        $tahun = date('Y');
        $bulan = date('m');

        $masuk    = (clone $q())->whereIn('status_order', ['Process', 'Done', 'Delivery'])->count();
        $selesai  = (clone $q())->where('status_order', 'Done')->count();
        $diambil  = (clone $q())->where('status_order', 'Delivery')->count();
        $bayar    = (clone $q())->where('status_payment', 'Success')->count();
        $belum    = (clone $q())->where('status_payment', 'Pending')->count();

        $pendapatanTotal = (float) (clone $q())->where('status_payment', 'Success')->sum('total_numeric');
        $pendapatanBulan = (float) (clone $q())->where('status_payment', 'Success')
            ->whereYear('tanggal_masuk', $tahun)->whereMonth('tanggal_masuk', $bulan)
            ->sum('total_numeric');
        $pendapatanHari  = (float) (clone $q())->where('status_payment', 'Success')
            ->whereDate('tanggal_masuk', now()->toDateString())
            ->sum('total_numeric');

        $kgBulan = (float) (clone $q())->whereYear('tanggal_masuk', $tahun)
            ->whereMonth('tanggal_masuk', $bulan)->sum('kg_numeric');

        return [
            'masuk'            => $masuk,
            'selesai'          => $selesai,
            'diambil'          => $diambil,
            'sudahbayar'       => $bayar,
            'belumbayar'       => $belum,
            'pendapatan_total' => $pendapatanTotal,
            'pendapatan_bulan' => $pendapatanBulan,
            'pendapatan_hari'  => $pendapatanHari,
            'kg_bulan'         => $kgBulan,
        ];
    }

    /**
     * Rincian per cabang (untuk tabel perbandingan).
     *
     * @param  array<int>  $cabangIds
     * @return array<int,array<string,mixed>>
     */
    public static function perCabang(array $cabangIds): array
    {
        if (empty($cabangIds)) {
            return [];
        }

        $tahun = date('Y');
        $bulan = date('m');

        $hasil = [];

        foreach (Cabang::whereIn('id', $cabangIds)->orderBy('nama')->get() as $c) {
            $q = fn () => transaksi::tanpaScopeCabang()->where('cabang_id', $c->id);

            $hasil[] = [
                'cabang_id'        => $c->id,
                'nama'             => $c->nama,
                'kode'             => $c->kode,
                'status'           => $c->status,
                'masuk'            => (clone $q())->whereIn('status_order', ['Process', 'Done', 'Delivery'])->count(),
                'selesai'          => (clone $q())->where('status_order', 'Done')->count(),
                'pendapatan_bulan' => (float) (clone $q())->where('status_payment', 'Success')
                                        ->whereYear('tanggal_masuk', $tahun)
                                        ->whereMonth('tanggal_masuk', $bulan)
                                        ->sum('total_numeric'),
                'pendapatan_total' => (float) (clone $q())->where('status_payment', 'Success')
                                        ->sum('total_numeric'),
                'kg_bulan'         => (float) (clone $q())->whereYear('tanggal_masuk', $tahun)
                                        ->whereMonth('tanggal_masuk', $bulan)
                                        ->sum('kg_numeric'),
                'belumbayar'       => (clone $q())->where('status_payment', 'Pending')->count(),
            ];
        }

        return $hasil;
    }

    /**
     * Data grafik: transaksi harian bulan ini untuk sekumpulan cabang.
     *
     * @param  array<int>  $cabangIds
     * @return array{label:string, nilai:string}
     */
    public static function grafikHarian(array $cabangIds): array
    {
        if (empty($cabangIds)) {
            return ['label' => '', 'nilai' => ''];
        }

        $rows = DB::table('transaksis')
            ->select(DB::raw('DAY(tanggal_masuk) AS hari'), DB::raw('COUNT(*) AS jml'))
            ->whereIn('cabang_id', $cabangIds)
            ->whereYear('tanggal_masuk', date('Y'))
            ->whereMonth('tanggal_masuk', date('m'))
            ->whereNotNull('tanggal_masuk')
            ->groupBy(DB::raw('DAY(tanggal_masuk)'))
            ->pluck('jml', 'hari');

        $label = []; $nilai = [];
        for ($i = 1; $i <= 31; $i++) {
            $label[] = (string) $i;
            $nilai[] = (string) ($rows[$i] ?? 0);
        }

        return ['label' => implode(',', $label), 'nilai' => implode(',', $nilai)];
    }

    /**
     * Pendapatan per bulan (1 tahun berjalan) untuk grafik batang.
     */
    public static function grafikBulanan(array $cabangIds): array
    {
        if (empty($cabangIds)) {
            return ['label' => '', 'nilai' => '', 'pendapatan' => ''];
        }

        $rows = DB::table('transaksis')
            ->select(DB::raw('MONTH(tanggal_masuk) AS bln'), DB::raw('COUNT(*) AS jml'),
                     DB::raw('SUM(COALESCE(total_numeric,0)) AS rp'))
            ->whereIn('cabang_id', $cabangIds)
            ->whereYear('tanggal_masuk', date('Y'))
            ->whereNotNull('tanggal_masuk')
            ->groupBy(DB::raw('MONTH(tanggal_masuk)'))
            ->get()->keyBy('bln');

        $label = []; $nilai = []; $rp = [];
        for ($i = 1; $i <= 12; $i++) {
            $label[] = (string) $i;
            $nilai[] = (string) ($rows[$i]->jml ?? 0);
            $rp[]    = (string) round((float) ($rows[$i]->rp ?? 0));
        }

        return ['label' => implode(',', $label), 'nilai' => implode(',', $nilai), 'pendapatan' => implode(',', $rp)];
    }

    protected static function kosong(): array
    {
        return [
            'masuk' => 0, 'selesai' => 0, 'diambil' => 0,
            'sudahbayar' => 0, 'belumbayar' => 0,
            'pendapatan_total' => 0, 'pendapatan_bulan' => 0,
            'pendapatan_hari' => 0, 'kg_bulan' => 0,
        ];
    }
}
