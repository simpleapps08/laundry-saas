<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware CabangAktif — TAHAP 2 (isolasi 2 tingkat).
 *
 * Tugas:
 *   1. Validasi `cabang_aktif_id` di session: kalau cabang itu BUKAN milik
 *      user (mis. sesi basi / dimanipulasi), reset ke null.
 *      Ini lapisan pertahanan terhadap kebocoran lintas-merchant.
 *   2. Untuk user yang punya cabang tapi belum punya cabang aktif, set
 *      otomatis ke cabang pertama yang boleh diakses.
 *
 * Tidak melakukan redirect — hanya menjaga agar session selalu konsisten.
 */
class CabangAktifMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user === null || ! method_exists($user, 'bolehAksesCabang')) {
            return $next($request);
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $cabangAktif = session('cabang_aktif_id');

        // 1. Validasi: cabang aktif HARUS boleh diakses user ini.
        if ($cabangAktif !== null && ! $user->bolehAksesCabang($cabangAktif)) {
            session()->forget('cabang_aktif_id');
            $cabangAktif = null;
        }

        // 2. SENGAJA TIDAK auto-set cabang aktif.
        //
        // `cabang_aktif_id` hanya diisi ketika user MEMILIH cabang lewat
        // pemilih cabang. Kalau kosong, artinya mode "Semua Cabang" —
        // user melihat seluruh cabang milik merchant-nya (lihat
        // MilikCabang::cabangIdsUntukScope()). Auto-set akan membuat
        // mode "Semua Cabang" tidak pernah tercapai.

        return $next($request);
    }
}
