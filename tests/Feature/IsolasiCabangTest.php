<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Isolasi multi-tenant 2 tingkat: Karyawan (kasir) TERKUNCI ke cabangnya.
 *
 * Regresi yang dicegah: kasir cabang A melihat order cabang B karena
 * `cabangIds()` mengembalikan SELURUH cabang merchant, bukan hanya cabangnya.
 */
class IsolasiCabangTest extends TestCase
{
    use DatabaseTransactions;

    private function buatMerchantDuaCabang(): array
    {
        $suffix = uniqid();
        $merchant = Merchant::create([
            'kode' => 'MU-'.$suffix,
            'nama' => 'Merchant Uji Isolasi',
            'slug' => 'merchant-uji-'.$suffix,
            'status' => 'Aktif',
        ]);

        $cabangA = Cabang::create([
            'merchant_id' => $merchant->id,
            'kode' => 'CA-'.$suffix,
            'nama' => 'Cabang Uji A',
        ]);
        $cabangB = Cabang::create([
            'merchant_id' => $merchant->id,
            'kode' => 'CB-'.$suffix,
            'nama' => 'Cabang Uji B',
        ]);

        $owner = User::create([
            'name' => 'Owner Uji', 'email' => 'owner.uji.'.uniqid().'@t.local',
            'password' => bcrypt('123456'), 'status' => 'Aktif', 'auth' => 'Karyawan',
            'merchant_id' => $merchant->id, 'cabang_id' => null,
        ]);
        $owner->assignRole('Admin');

        $kasirA = User::create([
            'name' => 'Kasir A', 'email' => 'kasir.a.'.uniqid().'@t.local',
            'password' => bcrypt('123456'), 'status' => 'Aktif', 'auth' => 'Karyawan',
            'merchant_id' => $merchant->id, 'cabang_id' => $cabangA->id,
        ]);
        $kasirA->assignRole('Karyawan');

        $kasirB = User::create([
            'name' => 'Kasir B', 'email' => 'kasir.b.'.uniqid().'@t.local',
            'password' => bcrypt('123456'), 'status' => 'Aktif', 'auth' => 'Karyawan',
            'merchant_id' => $merchant->id, 'cabang_id' => $cabangB->id,
        ]);
        $kasirB->assignRole('Karyawan');

        return [$merchant, $cabangA, $cabangB, $owner, $kasirA, $kasirB];
    }

    public function test_karyawan_terkunci_ke_cabangnya_sendiri(): void
    {
        [, $cabangA, $cabangB, , $kasirA, $kasirB] = $this->buatMerchantDuaCabang();

        $this->assertSame([(int) $cabangA->id], array_map('intval', $kasirA->cabangIds()),
            'Kasir A harus terlihat HANYA cabang A.');
        $this->assertSame([(int) $cabangB->id], array_map('intval', $kasirB->cabangIds()),
            'Kasir B harus terlihat HANYA cabang B.');
        $this->assertFalse($kasirA->multiCabang(), 'Kasir bukan multi-cabang.');
        $this->assertFalse($kasirA->bolehAksesCabang($cabangB->id),
            'Kasir A TIDAK boleh mengakses cabang B.');
    }

    public function test_owner_melihat_semua_cabang_merchantnya(): void
    {
        [, $cabangA, $cabangB, $owner] = $this->buatMerchantDuaCabang();

        $ids = array_map('intval', $owner->cabangIds());
        $this->assertContains((int) $cabangA->id, $ids, 'Owner harus lihat cabang A.');
        $this->assertContains((int) $cabangB->id, $ids, 'Owner harus lihat cabang B.');
        $this->assertTrue($owner->multiCabang(), 'Owner multi-cabang.');
    }

    public function test_scope_transaksi_mengunci_karyawan_ke_cabangnya(): void
    {
        [, $cabangA, $cabangB, , $kasirA] = $this->buatMerchantDuaCabang();

        $tA = \App\Models\transaksi::create([
            'invoice' => 'UJI-A-'.uniqid(), 'cabang_id' => $cabangA->id,
            'user_id' => $kasirA->id, 'status' => 'Process',
        ]);
        $tB = \App\Models\transaksi::create([
            'invoice' => 'UJI-B-'.uniqid(), 'cabang_id' => $cabangB->id,
            'status' => 'Process',
        ]);

        $this->actingAs($kasirA);
        $terlihat = \App\Models\transaksi::pluck('id')->map('intval')->all();

        $this->assertContains((int) $tA->id, $terlihat, 'Kasir A harus lihat order cabangnya.');
        $this->assertNotContains((int) $tB->id, $terlihat, 'Kasir A TIDAK boleh lihat order cabang B.');
    }
}
