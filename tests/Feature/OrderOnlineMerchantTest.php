<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Merchant;
use App\Models\PesananOnline;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Q3=C — Order online per-merchant / per-cabang.
 *
 * Melindungi 3 hal yang paling rawan rusak:
 *  1. Isolasi cabang: form merchant hanya boleh menampilkan cabang miliknya.
 *  2. Keamanan lintas-merchant: POST cabang milik merchant lain harus ditolak.
 *  3. Route wildcard /{slug} TIDAK boleh menabrak route statis (customer,
 *     finance, dokumentasi) — bug nyata yang pernah terjadi (404).
 */
class OrderOnlineMerchantTest extends TestCase
{
    use DatabaseTransactions;   // BUKAN RefreshDatabase: tabel role/permission
                                // di-seed sekali & dipakai test lain -> jangan dikosongkan.

    private function buatMerchant(string $nama, string $slug, array $cabang = []): Merchant
    {
        $m = Merchant::create([
            'kode'   => 'M-'.strtoupper(substr(md5($slug), 0, 6)),
            'nama'   => $nama,
            'slug'   => $slug,
            'status' => 'aktif',
        ]);

        foreach ($cabang as $i => $c) {
            Cabang::create([
                'merchant_id' => $m->id,
                'kode'        => $c['kode'],
                'nama'        => $c['nama'],
                'alamat'      => $c['alamat'] ?? null,
                'status'      => 'aktif',
            ]);
        }

        return $m;
    }

    public function test_form_merchant_hanya_menampilkan_cabang_miliknya(): void
    {
        $a = $this->buatMerchant('Laundry A', 'laundry-a', [
            ['kode' => 'A-01', 'nama' => 'Cabang A Satu'],
            ['kode' => 'A-02', 'nama' => 'Cabang A Dua'],
        ]);
        $this->buatMerchant('Laundry B', 'laundry-b', [
            ['kode' => 'B-01', 'nama' => 'Cabang B Satu'],
        ]);

        $resp = $this->get('/laundry-a/pesan');
        $resp->assertOk();
        $resp->assertSee('Cabang A Satu');
        $resp->assertSee('Cabang A Dua');
        $resp->assertDontSee('Cabang B Satu');
    }

    public function test_form_cabang_kunci_ke_satu_cabang_tanpa_dropdown(): void
    {
        $this->buatMerchant('Laundry A', 'laundry-a', [
            ['kode' => 'A-01', 'nama' => 'Cabang A Satu'],
            ['kode' => 'A-02', 'nama' => 'Cabang A Dua'],
        ]);

        $resp = $this->get('/laundry-a/A-01/pesan');
        $resp->assertOk();
        $resp->assertSee('Cabang A Satu');
        $resp->assertDontSee('Cabang A Dua');          // tidak ada opsi cabang lain
        $resp->assertSee('oo-cabang-fixed', false);    // pakai panel cabang fix
    }

    public function test_kode_cabang_case_insensitive(): void
    {
        $this->buatMerchant('Laundry A', 'laundry-a', [
            ['kode' => 'A-01', 'nama' => 'Cabang A Satu'],
        ]);

        $this->get('/laundry-a/a-01/pesan')->assertOk();
        $this->get('/laundry-a/A-01/pesan')->assertOk();
    }

    public function test_merchant_tidak_ada_atau_nonaktif_404(): void
    {
        $this->get('/tidak-ada-slug/pesan')->assertNotFound();

        $this->buatMerchant('Laundry Off', 'laundry-off', [
            ['kode' => 'O-01', 'nama' => 'Cabang Off'],
        ]);
        Merchant::where('slug', 'laundry-off')->update(['status' => 'nonaktif']);

        $this->get('/laundry-off/pesan')->assertNotFound();
    }

    public function test_post_cabang_lintas_merchant_ditolak(): void
    {
        $a = $this->buatMerchant('Laundry A', 'laundry-a', [
            ['kode' => 'A-01', 'nama' => 'Cabang A Satu'],
        ]);
        $b = $this->buatMerchant('Laundry B', 'laundry-b', [
            ['kode' => 'B-01', 'nama' => 'Cabang B Satu'],
        ]);

        $cabangB = Cabang::where('merchant_id', $b->id)->first();

        // Kirim cabang milik B ke endpoint merchant A -> harus gagal validasi.
        $resp = $this->post('/laundry-a/pesan', [
            'nama'          => 'Penyusup',
            'no_telp'       => '08123456789',
            'alamat'        => 'Jl Test',
            'cabang_id'     => $cabangB->id,
            'mode_layanan'  => 'pickup',
        ]);

        $resp->assertSessionHasErrors('cabang_id');
        $this->assertDatabaseMissing('pesanan_online', ['nama' => 'Penyusup']);
    }

    public function test_post_cabang_dipaksa_ke_cabang_di_url(): void
    {
        $a = $this->buatMerchant('Laundry A', 'laundry-a', [
            ['kode' => 'A-01', 'nama' => 'Cabang A Satu'],
            ['kode' => 'A-02', 'nama' => 'Cabang A Dua'],
        ]);
        $cabangSatu = Cabang::where('kode', 'A-01')->first();
        $cabangDua  = Cabang::where('kode', 'A-02')->first();

        // POST ke URL A-01 tapi kirim cabang_id A-02 -> harus dipaksa ke A-01.
        $this->post('/laundry-a/A-01/pesan', [
            'nama'          => 'Pelanggan Paksa',
            'no_telp'       => '08123456789',
            'alamat'        => 'Jl Test',
            'cabang_id'     => $cabangDua->id,
            'mode_layanan'  => 'pickup',
        ]);

        $this->assertDatabaseHas('pesanan_online', [
            'nama'      => 'Pelanggan Paksa',
            'cabang_id' => $cabangSatu->id,
        ]);
        $this->assertDatabaseMissing('pesanan_online', [
            'nama'      => 'Pelanggan Paksa',
            'cabang_id' => $cabangDua->id,
        ]);
    }

    /**
     * ANTI-REGRESI: route wildcard /{slug} TIDAK boleh menelan route statis.
     * Bug nyata: /{slug} didaftarkan sebelum /customer dkk -> semua 404.
     */
    public function test_route_statis_tidak_ditelan_slug(): void
    {
        // Route-route ini terdaftar di routes/web.php dan HARUS tetap ada.
        // Kalau /{slug} menang, mereka hilang dari daftar route.
        $daftarRoute = collect(app('router')->getRoutes()->getRoutes())
            ->map(fn ($r) => $r->uri())
            ->all();

        foreach (['customer', 'finance', 'dokumentasi', 'admin', 'login', 'daftar'] as $statis) {
            $this->assertContains(
                $statis,
                $daftarRoute,
                "Route statis '{$statis}' hilang — kemungkinan ditelan wildcard /{slug}."
            );
        }
    }

    public function test_profil_merchant_menampilkan_daftar_cabang(): void
    {
        $this->buatMerchant('Laundry A', 'laundry-a', [
            ['kode' => 'A-01', 'nama' => 'Cabang A Satu'],
            ['kode' => 'A-02', 'nama' => 'Cabang A Dua'],
        ]);

        $resp = $this->get('/laundry-a');
        $resp->assertOk();
        $resp->assertSee('Cabang A Satu');
        $resp->assertSee('Cabang A Dua');
    }
}
