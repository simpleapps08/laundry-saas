<?php

namespace App\Http\Middleware;

use App\Services\FiturPaket;
use Closure;
use Illuminate\Http\Request;

/**
 * Middleware FiturPaketMiddleware — batasi route berdasarkan fitur paket.
 *
 * Daftarkan di Kernel.php:
 *   'fitur' => \App\Http\Middleware\FiturPaketMiddleware::class,
 *
 * Pakai di route:
 *   Route::get('export', ...)->middleware('fitur:excel');
 *   Route::get('wa', ...)->middleware('fitur:whatsapp');
 *
 * Super-admin (tanpa cabang) selalu lolos.
 */
class FiturPaketMiddleware
{
    public function handle(Request $request, Closure $next, string $fitur)
    {
        if (! FiturPaket::punya($fitur)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Fitur tidak tersedia di paket Anda.',
                    'fitur' => $fitur,
                ], 403);
            }

            return response()->view('errors.fitur-terkunci', [
                'fitur' => $fitur,
            ], 403);
        }

        return $next($request);
    }
}
