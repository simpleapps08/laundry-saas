<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // ── $setpage tersedia di SEMUA view ────────────────────────────
        // Sebelumnya hanya FrontController yang mengirim $setpage, sehingga
        // halaman publik lain (/daftar, /daftar/form) yang memakai navbar
        // gagal render (variabel tidak terdefinisi). View Composer
        // menyentralisasi ini: satu sumber, semua view dapat.
        //
        // Pakai '*' supaya juga mencakup partial (header.blade.php) yang
        // di-@include, bukan hanya view utama.
        View::composer('*', function ($view) {
            // Jangan timpa kalau controller sudah mengirim nilai sendiri.
            if (! array_key_exists('setpage', $view->getData())) {
                $view->with('setpage', \App\Models\PageSettings::first());
            }
        });

        // ── Blade directive paket langganan (Fase 3) ───────────────────
        // @fitur('telegram') ... @endfitur
        //     -> tampil hanya kalau paket cabang mengaktifkan fitur itu
        // @tanpaFitur('telegram') ... @endtanpaFitur
        //     -> kebalikannya, untuk menampilkan "upgrade paket"
        //
        // Super-admin (tanpa cabang) selalu lolos — lihat FiturPaket::punya().
        Blade::directive('fitur', function ($ekspresi) {
            return "<?php if (\\App\\Services\\FiturPaket::punya({$ekspresi})): ?>";
        });
        Blade::directive('endfitur', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('tanpaFitur', function ($ekspresi) {
            return "<?php if (! \\App\\Services\\FiturPaket::punya({$ekspresi})): ?>";
        });
        Blade::directive('endtanpaFitur', function () {
            return "<?php endif; ?>";
        });
    }
}
