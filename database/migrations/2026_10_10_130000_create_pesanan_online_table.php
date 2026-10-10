<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FASE F2 — Kotak masuk order online (self-service, TANPA akun).
 *
 * PENTING: ini tabel STAGING, bukan sumber kebenaran transaksi.
 * Transaksi resmi tetap di `transaksis`. Tabel ini hanya menampung
 * permintaan pelanggan sebelum ditimbang (kg/harga belum ada).
 *
 * Alasan tidak langsung ke `transaksis`: 7 kolom di sana NOT NULL
 * (customer_id, user_id, harga_id, kg, harga, hari, status_payment)
 * yang BELUM bisa diisi saat pelanggan memesan. Memaksa masuk =
 * data palsu + sistem paralel.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pesanan_online')) {
            return;
        }

        Schema::create('pesanan_online', function (Blueprint $table) {
            $table->id();
            // cabang tujuan (dipilih pelanggan / default cabang pertama merchant)
            $table->unsignedBigInteger('cabang_id')->nullable();
            $table->unsignedBigInteger('merchant_id')->nullable();

            // identitas pelanggan (tanpa akun)
            $table->string('nama');
            $table->string('no_telp', 30);
            $table->text('alamat')->nullable();

            // detail permintaan
            $table->string('mode_layanan', 20)->default('pickup'); // pickup / dropoff
            $table->string('jenis_pakaian', 100)->nullable();       // preferensi (opsional)
            $table->string('estimasi_kg', 20)->nullable();          // perkiraan, bukan final
            $table->text('catatan')->nullable();

            // alur
            $table->string('status_online', 20)->default('Menunggu'); // Menunggu/Diproses/Dibatalkan
            $table->string('kode_pesanan', 40)->unique();             // kode cek status
            // kalau sudah diproses -> tautkan ke transaksi asli
            $table->unsignedBigInteger('transaksi_id')->nullable();
            $table->timestamp('diproses_at')->nullable();

            $table->timestamps();

            $table->index('cabang_id');
            $table->index('status_online');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan_online');
    }
};
