<?php

namespace App\Console\Commands;

use App\Models\Langganan;
use App\Models\Merchant;
use App\Models\Tagihan;
use Illuminate\Console\Command;

/**
 * TagihLanggananBulanan — generate tagihan bulanan otomatis untuk semua
 * merchant dengan langganan aktif, memakai harga bertingkat (diskon volume).
 *
 * Pakai:
 *   php artisan langganan:tagih              → generate untuk semua merchant
 *   php artisan langganan:tagih --merchant=3 → hanya merchant id 3
 *   php artisan langganan:tagih --dry-run    → tampilkan saja, tidak simpan
 *
 * Jadwalkan di routes/console.php (Laravel 11+) atau Kernel:
 *   Schedule::command('langganan:tagih')->monthlyOn(1, '02:00');
 *
 * AMAN dijalankan berulang: lewati langganan yang sudah punya tagihan
 * belum lunas (biar tidak menagih dobel).
 */
class TagihLanggananBulanan extends Command
{
    protected $signature = 'langganan:tagih
                            {--merchant= : Batasi ke satu merchant id}
                            {--dry-run : Tampilkan saja, tidak menyimpan}';

    protected $description = 'Generate tagihan bulanan langganan (harga bertingkat + diskon volume)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $merchantId = $this->option('merchant');

        $query = Merchant::query()->where('status', 'aktif');
        if ($merchantId) {
            $query->where('id', $merchantId);
        }

        $merchants = $query->get();

        if ($merchants->isEmpty()) {
            $this->warn('Tidak ada merchant aktif.');
            return self::SUCCESS;
        }

        $totalTagihan = 0;
        $totalNilai = 0.0;
        $dilewati = 0;

        foreach ($merchants as $m) {
            $rincian = $m->rincianTagihan();

            if (empty($rincian['baris'])) {
                continue;
            }

            $this->line("");
            $this->info("Merchant: {$m->nama} ({$m->kode}) — {$rincian['jumlah_cabang']} cabang, "
                . "diskon {$rincian['diskon_persen']}%");

            foreach ($rincian['baris'] as $b) {
                // Lewati kalau masih ada tagihan belum lunas untuk langganan ini
                $adaTunggakan = Tagihan::where('langganan_id', $b['langganan_id'])
                    ->whereIn('status', ['belum_bayar', 'menunggu_verifikasi'])
                    ->exists();

                if ($adaTunggakan) {
                    $this->warn("  ⊘ {$b['cabang']} — dilewati (masih ada tagihan belum lunas)");
                    $dilewati++;
                    continue;
                }

                if ($dryRun) {
                    $this->line("  → {$b['cabang']} | Rp "
                        . number_format($b['total'], 0, ',', '.'));
                    $totalTagihan++;
                    $totalNilai += (float) $b['total'];
                    continue;
                }

                $l = Langganan::find($b['langganan_id']);
                $akhir = $l->siklus === 'tahunan'
                    ? now()->addYear()->toDateString()
                    : now()->addMonth()->toDateString();

                Tagihan::create([
                    'langganan_id' => $l->id,
                    'cabang_id' => $l->cabang_id,
                    'merchant_id' => $m->id,
                    'nomor' => Tagihan::buatNomor(),
                    'jumlah' => $b['total'],
                    'status' => 'belum_bayar',
                    'jatuh_tempo' => now()->addDays(7)->toDateString(),
                    'periode_mulai' => now()->toDateString(),
                    'periode_akhir' => $akhir,
                    'catatan' => "Tagihan otomatis. Diskon {$rincian['diskon_persen']}% "
                        . "({$rincian['jumlah_cabang']} cabang).",
                ]);

                $this->line("  ✓ {$b['cabang']} — Rp "
                    . number_format($b['total'], 0, ',', '.'));
                $totalTagihan++;
                $totalNilai += (float) $b['total'];
            }
        }

        $this->line("");
        $this->info(($dryRun ? '[DRY-RUN] ' : '') . "Selesai: {$totalTagihan} tagihan, total Rp "
            . number_format($totalNilai, 0, ',', '.')
            . ($dilewati > 0 ? " ({$dilewati} dilewati karena menunggak)" : ''));

        return self::SUCCESS;
    }
}
