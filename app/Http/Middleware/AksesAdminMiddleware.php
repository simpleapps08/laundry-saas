<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware AksesAdmin — gerbang halaman admin yang sadar super-admin.
 *
 * MASALAH YANG DIPERBAIKI:
 *   Rute admin dulu memakai `role:Admin` (Spatie). Super-admin berperan
 *   'SuperAdmin', sehingga hasRole('Admin') = false → 403 di SEMUA halaman
 *   admin. Akibatnya super-admin hanya bisa membuka /super-admin.
 *
 * SOLUSI:
 *   Super-admin lolos tanpa melihat daftar role; peran lain tetap dicek.
 *   Definisi super-admin memakai User::isSuperAdmin() supaya hanya ada
 *   SATU sumber kebenaran (sama seperti FiturPaketMiddleware).
 *
 * PEMAKAIAN di routes/web.php:
 *   Route::middleware('akses.admin')->group(...)
 */
class AksesAdminMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        if ($user === null) {
            abort(403, 'Silakan masuk terlebih dahulu.');
        }

        // Super-admin = akses penuh, tidak perlu dicek peran.
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return $next($request);
        }

        // Peran lain: periksa seperti biasa (default 'Admin').
        $roles = $roles ?: ['Admin'];

        foreach ($roles as $role) {
            if (method_exists($user, 'hasRole') && $user->hasRole($role)) {
                return $next($request);
            }
        }

        abort(403, 'Akses khusus admin.');
    }
}
