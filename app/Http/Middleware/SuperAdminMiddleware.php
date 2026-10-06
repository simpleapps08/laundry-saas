<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware SuperAdmin — batasi route hanya untuk super-admin.
 *
 * Super-admin = user berperan 'SuperAdmin', ATAU Admin tanpa cabang
 * (cabang_id NULL). Definisi tunggal ada di User::isSuperAdmin() supaya
 * hanya ada satu sumber kebenaran.
 *
 * Daftarkan di Kernel.php:
 *   'superadmin' => \App\Http\Middleware\SuperAdminMiddleware::class,
 */
class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user === null || ! $user->isSuperAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Akses khusus super-admin.'], 403);
            }

            abort(403, 'Akses khusus super-admin.');
        }

        return $next($request);
    }
}
