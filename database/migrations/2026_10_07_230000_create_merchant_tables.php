<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TAHAP 1 — Fondasi tingkatan Merchant.
 *
 * Menambah satu tingkat hierarki di atas `cabang`:
 *
 *   merchant (pemilik bisnis, pemegang langganan)
 *      └── cabang  (outlet fisik, tenant isolasi data)
 *
 * Bersifat NON-DESTRUKTIF:
 *   - tabel baru `merchant`
 *   - kolom baru `cabang.merchant_id` (NULLABLE → data lama tetap valid)
 *   - kolom baru `users.merchant_id` (NULLABLE → penanda owner di level merchant)
 *   - tidak ada kolom/tabel yang dihapus
 */
return new class extends Migration
{
    public function up(): void
    {
        // ---------- 1. Tabel merchant ----------
        Schema::create('merchant', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();          // LBR, BRS, dst
            $table->string('nama');                        // "Laundry Bersih Group"
            $table->string('slug')->nullable()->unique();  // untuk URL bila perlu
            $table->unsignedBigInteger('pemilik_id')->nullable(); // owner (users.id)
            $table->unsignedBigInteger('paket_id')->nullable();   // langganan level merchant
            $table->string('email')->nullable();
            $table->string('no_telp', 30)->nullable();
            $table->string('alamat')->nullable();
            $table->string('logo')->nullable();
            $table->enum('status', ['aktif', 'nonaktif', 'suspended'])->default('aktif');
            $table->text('catatan')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('pemilik_id');
            $table->index('paket_id');
            $table->index('status');

            $table->foreign('pemilik_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('paket_id')->references('id')->on('paket')->nullOnDelete();
        });

        // ---------- 2. cabang.merchant_id ----------
        Schema::table('cabang', function (Blueprint $table) {
            $table->unsignedBigInteger('merchant_id')->nullable()->after('id');
            $table->index('merchant_id');
            $table->foreign('merchant_id')->references('id')->on('merchant')->nullOnDelete();
        });

        // ---------- 3. users.merchant_id (penanda owner/afiliasi merchant) ----------
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('merchant_id')->nullable()->after('cabang_id');
            $table->index('merchant_id');
            $table->foreign('merchant_id')->references('id')->on('merchant')->nullOnDelete();
        });

        // ---------- 4. Tabel pivot: user akses banyak cabang ----------
        // Owner merchant perlu akses >1 cabang; tabel ini menyatakan cabang
        // mana saja yang boleh diakses seorang user.
        Schema::create('cabang_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('cabang_id');
            $table->enum('peran_di_cabang', ['owner', 'admin', 'karyawan'])->default('admin');
            $table->timestamps();

            $table->unique(['user_id', 'cabang_id']);
            $table->index('cabang_id');

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('cabang_id')->references('id')->on('cabang')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cabang_user');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['merchant_id']);
            $table->dropIndex(['merchant_id']);
            $table->dropColumn('merchant_id');
        });

        Schema::table('cabang', function (Blueprint $table) {
            $table->dropForeign(['merchant_id']);
            $table->dropIndex(['merchant_id']);
            $table->dropColumn('merchant_id');
        });

        Schema::dropIfExists('merchant');
    }
};
