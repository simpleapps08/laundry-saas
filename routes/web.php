<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', 'FrontController@index');

// ── TAHAP 3: Dashboard Merchant (multi-cabang) ──────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('merchant/dashboard', [\App\Http\Controllers\MerchantDashboardController::class, 'index'])
        ->name('merchant.dashboard');
    Route::post('merchant/ganti-cabang', [\App\Http\Controllers\MerchantDashboardController::class, 'gantiCabang'])
        ->name('merchant.ganti-cabang');
});

// Frontend
Route::get('pencarian-laundry','FrontController@search');

Auth::routes([
    'register' => false,
]);

Route::middleware('auth')->group(function () {
  Route::get('/home', 'HomeController@index')->name('home');

  Route::get('read-notifikasi','HomeController@readNotifikasi');
  // Modul Admin
  Route::prefix('/')->middleware('akses.admin')->group(function () {
    Route::resource('admin','Admin\AdminController');

    // Pengguna/karyawan
    Route::resource('karyawan','Admin\KaryawanController');
    Route::get('update-satatus-karyawan','Admin\KaryawanController@updateKaryawan');

    // Customer
    Route::resource('customer','Admin\CustomerController');

    // Data Transaksi
    // Keputusan Boz: transaksi hanya boleh DITAMBAH + DIBATALKAN, tidak diedit.
    // Method edit/update sengaja tidak diimplementasi -> jangan didaftarkan
    // supaya URL-nya 404, bukan 500.
    Route::resource('transaksi','Admin\TransaksiController')->only(['index','create','store','show','destroy']);
    Route::get('filter-transaksi','Admin\TransaksiController@filtertransaksi'); // filter data transaksi by karyawan
    Route::get('invoice-customer/{invoice}','Admin\TransaksiController@invoice'); // lihat invoice

    Route::get('data-harga','Admin\FinanceController@dataharga');
    Route::post('harga-store','Admin\FinanceController@hargastore');
    Route::get('edit-harga','Admin\FinanceController@hargaedit');

    // Finance
    Route::get('finance','Admin\FinanceController@index')->name('finance.index');

    // Notifikasi
    Route::get('read-notification','Admin\AdminController@notif');

    // Setting
    Route::get('settings','Admin\SettingsController@setting');
    Route::put('proses-setting-page/{id}','Admin\SettingsController@proses_set_page')->name('seting-page.update');
    Route::put('set-theme/{id}','Admin\SettingsController@set_theme')->name('setting-theme.update');
    Route::put('set-target-laundry/{id}','Admin\SettingsController@set_target_laundry')->name('set-target.update');
    Route::post('add-bank','Admin\SettingsController@bank')->name('setting.bank');
    Route::put('set-notif/{id}','Admin\SettingsController@notif')->name('set-notif.update');

    // Profile
    Route::get('profile-admin/{id}','Admin\AdminController@profile');
    Route::get('profile-admin-edit','Admin\AdminController@edit_profile');

    // Dodkumentasi
    Route::get('dokumentasi','Admin\DokumentasiController@index'); // Dokumentasi
    Route::get('dokumentasi/tentang','Admin\DokumentasiController@tentang'); // Tentang
    Route::get('dokumentasi/instalasi-penggunaan','Admin\DokumentasiController@instalasi'); // Instalasi & Penggunaan
    Route::get('dokumentasi/versi','Admin\DokumentasiController@versi'); // Versi dan Pembaruan
    Route::get('dokumentasi/notifikasi','Admin\DokumentasiController@notifikasi'); // Notifikasi

  });

  // ── Modul Super Admin (FASE 3) ──────────────────────────────────────
  // Mengelola lintas cabang: daftar cabang, langganan, tagihan, paket.
  Route::prefix('super-admin')->middleware('superadmin')->group(function () {
    Route::get('/', 'SuperAdmin\PanelController@index')->name('superadmin.index');
    Route::get('cabang', 'SuperAdmin\PanelController@cabang')->name('superadmin.cabang');
    Route::get('langganan', 'SuperAdmin\PanelController@langganan')->name('superadmin.langganan');
    Route::post('langganan/simpan', 'SuperAdmin\PanelController@simpanLangganan')->name('superadmin.langganan.simpan');
    Route::get('tagihan', 'SuperAdmin\PanelController@tagihan')->name('superadmin.tagihan');
    Route::post('tagihan/lunas', 'SuperAdmin\PanelController@lunaskan')->name('superadmin.tagihan.lunas');
    Route::get('paket', 'SuperAdmin\PanelController@paket')->name('superadmin.paket');
    Route::get('merchant', 'SuperAdmin\PanelController@merchant')->name('superadmin.merchant');
    Route::post('merchant/tagihan', 'SuperAdmin\PanelController@buatTagihanMerchant')->name('superadmin.merchant.tagihan');
    Route::get('setelan', 'SuperAdmin\PanelController@setelan')->name('superadmin.setelan');
    Route::post('setelan/simpan', 'SuperAdmin\PanelController@simpanSetelan')->name('superadmin.setelan.simpan');
  });

  // Modul Karyawan
  Route::prefix('/')->middleware('role:Karyawan')->group(function () {
    // PelayananController hanya menyediakan index/store/show/create.
    // edit/update/destroy tidak diimplementasi -> dibatasi agar 404, bukan 500.
    Route::resource('pelayanan','Karyawan\PelayananController')->only(['index','create','store','show']);
    // Transaksi
    Route::get('add-order','Karyawan\PelayananController@addorders');
    Route::get('update-status-laundry','Karyawan\PelayananController@updateStatusLaundry');

    // Customer
    Route::get('customers','Karyawan\CustomerController@index');
    Route::get('customers/{id}','Karyawan\CustomerController@detail');
    Route::get('customers-create','Karyawan\CustomerController@create');
    Route::post('customers-store','Karyawan\CustomerController@store');

    // Filter
    Route::get('listharga','Karyawan\PelayananController@listharga');
    Route::get('listhari','Karyawan\PelayananController@listhari');

    // Laporan
    Route::get('laporan','Karyawan\LaporanController@laporan');
    Route::get('export-excel','Karyawan\LaporanController@exportExcel');

    // Invoice
    Route::get('invoice-kar/{id}','Karyawan\InvoiceController@invoicekar');
    Route::get('cetak-invoice/{id}/print','Karyawan\InvoiceController@cetakinvoice');

    // Profile
    Route::get('profile-karyawan/{id}','Karyawan\ProfileController@karyawanProfile');
    Route::put('profile-karyawan/update/{id}','Karyawan\ProfileController@karyawanProfileSave');

    // Setting
    Route::get('karyawan-setting','Karyawan\SettingsController@setting');
    Route::put('proses-setting-karyawan/{id}','Karyawan\SettingsController@proses_setting_karyawan')->name('proses-setting-karyawan.update');
  });


  // Modul Customer
  Route::prefix('/')->middleware('role:Customer')->group(function (){
    // Setting
    Route::get('setitng','Customer\SettingController@index')->name('customer.setting');
    Route::put('setitng/{id}','Customer\SettingController@settingUpdateCustomer')->name('customer.setting-update');

    // Profile
    Route::get('me','Customer\ProfileController@index');
    Route::put('me/{id}','Customer\ProfileController@updateProfile');
  });
});
