<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FASE 1 — Fondasi Data (lanjutan)
 *
 * 1. Menambahkan `cabang_id` (FK ke `cabang`) pada tabel yang datanya
 *    harus terisolasi per cabang: users, transaksis, hargas,
 *    laundry_settings, notifications_settings.
 *
 * 2. Mengkonversi tipe data dari string ke numerik/tanggal yang benar:
 *    - transaksis.kg/harga/disc/harga_akhir  string -> decimal
 *    - transaksis.tgl_transaksi/tgl_ambil    string -> date
 *    - hargas.kg/harga                       string -> decimal
 *
 *    Sebelumnya semua bertipe string sehingga SUM()/AVG()/query rentang
 *    tanggal tidak bisa dilakukan — laporan harus konversi manual.
 *
 * 3. Menambahkan kolom `cabang_id` ke `hargas` supaya harga jadi milik
 *    CABANG, bukan milik akun karyawan (sebelumnya: setiap karyawan harus
 *    input daftar harga sendiri meski satu cabang).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. KOLOM cabang_id ──────────────────────────────────────────
        $tabelCabang = ['users', 'transaksis', 'hargas', 'laundry_settings', 'notifications_settings'];

        foreach ($tabelCabang as $nama) {
            if (Schema::hasTable($nama) && !Schema::hasColumn($nama, 'cabang_id')) {
                Schema::table($nama, function (Blueprint $table) {
                    $table->unsignedBigInteger('cabang_id')->nullable()->after('id');
                    $table->foreign('cabang_id')
                          ->references('id')->on('cabang')
                          ->nullOnDelete()->cascadeOnUpdate();
                    $table->index('cabang_id');
                });
            }
        }

        // ── 2. KONVERSI TIPE DATA — transaksis ──────────────────────────
        Schema::table('transaksis', function (Blueprint $table) {
            // Nilai "10", "10.5", "kg" dsb -> numerik. Nullable agar baris
            // lama yang tidak valid tidak memblokir migrasi.
            $table->decimal('kg_numeric', 8, 2)->nullable()->after('kg');
            $table->decimal('harga_numeric', 12, 2)->nullable()->after('harga');
            $table->decimal('disc_numeric', 12, 2)->nullable()->after('disc');
            $table->decimal('harga_akhir_numeric', 12, 2)->nullable()->after('harga_akhir');
            $table->decimal('total_numeric', 14, 2)->nullable()->after('harga_akhir_numeric');

            $table->date('tanggal_masuk')->nullable()->after('tgl_transaksi');
            $table->date('tanggal_ambil')->nullable()->after('tgl_ambil');

            $table->index('tanggal_masuk');
            $table->index('status_order');
            $table->index('status_payment');
        });

        // ── 2b. KONVERSI TIPE DATA — hargas ─────────────────────────────
        Schema::table('hargas', function (Blueprint $table) {
            $table->decimal('kg_numeric', 8, 2)->nullable()->after('kg');
            $table->decimal('harga_numeric', 12, 2)->nullable()->after('harga');
        });

        // ── 3. BACKFILL data lama (kalau ada) ───────────────────────────
        // Pakai raw SQL agar aman terhadap format string campuran.
        if (Schema::hasTable('transaksis')) {
            \DB::statement("
                UPDATE transaksis SET
                    kg_numeric = NULLIF(REPLACE(REPLACE(kg, ',', '.'), ' ', ''), '') + 0,
                    harga_numeric = NULLIF(REPLACE(REPLACE(harga, ',', '.'), ' ', ''), '') + 0,
                    disc_numeric = NULLIF(REPLACE(REPLACE(COALESCE(disc,''), ',', '.'), ' ', ''), '') + 0,
                    harga_akhir_numeric = NULLIF(REPLACE(REPLACE(COALESCE(harga_akhir,''), ',', '.'), ' ', ''), '') + 0
                WHERE kg REGEXP '^[0-9.,]+' OR harga REGEXP '^[0-9.,]+'
            ");
        }
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropIndex(['tanggal_masuk']);
            $table->dropIndex(['status_order']);
            $table->dropIndex(['status_payment']);
            $table->dropColumn([
                'kg_numeric', 'harga_numeric', 'disc_numeric',
                'harga_akhir_numeric', 'total_numeric',
                'tanggal_masuk', 'tanggal_ambil',
            ]);
        });

        Schema::table('hargas', function (Blueprint $table) {
            $table->dropColumn(['kg_numeric', 'harga_numeric']);
        });

        foreach (['users', 'transaksis', 'hargas', 'laundry_settings', 'notifications_settings'] as $nama) {
            if (Schema::hasColumn($nama, 'cabang_id')) {
                Schema::table($nama, function (Blueprint $table) {
                    $table->dropForeign(['cabang_id']);
                    $table->dropIndex(['cabang_id']);
                    $table->dropColumn('cabang_id');
                });
            }
        }
    }
};
