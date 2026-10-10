<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\LaundrySetting;
use App\Models\Merchant;
use App\Models\User;
use App\Models\notifications_setting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Setelan per-cabang: target laundry & notifikasi.
 *
 * Regresi yang dicegah:
 *  1. `set_target_laundry` / `notif` dulu `findOrFail($id)` dengan `$id` = ID USER
 *     sementara tabel setelan kosong -> 404, setelan TIDAK PERNAH bisa disimpan.
 *  2. Setelan harus TERISOLASI per-cabang (tidak bocor antar cabang).
 *  3. `telegram_channel_selesai` dulu diisi dari nilai channel masuk (bug copy-paste).
 */
class SetelanPerCabangTest extends TestCase
{
    use DatabaseTransactions;

    private function buatMerchantDuaCabang(): array
    {
        $suffix = uniqid();
        $merchant = Merchant::create([
            'kode' => 'MS-'.$suffix,
            'nama' => 'Merchant Setelan',
            'slug' => 'merchant-setelan-'.$suffix,
            'status' => 'Aktif',
        ]);

        $cabangA = Cabang::create(['merchant_id' => $merchant->id, 'kode' => 'SA-'.$suffix, 'nama' => 'Setelan A']);
        $cabangB = Cabang::create(['merchant_id' => $merchant->id, 'kode' => 'SB-'.$suffix, 'nama' => 'Setelan B']);

        $owner = User::create([
            'name' => 'Owner Setelan', 'email' => 'owner.set.'.uniqid().'@t.local',
            'password' => bcrypt('123456'), 'status' => 'Aktif', 'auth' => 'Admin',
            'merchant_id' => $merchant->id, 'cabang_id' => null,
        ]);
        $owner->assignRole('Admin');

        return [$merchant, $cabangA, $cabangB, $owner];
    }

    /** Target bisa disimpan per-cabang (dulu 404). */
    public function test_target_bisa_disimpan_per_cabang(): void
    {
        [$merchant, $cabangA, $cabangB, $owner] = $this->buatMerchantDuaCabang();

        $this->actingAs($owner);
        session(['cabang_aktif_id' => $cabangA->id]);

        $resp = $this->put('/set-target-laundry/0', [
            'target_day' => 100, 'target_month' => 3000, 'target_year' => 36000,
        ]);
        $resp->assertStatus(302); // redirect back(), bukan 404/'500

        $this->assertDatabaseHas('laundry_settings', [
            'cabang_id' => $cabangA->id, 'target_day' => 100,
        ]);

        // Ganti cabang aktif -> setelan TERISOLASI (tidak ikut terbaca)
        session(['cabang_aktif_id' => $cabangB->id]);
        $this->assertNull(LaundrySetting::where('cabang_id', $cabangB->id)->first());
    }

    /** Notifikasi bisa disimpan & channel selesai TERPISAH dari channel masuk. */
    public function test_notif_disimpan_dan_channel_selesai_terpisah(): void
    {
        [$merchant, $cabangA, $cabangB, $owner] = $this->buatMerchantDuaCabang();

        $this->actingAs($owner);
        session(['cabang_aktif_id' => $cabangA->id]);

        $resp = $this->put('/set-notif/0', [
            'telegram_order_masuk'   => '1',
            'telegram_order_selesai' => '1',
            'telegram_channel_masuk'   => '@masuk',
            'telegram_channel_selesai' => '@selesai',
        ]);
        $resp->assertStatus(302);

        $row = notifications_setting::where('cabang_id', $cabangA->id)->first();
        $this->assertNotNull($row);
        $this->assertSame('@masuk', $row->telegram_channel_masuk);
        // Bug copy-paste: dulu '@selesai' selalu sama dengan '@masuk'
        $this->assertSame('@selesai', $row->telegram_channel_selesai);
    }

    /** Channel selesai dikosongkan -> fallback ke channel masuk. */
    public function test_channel_selesai_kosong_fallback_ke_masuk(): void
    {
        [$merchant, $cabangA, $cabangB, $owner] = $this->buatMerchantDuaCabang();

        $this->actingAs($owner);
        session(['cabang_aktif_id' => $cabangA->id]);

        $this->put('/set-notif/0', [
            'telegram_order_masuk'   => '1',
            'telegram_channel_masuk'   => '@satu',
            'telegram_channel_selesai' => '',
        ])->assertStatus(302);

        $row = notifications_setting::where('cabang_id', $cabangA->id)->first();
        $this->assertSame('@satu', $row->telegram_channel_selesai);
    }
}
