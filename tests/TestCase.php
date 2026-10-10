<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Pastikan role dasar (Admin/Karyawan/Customer/...) ada sebelum tiap test.
     *
     * Alasan: sebagian test (IsolasiCabangTest, SetelanPerCabangTest) memanggil
     * `assignRole('Admin')` tapi tidak menjalankan RoleSeeder sendiri. Kalau tabel
     * `roles` kosong (DB dev/test belum pernah di-seed), test gagal dengan
     * RoleDoesNotExist. Menjalankan seeder di sini membuat test mandiri (tidak
     * bergantung state DB) dan aman diulang (seeder pakai firstOrCreate).
     */
    protected function setUp(): void
    {
        parent::setUp();

        try {
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\RoleSeeder',
                '--force' => true,
            ]);
        } catch (\Throwable $e) {
            // Kalau seeder tidak ada / DB belum dimigrasi, jangan gagalkan setUp —
            // biarkan test melaporkan error aslinya.
        }
    }
}
