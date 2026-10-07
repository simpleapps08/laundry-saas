<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AddKaryawanRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\transaksi;
use Session;

class KaryawanController extends Controller
{
    public function index()
    {
      $kry = User::where('auth','Karyawan')->get();
      return view('modul_admin.pengguna.kry', compact('kry'));
    }

    public function create()
    {
      return view('modul_admin.pengguna.addkry');
    }

    public function store(AddKaryawanRequest $request)
    {
        $phone_number = preg_replace('/^0/','62',$request->no_telp);
        $adduser = New User();
        $adduser->name          = $request->name;
        $adduser->email         = $request->email;
        $adduser->nama_cabang   = $request->nama_cabang;
        $adduser->alamat        = $request->alamat;
        $adduser->alamat_cabang = $request->alamat_cabang;
        $adduser->no_telp       = $phone_number;
        $adduser->status        = 'Active';
        $adduser->auth          = 'Karyawan';
        $adduser->password      = Hash::make($request->password);
        $adduser->save();

      $adduser->assignRole($adduser->auth);

      Session::flash('success','Karyawan Berhasil Dibuat.');
      return redirect('karyawan');
    }

    /**
     * Detail karyawan + riwayat transaksinya.
     */
    public function show($id)
    {
      $karyawan = User::where('id', $id)->where('auth','Karyawan')->firstOrFail();
      $transaksi = transaksi::with('price')
        ->where('user_id', $karyawan->id)
        ->orderBy('created_at','desc')
        ->get();

      return view('modul_admin.pengguna.detailkry', compact('karyawan','transaksi'));
    }

    /**
     * Form edit karyawan.
     */
    public function edit($id)
    {
      $karyawan = User::where('id', $id)->where('auth','Karyawan')->firstOrFail();
      return view('modul_admin.pengguna.editkry', compact('karyawan'));
    }

    /**
     * Update data karyawan. Password hanya diubah bila diisi.
     */
    public function update(Request $request, $id)
    {
      $karyawan = User::where('id', $id)->where('auth','Karyawan')->firstOrFail();

      $request->validate([
        'name'  => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,'.$karyawan->id,
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

      $karyawan->update($data);

      Session::flash('success','Karyawan Berhasil Diperbarui.');
      return redirect('karyawan');
    }

    /**
     * Hapus karyawan.
     *
     * Bila karyawan masih punya transaksi, penghapusan DITOLAK (jejak
     * keuangan harus terjaga). Alternatif: nonaktifkan lewat updateKaryawan().
     */
    public function destroy($id)
    {
      $karyawan = User::where('id', $id)->where('auth','Karyawan')->firstOrFail();

      $jumlah = transaksi::where('user_id', $karyawan->id)->count();

      if ($jumlah > 0) {
        Session::flash('error','Karyawan tidak dapat dihapus: masih memiliki '.$jumlah.' transaksi. Nonaktifkan akun sebagai gantinya.');
        return redirect('karyawan');
      }

      $karyawan->removeRole($karyawan->auth);
      $karyawan->delete();

      Session::flash('success','Karyawan Berhasil Dihapus.');
      return redirect('karyawan');
    }

    // Update Status Karyawan
    public function updateKaryawan(Request $request)
    {
      // FIX: dulu User::find() bisa null -> update() pada null -> 500, dan
      // method tanpa return membingungkan pemanggil berbasis GET.
      $id = $request->id ?: $request->route('id');
      $karyawan = User::find($id);

      if (! $karyawan) {
        if ($request->expectsJson() || $request->ajax()) {
          return response()->json(['error' => 'Karyawan tidak ditemukan.'], 404);
        }
        Session::flash('error','Karyawan tidak ditemukan.');
        return redirect()->back();
      }

      $karyawan->update([
        'status'  => $karyawan->status == 'Active' ? 'Not Active' : 'Active'
      ]);

      Session::flash('success','Status Karyawan Berhasil Diupdate.');

      if ($request->expectsJson() || $request->ajax()) {
        return response()->json(['status' => $karyawan->status]);
      }

      return redirect('karyawan');
    }
}
