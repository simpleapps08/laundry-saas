<?php

namespace Database\Seeders;

use App\Models\Cabang;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Seeder Cabang — membuat cabang contoh + akun admin tiap cabang.
 *
 * Jalankan: php artisan db:seed --class=CabangSeeder
 *
 * CATATAN: seeder semula (DatabaseSeeder) hanya membuat role Customer dan
 * Karyawan — role Admin TIDAK pernah dibuat, sehingga user dengan
 * auth='Admin' tetap kena 403. Seeder ini menambal itu.
 */
class CabangSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ketiga role ada
        foreach (['Admin', 'Karyawan', 'Customer'] as $nama) {
            Role::firstOrCreate(['name' => $nama]);
        }

        $cabang = Cabang::firstOrCreate(
            ['kode' => 'TBN-01'],
            [
                'nama' => 'Cabang Pusat',
                'alamat' => 'Jl. Pahlawan, Tuban',
                'no_telp' => '08123456789',
                'status' => 'aktif',
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@laundry.com'],
            [
                'cabang_id' => $cabang->id,
                'name' => 'Admin Laundry',
                'password' => Hash::make('123456'),
                'auth' => 'Admin',
                'status' => 'Active',
            ]
        );

        // Isi cabang_id kalau user lama belum punya
        if ($admin->cabang_id === null) {
            $admin->update(['cabang_id' => $cabang->id]);
        }

        if (! $admin->hasRole('Admin')) {
            $admin->assignRole('Admin');
        }

        $cabang->update(['pemilik_id' => $admin->id]);

        $this->command->info("Cabang: {$cabang->nama} ({$cabang->kode})");
        $this->command->info("Admin : {$admin->email}");
    }
}
