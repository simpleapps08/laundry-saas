<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateFieldAuthInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Lewati pada driver non-MySQL (mis. SQLite in-memory saat test):
        // `ALTER TABLE ... CHANGE ... ENUM` hanya valid di MySQL. Kolom `auth`
        // sudah dibuat sebagai string di migration awal, cukup untuk test.
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE `users` CHANGE `auth` `auth` ENUM('Admin','Karyawan','Customer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
