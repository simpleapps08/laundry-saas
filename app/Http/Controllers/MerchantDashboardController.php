<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Services\MerchantDashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * MerchantDashboardController — TAHAP 3.
 *
 * Menyajikan dashboard lintas cabang untuk owner merchant,
 * plus endpoint ganti "cabang aktif".
 */
class MerchantDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard merchant — agregat seluruh cabang yang boleh dilihat.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $semuaCabang = MerchantDashboard::cabangTersedia();
        $semuaIds    = $semuaCabang->pluck('id')->map('intval')->all();

        // Mode: "semua" atau 1 cabang tertentu
        $mode        = $request->get('mode', 'semua');
        $cabangAktif = session('cabang_aktif_id');

        if ($mode === 'cabang' && $cabangAktif !== null && $user->bolehAksesCabang($cabangAktif)) {
            $ids = [(int) $cabangAktif];
        } elseif ($mode === 'cabang' && ! empty($semuaIds)) {
            $ids = [$semuaIds[0]];
        } else {
            $ids  = $semuaIds;
            $mode = 'semua';
        }

        $ringkasan = MerchantDashboard::ringkasan($ids);
        $perCabang = MerchantDashboard::perCabang($semuaIds);
        $harian    = MerchantDashboard::grafikHarian($ids);
        $bulanan   = MerchantDashboard::grafikBulanan($ids);

        return view('modul_admin.merchant-dashboard', [
            'semuaCabang'  => $semuaCabang,
            'mode'         => $mode,
            'cabangAktif'  => $cabangAktif,
            'dilihat'      => $ids,
            'ringkasan'    => $ringkasan,
            'perCabang'    => $perCabang,
            'grafikHarian' => $harian,
            'grafikBulanan'=> $bulanan,
        ]);
    }

    /**
     * Ganti cabang aktif (dipakai pemilih cabang di navbar).
     */
    public function gantiCabang(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'cabang_id' => 'nullable|integer',
        ]);

        $cabangId = $data['cabang_id'] ?? null;

        // null / kosong = mode "semua cabang"
        if ($cabangId === null) {
            session()->forget('cabang_aktif_id');
            return back()->with('sukses', 'Menampilkan semua cabang.');
        }

        // Tolak kalau bukan milik merchant user — lapisan anti-kebocoran
        if (! $user->bolehAksesCabang($cabangId)) {
            abort(403, 'Anda tidak berhak mengakses cabang tersebut.');
        }

        session(['cabang_aktif_id' => (int) $cabangId]);

        $nama = Cabang::find($cabangId)?->nama ?? 'cabang';

        return back()->with('sukses', "Cabang aktif: {$nama}");
    }
}
