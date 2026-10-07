<?php

namespace App\Services;

use App\Models\Cabang;
use App\Models\Langganan;
use App\Models\Merchant;
use App\Models\Paket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * OnboardingMerchant — buat merchant baru LENGKAP dalam satu transaksi.
 *
 * Tanpa service ini, super-admin harus: bikin user → bikin merchant →
 * bikin cabang → set cabang.merchant_id → bikin langganan. Lima langkah,
 * gampang setengah jalan (merchant tanpa cabang, atau owner tanpa merchant).
 *
 * Sekarang: satu panggilan → semuanya konsisten atau tidak sama sekali
 * (DB::transaction). Kalau ada langkah gagal, rollback total.
 */
class OnboardingMerchant
{
    /**
     * @param  array  $data  kunci: nama, email, password, no_telp?, alamat?,
     *                       paket_id, siklus, nama_cabang?, catatan?
     * @return array{merchant: Merchant, owner: User, cabang: Cabang, langganan: Langganan}
     */
    public static function buat(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $paket = Paket::findOrFail($data['paket_id']);
            $siklus = $data['siklus'] ?? 'bulanan';

            // ── 1. Kode merchant unik: M-XXXXX ─────────────────────────
            $kode = self::buatKodeMerchant($data['nama']);

            // ── 2. Merchant ────────────────────────────────────────────
            $merchant = Merchant::create([
                'kode' => $kode,
                'nama' => $data['nama'],
                'slug' => self::buatSlug($data['nama']),
                'paket_id' => $paket->id,
                'email' => $data['email'],
                'no_telp' => $data['no_telp'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'status' => 'aktif',
                'catatan' => $data['catatan'] ?? 'Dibuat via panel super-admin.',
            ]);

            // ── 3. Akun OWNER (cabang_id NULL, merchant_id diisi) ──────
            // PENTING: cabang_id = NULL membuat dia BUKAN admin cabang,
            // tapi owner merchant — lihat User::isSuperAdmin().
            $owner = User::create([
                'merchant_id' => $merchant->id,
                'cabang_id' => null,
                'name' => $data['owner_nama'] ?? $data['nama'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'auth' => 'Admin',
                'status' => 'Active',
                'no_telp' => $data['no_telp'] ?? null,
            ]);

            if (Role::where('name', 'Admin')->exists()) {
                $owner->assignRole('Admin');
            }

            $merchant->update(['pemilik_id' => $owner->id]);

            // ── 4. Cabang pertama ──────────────────────────────────────
            $cabang = Cabang::create([
                'merchant_id' => $merchant->id,
                'kode' => self::buatKodeCabang($kode),
                'nama' => $data['nama_cabang'] ?? $data['nama'] . ' - Cabang Utama',
                'email' => $data['email'],
                'no_telp' => $data['no_telp'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'pemilik_id' => $owner->id,
                'status' => 'aktif',
            ]);

            // Cabang pertama = domain default owner (biar UI-nya enak).
            $owner->update(['cabang_id' => $cabang->id]);

            // ── 5. Langganan pertama (sumber kebenaran billing) ───────
            $langganan = Langganan::create([
                'cabang_id' => $cabang->id,
                'merchant_id' => $merchant->id,
                'paket_id' => $paket->id,
                'jumlah_cabang' => 1,
                'siklus' => $siklus,
                'status' => 'trial',
                'mulai' => now()->toDateString(),
                'berakhir' => now()->addDays(14)->toDateString(),
                'trial_berakhir' => now()->addDays(14)->toDateString(),
                'catatan' => 'Trial 14 hari saat onboarding.',
            ]);

            FiturPaket::bersihkan($cabang->id);

            return compact('merchant', 'owner', 'cabang', 'langganan');
        });
    }

    /**
     * Tambah cabang baru ke merchant yang sudah ada.
     * Memeriksa kuota paket SEBELUM membuat.
     */
    public static function tambahCabang(Merchant $merchant, array $data): Cabang
    {
        if (! $merchant->bolehTambahCabang()) {
            throw new \RuntimeException(
                "Kuota cabang habis: paket {$merchant->paketEfektif()?->nama} "
                . "maksimal {$merchant->batasCabang()} cabang. Naikkan paket dulu."
            );
        }

        return DB::transaction(function () use ($merchant, $data) {
            $paket = $merchant->paketEfektif();

            $cabang = Cabang::create([
                'merchant_id' => $merchant->id,
                'kode' => self::buatKodeCabang($merchant->kode),
                'nama' => $data['nama'],
                'email' => $data['email'] ?? $merchant->email,
                'no_telp' => $data['no_telp'] ?? $merchant->no_telp,
                'alamat' => $data['alamat'] ?? null,
                'status' => 'aktif',
            ]);

            // Setiap cabang WAJIB punya langganan sendiri (model Opsi C:
            // harga per cabang). Pakai paket efektif merchant.
            $langganan = Langganan::create([
                'cabang_id' => $cabang->id,
                'merchant_id' => $merchant->id,
                'paket_id' => $paket->id,
                'jumlah_cabang' => 1,
                'siklus' => $data['siklus'] ?? 'bulanan',
                'status' => 'aktif',
                'mulai' => now()->toDateString(),
                'berakhir' => now()->addMonth()->toDateString(),
                'catatan' => 'Cabang tambahan (onboarding).',
            ]);

            // Segarkan jumlah_cabang semua langganan merchant (basis diskon).
            $jml = Langganan::where('merchant_id', $merchant->id)
                ->whereIn('status', ['trial', 'aktif'])
                ->count();
            Langganan::where('merchant_id', $merchant->id)->update(['jumlah_cabang' => $jml]);

            // Kalau paket punya fitur multi cabang, seluruh cabang ikut naik.
            FiturPaket::bersihkan($cabang->id);

            return $cabang;
        });
    }

    /* ── Helper kode & slug ────────────────────────────────────────────── */

    public static function buatKodeMerchant(string $nama): string
    {
        $dasar = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', Str::ascii($nama)));
        $dasar = substr($dasar ?: 'M', 0, 5);

        $kode = 'M-' . $dasar;
        $i = 1;
        while (Merchant::withTrashed()->where('kode', $kode)->exists()) {
            $kode = 'M-' . $dasar . $i;
            $i++;
        }

        return $kode;
    }

    public static function buatKodeCabang(string $kodeMerchant): string
    {
        $dasar = preg_replace('/^M-/', '', strtoupper($kodeMerchant));
        $kode = 'C-' . $dasar;
        $i = 1;
        while (Cabang::withTrashed()->where('kode', $kode)->exists()) {
            $kode = 'C-' . $dasar . $i;
            $i++;
        }

        return $kode;
    }

    public static function buatSlug(string $nama): string
    {
        $slug = Str::slug($nama);
        $slug = $slug !== '' ? $slug : 'merchant';
        $asli = $slug;
        $i = 1;
        while (Merchant::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $asli . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
