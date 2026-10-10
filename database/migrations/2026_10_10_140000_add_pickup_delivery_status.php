<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * F3 — Tambah status 'Dijemput' & 'Diantar' pada enum status_order.
 *
 * Alur lama (reguler): Process → Done → Delivery
 * Alur pickup         : Dijemput → Process → Done → Diantar
 * Alur dropoff        : Process → Done → Diantar
 *
 * Non-destruktif: hanya MENAMBAH nilai enum, baris lama tidak tersentuh.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE transaksis MODIFY COLUMN status_order
            ENUM('Dijemput','Process','Done','Diantar','Delivery','Batal')
            NOT NULL DEFAULT 'Process'");
    }

    public function down(): void
    {
        // Kembalikan baris yang memakai status baru ke nilai lama yang setara,
        // supaya penyempitan enum tidak ditolak MySQL.
        DB::table('transaksis')->where('status_order', 'Dijemput')->update(['status_order' => 'Process']);
        DB::table('transaksis')->where('status_order', 'Diantar')->update(['status_order' => 'Delivery']);

        DB::statement("ALTER TABLE transaksis MODIFY COLUMN status_order
            ENUM('Process','Done','Delivery','Batal') NOT NULL DEFAULT 'Process'");
    }
};
