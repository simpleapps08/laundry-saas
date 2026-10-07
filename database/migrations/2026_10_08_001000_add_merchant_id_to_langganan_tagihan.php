<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tahap 4 — Langganan & tagihan naik ke level Merchant.
 *
 * Prinsip:
 *  - NON-destruktif: tidak ada kolom/tabel yang dihapus.
 *  - Semua kolom baru NULLABLE / punya default supaya data lama aman.
 *  - `cabang_id` TETAP ada di langganan (kompatibilitas + basis diskon per cabang).
 *
 * Perubahan:
 *  - langganan.merchant_id   → penanda pemilik (dari cabang.merchant_id)
 *  - langganan.jumlah_cabang → basis perhitungan diskon bertingkat
 *  - tagihan.merchant_id     → ikut langganan, memudahkan rekap tagihan per merchant
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('langganan', function (Blueprint $table) {
            if (! Schema::hasColumn('langganan', 'merchant_id')) {
                $table->foreignId('merchant_id')
                    ->nullable()
                    ->after('cabang_id')
                    ->constrained('merchant')
                    ->nullOnDelete();

                $table->index('merchant_id');
            }

            if (! Schema::hasColumn('langganan', 'jumlah_cabang')) {
                $table->unsignedInteger('jumlah_cabang')
                    ->default(1)
                    ->after('paket_id')
                    ->comment('Basis diskon bertingkat: berapa cabang ditagih sekaligus');
            }
        });

        Schema::table('tagihan', function (Blueprint $table) {
            if (! Schema::hasColumn('tagihan', 'merchant_id')) {
                $table->foreignId('merchant_id')
                    ->nullable()
                    ->after('cabang_id')
                    ->constrained('merchant')
                    ->nullOnDelete();

                $table->index('merchant_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('langganan', function (Blueprint $table) {
            if (Schema::hasColumn('langganan', 'merchant_id')) {
                $table->dropForeign(['merchant_id']);
                $table->dropIndex(['merchant_id']);
                $table->dropColumn('merchant_id');
            }
            if (Schema::hasColumn('langganan', 'jumlah_cabang')) {
                $table->dropColumn('jumlah_cabang');
            }
        });

        Schema::table('tagihan', function (Blueprint $table) {
            if (Schema::hasColumn('tagihan', 'merchant_id')) {
                $table->dropForeign(['merchant_id']);
                $table->dropIndex(['merchant_id']);
                $table->dropColumn('merchant_id');
            }
        });
    }
};
