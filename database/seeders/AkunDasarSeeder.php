<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder akun demo/dasar Laundry SaaS.
 *
 * Idempotent: aman dijalankan berulang (updateOrCreate berdasarkan email).
 *
 * Termasuk perbaikan: super-admin (id=8) pernah kehilangan email karena
 * pengujian edit profile. Seeder ini mengembalikan nilainya.
 */
class AkunDasarSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            [
                'email'      => 'superadmin@laundry.com',
                'name'       => 'Super Admin',
                'auth'       => 'Admin',
                'role'       => 'SuperAdmin',
                'cabang_id'  => null,
                'nama_cabang' => null,
            ],
            [
                'email'      => 'admin.tbn@laundry.com',
                'name'       => 'Admin Tuban',
                'auth'       => 'Admin',
                'role'       => 'Admin',
                'cabang_id'  => 1,
                'nama_cabang' => 'Cabang Tuban',
            ],
            [
                'email'      => 'admin.sby@laundry.com',
                'name'       => 'Admin Surabaya',
                'auth'       => 'Admin',
                'role'       => 'Admin',
                'cabang_id'  => 2,
                'nama_cabang' => 'Cabang Surabaya',
            ],
            [
                'email'      => 'kar.tbn@laundry.com',
                'name'       => 'Karyawan Tuban',
                'auth'       => 'Karyawan',
                'role'       => 'Karyawan',
                'cabang_id'  => 1,
                'nama_cabang' => 'Cabang Tuban',
            ],
            [
                'email'      => 'kar.sby@laundry.com',
                'name'       => 'Karyawan Surabaya',
                'auth'       => 'Karyawan',
                'role'       => 'Karyawan',
                'cabang_id'  => 2,
                'nama_cabang' => 'Cabang Surabaya',
            ],
        ];

        foreach ($akun as $a) {
            $user = User::where('email', $a['email'])->first();

            if (! $user) {
                // Super-admin historis (id=8) bisa kehilangan email — cari lewat role.
                if ($a['role'] === 'SuperAdmin') {
                    $user = User::whereNull('email')
                        ->orWhere('email', '')
                        ->whereHas('roles', fn ($q) => $q->where('name', 'SuperAdmin'))
                        ->first();
                }
            }

            if (! $user) {
                $user = new User();
                $user->password = Hash::make('123456');
            }

            $user->fill([
                'name'        => $a['name'],
                'email'       => $a['email'],
                'auth'        => $a['auth'],
                'cabang_id'   => $a['cabang_id'],
                'nama_cabang' => $a['nama_cabang'],
                'status'      => 'Active',
            ]);

            // Password hanya di-set bila akun baru atau password kosong,
            // supaya seeder tidak menimpa password yang sudah diganti admin.
            if (! $user->exists || empty($user->password)) {
                $user->password = Hash::make('123456');
            }

            $user->save();

            if (! $user->hasRole($a['role'])) {
                $user->assignRole($a['role']);
            }

            $this->command->info("OK: {$a['email']} (id={$user->id}, role={$a['role']})");
        }
    }
}
