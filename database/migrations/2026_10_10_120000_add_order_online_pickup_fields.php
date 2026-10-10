<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * FASE F1 — Fondasi Order Online & Antar-Jemput.
 *
 * Menambah kolom pendukung di `transaksis`:
 *   - ongkir_numeric      : biaya antar-jemput (per km, diisi kasir manual)
 *   - jarak_km            : jarak tempuh (untuk catat & hitung ongkir)
 *   - mode_layanan        : pickup (dijemput) / dropoff (antar sendiri) / reguler
 *   - alamat_jemput       : alamat penjemputan/pengantaran
 *   - sumber_order        : 'kasir' (default) / 'online'
 *
 * Semua kolom NULLABLE / punya default aman supaya transaksi lama TIDAK berubah
 * perilaku (ongkir 0). Ini non-destruktif: tidak ada kolom lama diubah/dihapus.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            if (! Schema::hasColumn('transaksis', 'ongkir_numeric')) {
                $table->decimal('ongkir_numeric', 12, 2)->nullable()->default(0)->after('harga_akhir_numeric');
            }
            if (! Schema::hasColumn('transaksis', 'jarak_km')) {
                $table->decimal('jarak_km', 8, 2)->nullable()->after('ongkir_numeric');
            }
            if (! Schema::hasColumn('transaksis', 'mode_layanan')) {
                $table->string('mode_layanan', 20)->nullable()->default('reguler')->after('jarak_km');
            }
            if (! Schema::hasColumn('transaksis', 'alamat_jemput')) {
                $table->text('alamat_jemput')->nullable()->after('mode_layanan');
            }
            if (! Schema::hasColumn('transaksis', 'sumber_order')) {
                $table->string('sumber_order', 20)->nullable()->default('kasir')->after('alamat_jemput');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            foreach (['sumber_order', 'alamat_jemput', 'mode_layanan', 'jarak_km', 'ongkir_numeric'] as $col) {
                if (Schema::hasColumn('transaksis', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
