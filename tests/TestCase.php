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

        // Reset rate limiter antar-test. Tanpa ini, semua request test
        // (1 proses PHP) terakumulasi dalam satu hitungan throttle:20,60
        // -> test ke-21+ kena HTTP 429 (bukan kegagalan logika test).
        // Di produksi limiter per-IP per-menit; di test kita mulai dari nol.
        try {
            $cache = $this->app['cache']->store();
            if (method_exists($cache, 'flush')) {
                $cache->flush();
            }
        } catch (\Throwable $e) {
            // abaikan bila cache tidak tersedia
        }

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
