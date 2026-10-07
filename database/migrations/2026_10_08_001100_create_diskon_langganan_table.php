<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tahap 4 — Tabel setelan diskon bertingkat (bisa diatur super admin).
 *
 * Satu baris per tingkatan jumlah cabang. `diskon_persen` berlaku untuk
 * semua paket, kecuali ada override di `paket_persentase` (JSON) bila suatu
 * paket ingin aturan sendiri.
 *
 * Contoh isi:
 *   1 cabang  → 0%    5 cabang  → 30%
 *   2 cabang  → 10%   6+ cabang → 35%
 *   3 cabang  → 20%
 *   4 cabang  → 25%
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('diskon_langganan')) {
            return;
        }

        Schema::create('diskon_langganan', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('min_cabang')->unique()
                ->comment('Berlaku untuk jumlah cabang >= nilai ini');
            $table->decimal('diskon_persen', 5, 2)->default(0)
                ->comment('Diskon 0-100 persen');
            $table->string('label', 60)->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();

            $table->index(['is_aktif', 'min_cabang']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diskon_langganan');
    }
};
