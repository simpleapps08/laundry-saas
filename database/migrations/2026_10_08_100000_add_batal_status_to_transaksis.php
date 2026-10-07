<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Tambah status 'Batal' pada enum status_order.
 *
 * Keputusan Boz: transaksi hanya boleh DITAMBAH + DIBATALKAN (tidak diedit).
 * Pembatalan menandai baris, bukan menghapusnya, supaya jejak audit dan
 * laporan keuangan tetap utuh.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE transaksis MODIFY COLUMN status_order ENUM('Process','Done','Delivery','Batal') NOT NULL DEFAULT 'Process'");
    }

    public function down(): void
    {
        // Baris berstatus Batal dikembalikan ke Process dulu, kalau tidak
        // MySQL akan menolak penyempitan enum.
        DB::table('transaksis')->where('status_order', 'Batal')->update(['status_order' => 'Process']);
        DB::statement("ALTER TABLE transaksis MODIFY COLUMN status_order ENUM('Process','Done','Delivery') NOT NULL DEFAULT 'Process'");
    }
};
