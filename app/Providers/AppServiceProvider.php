<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
