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
        // GUARD: MODIFY COLUMN ... ENUM hanya sintaks MySQL/MariaDB.
        // Di SQLite (DB test) kolom enum direpresentasikan sebagai varchar,
        // sehingga tidak ada constraint yang perlu diubah.
        if (! in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
            return;
        }
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
