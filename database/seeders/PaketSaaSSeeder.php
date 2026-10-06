<?php

namespace Database\Seeders;

use App\Models\Paket;
use Illuminate\Database\Seeder;

/**
 * Seeder PaketSaaS — definisi paket berlangganan.
 *
 * ⚠️ HARGA DI BAWAH ADALAH PLACEHOLDER. Ganti sesuai keputusan bisnis Boz.
 *    Fitur yang tersedia: laundry_dasar, telegram, whatsapp, excel,
 *    invoice_pdf, multi_cabang, laporan_lintas_cabang, api, prioritas_support
 */
class PaketSaaSSeeder extends Seeder
{
    public function run(): void
    {
        $paket = [
            [
                'kode' => 'basic',
                'nama' => 'Basic',
                'deskripsi' => 'Untuk laundry satu cabang yang baru mulai digitalisasi.',
                'harga_bulanan' => 150000,
                'harga_tahunan' => 1500000,
                'batas_cabang' => 1,
                'batas_user' => 3,
                'batas_transaksi_bulanan' => 500,
                'urutan' => 1,
                'fitur' => [
                    'laundry_dasar' => true,
                    'telegram' => false,
                    'whatsapp' => false,
                    'excel' => true,
                    'invoice_pdf' => true,
                    'multi_cabang' => false,
                    'laporan_lintas_cabang' => false,
                    'api' => false,
                    'prioritas_support' => false,
                ],
            ],
            [
                'kode' => 'pro',
                'nama' => 'Pro',
                'deskripsi' => 'Untuk laundry berkembang: notifikasi pelanggan & beberapa cabang.',
                'harga_bulanan' => 350000,
                'harga_tahunan' => 3500000,
                'batas_cabang' => 3,
                'batas_user' => 15,
                'batas_transaksi_bulanan' => -1,
                'urutan' => 2,
                'fitur' => [
                    'laundry_dasar' => true,
                    'telegram' => true,
                    'whatsapp' => true,
                    'excel' => true,
                    'invoice_pdf' => true,
                    'multi_cabang' => true,
                    'laporan_lintas_cabang' => true,
                    'api' => false,
                    'prioritas_support' => false,
                ],
            ],
            [
                'kode' => 'enterprise',
                'nama' => 'Enterprise',
                'deskripsi' => 'Untuk jaringan laundry: cabang & user tanpa batas, integrasi API.',
                'harga_bulanan' => 750000,
                'harga_tahunan' => 7500000,
                'batas_cabang' => -1,
                'batas_user' => -1,
                'batas_transaksi_bulanan' => -1,
                'urutan' => 3,
                'fitur' => [
                    'laundry_dasar' => true,
                    'telegram' => true,
                    'whatsapp' => true,
                    'excel' => true,
                    'invoice_pdf' => true,
                    'multi_cabang' => true,
                    'laporan_lintas_cabang' => true,
                    'api' => true,
                    'prioritas_support' => true,
                ],
            ],
        ];

        foreach ($paket as $p) {
            Paket::updateOrCreate(['kode' => $p['kode']], $p);
            $this->command->info("Paket: {$p['nama']} — Rp " . number_format($p['harga_bulanan'], 0, ',', '.') . "/bln");
        }
    }
}
