<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FASE 1 — Fondasi Data
 *
 * Membuat tabel `cabang` sebagai entitas penyewa (tenant) dan menambahkan
 * kolom `cabang_id` ke tabel-tabel yang datanya harus terisolasi per cabang.
 *
 * Alasan: sebelumnya "multi-toko" diimplementasikan sebagai 1 cabang = 1 akun
 * Admin, dan isolasi data memakai  user_id. Akibatnya tidak ada identitas
 * cabang pada data, sehingga laporan lintas cabang tidak mungkin dan fondasi
 * multi-tenant tidak ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cabang', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();        // kode unik cabang, mis. TBN-01
            $table->string('nama');
            $table->string('alamat')->nullable();
            $table->string('no_telp', 30)->nullable();
            $table->string('email')->nullable();

            // Pemilik / penanggung jawab cabang (akun Admin)
            $table->unsignedBigInteger('pemilik_id')->nullable();

            // Status operasional cabang
            $table->enum('status', ['aktif', 'nonaktif', 'suspended'])->default('aktif');

            // Pengaturan cetak/invoice per cabang
            $table->string('logo')->nullable();
            $table->text('catatan_kaki')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('pemilik_id')
                  ->references('id')->on('users')
                  ->nullOnDelete()->cascadeOnUpdate();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cabang');
    }
};
