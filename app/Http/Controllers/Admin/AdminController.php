<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;
use Auth;
use DB;
use Session;
use Spatie\Permission\Models\Role;
use Carbon\carbon;

class AdminController extends Controller
{
    /**
     * Daftar akun Admin.
     */
    public function index()
    {
      $adm = User::where('auth','Admin')->get();
      return view('modul_admin.pengguna.admin', compact('adm'));
    }

    /**
     * Form tambah admin.
     */
    public function create()
    {
      return view('modul_admin.pengguna.addadmin');
    }

    /**
     * Simpan admin baru.
     */
    public function store(Request $request)
    {
      $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|max:255|unique:users,email',
        'password' => 'required|string|min:6',
      ]);

      $admin = new User();
      $admin->name          = $request->name;
      $admin->email         = $request->email;
      $admin->nama_cabang   = $request->nama_cabang;
      $admin->alamat        = $request->alamat;
      $admin->alamat_cabang = $request->alamat_cabang;
      $admin->no_telp       = $request->no_telp ? preg_replace('/^0/','62',$request->no_telp) : null;
      $admin->status        = 'Active';
      $admin->auth          = 'Admin';
      $admin->password      = Hash::make($request->password);
      $admin->save();

      $admin->assignRole('Admin');

      Session::flash('success','Admin Berhasil Dibuat.');
      return redirect('admin');
    }

    /**
     * Detail admin.
     */
    public function show($id)
    {
      $admin = User::where('id',$id)->where('auth','Admin')->firstOrFail();
      return view('modul_admin.pengguna.detailadmin', compact('admin'));
    }

    /**
     * Form edit admin.
     */
    public function edit($id)
    {
      $admin = User::where('id',$id)->where('auth','Admin')->firstOrFail();
      return view('modul_admin.pengguna.editadmin', compact('admin'));
    }

    /**
     * Update data admin. Password hanya diubah bila diisi.
     */
    public function update(Request $request, $id)
    {
      $admin = User::where('id',$id)->where('auth','Admin')->firstOrFail();

      $request->validate([
        'name'  => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,'.$admin->id,
      ]);

      $data = [
        'name'          => $request->name,
        'email'         => $request->email,
        'nama_cabang'   => $request->nama_cabang,
        'alamat'        => $request->alamat,
        'alamat_cabang' => $request->alamat_cabang,
      ];

      if ($request->filled('no_telp')) {
        $data['no_telp'] = preg_replace('/^0/','62',$request->no_telp);
      }

      if ($request->filled('password')) {
        $request->validate(['password' => 'string|min:6']);
        $data['password'] = Hash::make($request->password);
      }

      $admin->update($data);

      Session::flash('success','Admin Berhasil Diperbarui.');
      return redirect('admin');
    }

    /**
     * Hapus admin.
     *
     * Pengaman:
     *  - Tidak boleh menghapus akun sendiri (bisa terkunci dari sistem).
     *  - Tidak boleh menghapus admin terakhir yang tersisa.
     */
    public function destroy($id)
    {
      $admin = User::where('id',$id)->where('auth','Admin')->firstOrFail();

      if ($admin->id === Auth::id()) {
        Session::flash('error','Anda tidak dapat menghapus akun Anda sendiri.');
        return redirect('admin');
      }

      if (User::where('auth','Admin')->count() <= 1) {
        Session::flash('error','Admin terakhir tidak dapat dihapus.');
        return redirect('admin');
      }

      $admin->removeRole('Admin');
      $admin->delete();

      Session::flash('success','Admin Berhasil Dihapus.');
      return redirect('admin');
    }

    // Profile
    public function profile(Request $request)
    {
      // Dukung dua gaya pemanggilan: route dengan {id} atau tanpa id (diri sendiri).
      $id = $request->route('id') ?: Auth::id();
      $profile = User::where('id',$id)->firstOrFail();
      return view('modul_admin.setting.profile', compact('profile'));
    }

    // Proses edit profile
    public function edit_profile(Request $request)
    {
      // FIX: dulu User::find() bisa null -> update() pada null -> 500.
      $profile = User::find($request->id_profile ?: Auth::id());

      if (! $profile) {
        Session::flash('error','Profil tidak ditemukan.');
        return redirect()->back();
      }

      $profile->update([
        'name'  => $request->name,
        'email'  => $request->email
      ]);

      Session::flash('success','Update Profile Berhasil');
      return $profile;
    }

    /**
     * Daftar notifikasi.
     *
     * Sebelumnya method ini HILANG padahal route /read-notification
     * terdaftar -> BadMethodCallException (500). Method ini juga dipakai
     * mengembalikan jumlah notifikasi belum dibaca.
     */
    public function notif(Request $request)
    {
      // FIX: dulu memakai Auth::user()->notifications (relasi bawaan Laravel
      // Notifiable) yang mencari kolom notifiable_type — kolom itu TIDAK ADA
      // di tabel `notifications` proyek ini -> SQLSTATE[42S22] -> 500.
      // Model Notification lokal memakai skema: transaksi_id, user_id,
      // kategori, title, body, is_read.
      $notifikasi = Notification::where(function ($q) {
          $q->where('user_id', Auth::id())->orWhereNull('user_id');
        })
        ->orderBy('created_at', 'desc')
        ->get();

      if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
          'jumlah' => $notifikasi->where('is_read', 0)->count(),
          'data'   => $notifikasi->take(10)->values(),
        ]);
      }

      return view('modul_admin.notifikasi.index', compact('notifikasi'));
    }
}
