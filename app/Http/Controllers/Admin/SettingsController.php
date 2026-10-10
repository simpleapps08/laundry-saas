<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\{PageSettings,User,LaundrySetting,DataBank,notifications_setting};
use Auth;
use Session;

class SettingsController extends Controller
{

  // Settings
  public function setting()
  {
    // FIX: dulu first() bisa null (tabel kosong) -> view error 500.
    // Sekarang disediakan instance kosong agar view tetap tampil.
    $setpage    = PageSettings::first() ?: new PageSettings();
    $settarget  = LaundrySetting::first() ?: new LaundrySetting();
    $databank   = DataBank::where('user_id',Auth::id())->get();
    $setnotif   = notifications_setting::first() ?: new notifications_setting();

    return view('modul_admin.setting.index', compact('setpage','settarget','databank','setnotif'));
  }

  // Proses setting page
  public function proses_set_page(Request $request, $id)
  {
    $request->validate([
      'judul'   => 'required|max:15'
    ]);

    $img_hero = $request->file('img_hero');
    if ($img_hero) {
        $img_heros = time()."_".$img_hero->getClientoriginalName();
        // Folder Penyimpanan
        $tujuan_upload = 'frontend/img/logo';
        $img_hero->move($tujuan_upload, $img_heros);
    }

    $setpage = PageSettings::find($id) ?: new PageSettings();
    $setpage->judul     = $request->judul;
    $setpage->img_hero  = $img_hero;
    $setpage->tentang   = $request->tentang;
    $setpage->facebook  = $request->facebook;
    $setpage->instagram = $request->instagram;
    $setpage->twitter   = $request->twitter;
    $setpage->whatsapp  = $request->whatsapp;
    $setpage->no_telp   = $request->no_telp;
    $setpage->email     = $request->email;
    $setpage->save();

    if ($setpage) {
      Session::flash('success','Setting Berhasil Disimpan !');
      return back();
    }
  }

  // Check Setting Theme
  public function set_theme(Request $request)
  {
    $id = Auth::id();
    $user = User::all();

    $set_theme = User::findOrFail($id);
    if ($request->theme == NULL) {
      $set_theme->theme = '0';
    } else {
      $set_theme->theme = $request->theme;
    }

    $set_theme->save();

    Session::flash('success','Setting Berhasil Disimpan !');
    return back();
  }

  // Setting Laundry Target
  // FIX: dulu findOrFail($id) dengan $id = ID USER -> tabel laundry_settings kosong
  //      -> 404 (target tidak pernah bisa disimpan). Sekarang firstOrCreate PER-CABANG.
  public function set_target_laundry(Request $request, $id = null)
  {
    $request->validate([
      'target_day'   => 'required|numeric|min:0',
      'target_month' => 'required|numeric|min:0',
      'target_year'  => 'required|numeric|min:0',
    ]);

    // Cabang tujuan = cabang aktif (session) / cabang user / cabang pertama merchant.
    $cabangId = $this->_cabangAktif();

    $set_target = LaundrySetting::firstOrCreate(
      ['cabang_id' => $cabangId],
      ['user_id'   => Auth::id(), 'target_day' => 0, 'target_month' => 0, 'target_year' => 0]
    );
    $set_target->target_day   = $request->target_day;
    $set_target->target_month = $request->target_month;
    $set_target->target_year  = $request->target_year;
    $set_target->save();

    Session::flash('success','Target Berhasil Diupdate !');
    return back();
  }

  /**
   * Tentukan cabang aktif untuk operasi tulis setelan.
   * Prioritas: cabang aktif di session -> cabang_id user -> cabang pertama merchant.
   */
  private function _cabangAktif(): ?int
  {
    $user = Auth::user();
    if ($user === null) return null;

    $cabangAktif = session('cabang_aktif_id');
    if ($cabangAktif !== null && $user->bolehAksesCabang($cabangAktif)) {
      return (int) $cabangAktif;
    }
    if ($user->cabang_id !== null) {
      return (int) $user->cabang_id;
    }
    $ids = $user->cabangIds();
    return $ids[0] ?? null;
  }

  // Simpan Bank
  public function bank(Request $request)
  {

    // FIX: batas 3 bank dihitung PER-USER (dulu global -> user lain ikut kehitung).
    $cek = DataBank::where('user_id', Auth::id())->count();
    if ($cek >= 3) {
      Session::flash('error','Maksimal bank hanya 3 !');
      return back();
    }

    // FIX: unique per-user (dulu global -> satu "BCA" memblokir semua user lain).
    $request->validate([
      'nama_bank'   => ['required', Rule::unique('data_banks', 'nama_bank')->where(fn ($q) => $q->where('user_id', Auth::id()))],
      'no_rekening' => ['required', Rule::unique('data_banks', 'no_rekening')->where(fn ($q) => $q->where('user_id', Auth::id()))],
      'nama_pemilik' => 'required',
    ]);

    DataBank::create([
      'nama_bank'     => $request->nama_bank,
      'no_rekening'   => $request->no_rekening,
      'nama_pemilik'  => $request->nama_pemilik,
      'user_id'       => Auth::id(),
    ]);

    Session::flash('success','Bank Berhasil Ditambah !');
    return back();
  }

  // Edit Bank
  public function editBank(Request $request, $id)
  {
    // FIX: hanya boleh mengubah rekening MILIK SENDIRI (isolasi per-user).
    $bank = DataBank::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

    // FIX: unique per-user, abaikan baris ini sendiri saat cek.
    $request->validate([
      'nama_bank'    => ['required', Rule::unique('data_banks', 'nama_bank')->where(fn ($q) => $q->where('user_id', Auth::id()))->ignore($bank->id)],
      'no_rekening'  => ['required', Rule::unique('data_banks', 'no_rekening')->where(fn ($q) => $q->where('user_id', Auth::id()))->ignore($bank->id)],
      'nama_pemilik' => 'required',
    ]);

    $bank->nama_bank    = $request->nama_bank;
    $bank->no_rekening  = $request->no_rekening;
    $bank->nama_pemilik = $request->nama_pemilik;
    $bank->save();

    Session::flash('success','Bank Berhasil Diupdate !');
    return back();
  }

  // Hapus Bank
  public function hapusBank($id)
  {
    // FIX: hanya boleh menghapus rekening MILIK SENDIRI (isolasi per-user).
    $bank = DataBank::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
    $bank->delete();

    Session::flash('success','Bank Berhasil Dihapus !');
    return back();
  }

  // Notification
  // FIX: dulu findOrFail($id) dengan $id = ID USER -> tabel notifications_settings kosong
  //      -> 404 (notifikasi tidak pernah bisa disimpan). Sekarang firstOrCreate PER-CABANG.
  // FIX: dulu `telegram_channel_selesai` diisi dari `$request->telegram_channel_masuk`
  //      (bug copy-paste) -> notif order selesai salah channel. Sekarang field terpisah.
  public function notif(Request $request, $id = null)
  {
    $notif = notifications_setting::firstOrCreate(
      ['cabang_id' => $this->_cabangAktif()],
      ['user_id'   => Auth::id()]
    );
    $notif->telegram_order_masuk      = $request->has('telegram_order_masuk') ? 1 : 0;
    $notif->telegram_order_selesai    = $request->has('telegram_order_selesai') ? 1 : 0;
    $notif->email                     = $request->has('email') ? 1 : 0;
    $notif->telegram_channel_masuk    = $request->telegram_channel_masuk;
    $notif->telegram_channel_selesai  = $request->telegram_channel_selesai ?: $request->telegram_channel_masuk;
    $notif->wa_order_selesai          = $request->has('wa_order_selesai') ? 1 : 0;
    $notif->wa_token                  = $request->wa_token;
    $notif->save();

    Session::flash('success','Notifications Berhasil Diupdate !');
    return back();
  }

}
