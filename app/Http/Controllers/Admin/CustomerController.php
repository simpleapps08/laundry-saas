<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddKaryawanRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\transaksi;
use Illuminate\Support\Facades\Hash;
use Session;

class CustomerController extends Controller
{

    public function index()
    {
      $customer = User::where('auth','Customer')->get();
      return view('modul_admin.customer.index', compact('customer'));
    }

    /**
     * Form tambah customer.
     */
    public function create()
    {
      return view('modul_admin.customer.create');
    }

    /**
     * Simpan customer baru.
     */
    public function store(Request $request)
    {
      $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|max:255|unique:users,email',
        'no_telp'  => 'nullable|string|max:20',
        'password' => 'required|string|min:6',
      ]);

      $customer = new User();
      $customer->name     = $request->name;
      $customer->email    = $request->email;
      $customer->no_telp  = $request->no_telp ? preg_replace('/^0/','62',$request->no_telp) : null;
      $customer->alamat   = $request->alamat;
      $customer->status   = 'Active';
      $customer->auth     = 'Customer';
      $customer->password = Hash::make($request->password);
      $customer->save();

      $customer->assignRole($customer->auth);

      Session::flash('success','Customer Berhasil Dibuat.');
      return redirect('customer');
    }

    public function show($id)
    {
      $customer = User::with('transaksiCustomer')->where('id',$id)->first();
      return view('modul_admin.customer.infoCustomer', compact('customer'));
    }

    /**
     * Form edit customer.
     */
    public function edit($id)
    {
      $customer = User::where('id', $id)->where('auth','Customer')->firstOrFail();
      return view('modul_admin.customer.edit', compact('customer'));
    }

    /**
     * Update data customer. Password hanya diubah bila diisi.
     */
    public function update(Request $request, $id)
    {
      $customer = User::where('id', $id)->where('auth','Customer')->firstOrFail();

      $request->validate([
        'name'    => 'required|string|max:255',
        'email'   => 'required|email|max:255|unique:users,email,'.$customer->id,
        'no_telp' => 'nullable|string|max:20',
      ]);

      $data = [
        'name'    => $request->name,
        'email'   => $request->email,
        'alamat'  => $request->alamat,
        'no_telp' => $request->no_telp ? preg_replace('/^0/','62',$request->no_telp) : $customer->no_telp,
      ];

      if ($request->filled('password')) {
        $request->validate(['password' => 'string|min:6']);
        $data['password'] = Hash::make($request->password);
      }

      $customer->update($data);

      Session::flash('success','Customer Berhasil Diperbarui.');
      return redirect('customer');
    }

    /**
     * Hapus customer.
     *
     * Bila customer masih punya transaksi, penghapusan DITOLAK supaya
     * laporan keuangan tidak kehilangan jejak. Admin diarahkan menonaktifkan
     * akun (status = Not Active) sebagai gantinya.
     */
    public function destroy($id)
    {
      $customer = User::where('id', $id)->where('auth','Customer')->firstOrFail();

      $jumlah = transaksi::where('customer_id', $customer->id)->count();

      if ($jumlah > 0) {
        Session::flash('error','Customer tidak dapat dihapus: masih memiliki '.$jumlah.' transaksi. Nonaktifkan akun sebagai gantinya.');
        return redirect('customer');
      }

      $customer->removeRole($customer->auth);
      $customer->delete();

      Session::flash('success','Customer Berhasil Dihapus.');
      return redirect('customer');
    }
}
