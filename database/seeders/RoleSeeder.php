<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Buat role dasar aplikasi. IDEMPOTEN (firstOrCreate) supaya aman
     * dijalankan berulang — sebelumnya pakai Role::create() yang melempar
     * error kalau role sudah ada, dan hanya membuat 'Karyawan' (tanpa 'Admin')
     * sehingga test yang memanggil assignRole('Admin') gagal RoleDoesNotExist.
     *
     * @return void
     */
    public function run()
    {
        foreach (['Admin', 'Karyawan', 'Customer', 'SuperAdmin'] as $nama) {
            Role::firstOrCreate(
                ['name' => $nama, 'guard_name' => 'web'],
            );
        }
    }
}
