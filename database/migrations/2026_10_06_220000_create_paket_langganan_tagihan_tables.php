<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FASE 3 — Lapisan SaaS: paket & langganan.
 *
 * `paket`      = definisi produk berlangganan (harga, batas, fitur).
 * `langganan`  = satu baris per cabang yang berlangganan.
 * `tagihan`    = riwayat tagihan/pembayaran langganan.
 *
 * Catatan desain:
 *  - Fitur disimpan sebagai JSON (`fitur`) supaya paket baru bisa dibuat
 *    tanpa migration. Kunci fitur dicek lewat FiturPaket::punya().
 *  - `batas_cabang`/`batas_user` = -1 berarti tanpa batas.
 *  - `langganan` sengaja TIDAK memakai soft delete: riwayat langganan
 *    harus tetap terlihat untuk keperluan audit pendapatan.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── PAKET ───────────────────────────────────────────────────────
        Schema::create('paket', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();     // basic, pro, enterprise
            $table->string('nama');
            $table->text('deskripsi')->nullable();

            $table->decimal('harga_bulanan', 12, 2)->default(0);
            $table->decimal('harga_tahunan', 12, 2)->default(0);

            // Batas penggunaan; -1 = tanpa batas
            $table->integer('batas_cabang')->default(1);
            $table->integer('batas_user')->default(3);
            $table->integer('batas_transaksi_bulanan')->default(-1);

            // Daftar fitur: {"telegram":true,"whatsapp":false,"excel":true,...}
            $table->json('fitur')->nullable();

            $table->boolean('is_aktif')->default(true);
            $table->integer('urutan')->default(0);   // untuk sorting tampilan

            $table->timestamps();
            $table->softDeletes();
        });

        // ── LANGGANAN ───────────────────────────────────────────────────
        Schema::create('langganan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cabang_id');
            $table->unsignedBigInteger('paket_id');

            $table->enum('siklus', ['bulanan', 'tahunan'])->default('bulanan');
            $table->enum('status', ['trial', 'aktif', 'jatuh_tempo', 'berhenti'])
                  ->default('trial');

            $table->date('mulai');
            $table->date('berakhir');
            $table->date('trial_berakhir')->nullable();

            // Harga yang disepakati (untuk diskon/negosiasi per klien)
            $table->decimal('harga_disepakati', 12, 2)->nullable();

            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->foreign('cabang_id')->references('id')->on('cabang')
                  ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('paket_id')->references('id')->on('paket')
                  ->restrictOnDelete()->cascadeOnUpdate();

            $table->index(['cabang_id', 'status']);
            $table->index('berakhir');
        });

        // ── TAGIHAN ─────────────────────────────────────────────────────
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('langganan_id');
            $table->unsignedBigInteger('cabang_id');

            $table->string('nomor', 40)->unique();
            $table->decimal('jumlah', 12, 2);
            $table->enum('status', ['belum_bayar', 'menunggu_verifikasi', 'lunas', 'batal'])
                  ->default('belum_bayar');

            $table->date('jatuh_tempo');
            $table->date('periode_mulai');
            $table->date('periode_akhir');

            $table->timestamp('dibayar_pada')->nullable();
            $table->string('metode_bayar', 30)->nullable();
            $table->string('bukti_bayar')->nullable();   // path file
            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->foreign('langganan_id')->references('id')->on('langganan')
                  ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('cabang_id')->references('id')->on('cabang')
                  ->cascadeOnDelete()->cascadeOnUpdate();

            $table->index(['cabang_id', 'status']);
            $table->index('jatuh_tempo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan');
        Schema::dropIfExists('langganan');
        Schema::dropIfExists('paket');
    }
};
