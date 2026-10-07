<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Setelan awal diskon bertingkat (Tahap 4).
 * Idempotent: updateOrInsert per min_cabang.
 */
class DiskonLanggananSeeder extends Seeder
{
    public function run(): void
    {
        $baris = [
            ['min_cabang' => 1, 'diskon_persen' => 0.00,  'label' => 'Tanpa diskon',  'urutan' => 1],
            ['min_cabang' => 2, 'diskon_persen' => 10.00, 'label' => 'Mulai berkembang', 'urutan' => 2],
            ['min_cabang' => 3, 'diskon_persen' => 20.00, 'label' => 'Jaringan kecil', 'urutan' => 3],
            ['min_cabang' => 4, 'diskon_persen' => 25.00, 'label' => 'Jaringan menengah', 'urutan' => 4],
            ['min_cabang' => 5, 'diskon_persen' => 30.00, 'label' => 'Jaringan besar', 'urutan' => 5],
        ];

        foreach ($baris as $b) {
            DB::table('diskon_langganan')->updateOrInsert(
                ['min_cabang' => $b['min_cabang']],
                $b + ['is_aktif' => true, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
