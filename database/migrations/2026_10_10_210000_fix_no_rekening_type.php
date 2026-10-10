<?php
// Migration: no_rekening dari INT -> VARCHAR (nomor rekening/HP bisa panjang)
// Non-destruktif: memperluas tipe, data lama tetap terbaca.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_banks', function (Blueprint $table) {
            $table->string('no_rekening', 40)->change();
        });
    }

    public function down(): void
    {
        Schema::table('data_banks', function (Blueprint $table) {
            $table->integer('no_rekening')->change();
        });
    }
};
