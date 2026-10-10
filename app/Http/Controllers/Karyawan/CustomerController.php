<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use ErrorException;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Requests\AddCustomerRequest;
use Illuminate\Support\Facades\Hash;
use App\Jobs\RegisterCustomerJob;
use Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class CustomerController extends Controller
{
    // index
    public function index()
    {
      $customer = User::where('karyawan_id',Auth::user()->id)
      ->where('auth','Customer')
      ->orderBy('id','DESC')->get();
      return view('karyawan.customer.index', compact('customer'));
    }

    // Detail Customer
    public function detail($id)
    {
      $customer = User::with('transaksiCustomer')
      ->where('karyawan_id',Auth::user()->id)
      ->where('id',$id)->first();
      return view('karyawan.customer.detail', compact('customer'));
    }

    // Create
    public function create(\Illuminate\Http\Request $request)
    {
      // F2: kalau datang dari Order Online Masuk, isikan form otomatis
      // (nama/WA/alamat) supaya kasir tidak mengetik ulang.
      $prefill = [
        'name'   => '',
        'no_telp'=> '',
        'alamat' => '',
        'kode'   => '',
      ];

      $kode = $request->query('dari_pesanan');
      if ($kode) {
        $pesanan = \App\Models\PesananOnline::where('kode_pesanan', $kode)->first();
        if ($pesanan && Auth::user()->bolehAksesCabang($pesanan->cabang_id)) {
          $prefill = [
            'name'   => $pesanan->nama,
            // no_telp disimpan 62xxx; form minta format lokal (0xxx)
            'no_telp'=> preg_replace('/^62/', '0', $pesanan->no_telp),
            'alamat' => (string) $pesanan->alamat,
            'kode'   => $pesanan->kode_pesanan,
          ];
        }
      }

      return view('karyawan.customer.create', compact('prefill'));
    }

    // Store
    public function store(AddCustomerRequest $request)
    {

      try {
        DB::beginTransaction();


        $phone_number = preg_replace('/^0/','62',$request->no_telp);
        $password = str::random(8);

        // F2 FIX: tentukan cabang & merchant customer.
        // Prioritas: cabang pesanan online (kalau ada) -> cabang kasir pembuat.
        $cabangId   = Auth::user()->cabang_id;
        $merchantId = Auth::user()->merchant_id;

        $kodePesananInput = $request->input('kode_pesanan');
        if ($kodePesananInput) {
          $pesananAsal = \App\Models\PesananOnline::where('kode_pesanan', $kodePesananInput)->first();
          if ($pesananAsal && Auth::user()->bolehAksesCabang($pesananAsal->cabang_id)) {
            $cabangId   = $pesananAsal->cabang_id;
            $merchantId = $pesananAsal->merchant_id ?: $merchantId;
          }
        }

        $addCustomer = User::create([
          'karyawan_id' => Auth::id(),
          'cabang_id'   => $cabangId,
          'merchant_id' => $merchantId,
          'name'        => $request->name,
          'email'       => $request->email,
          'auth'        => 'Customer',
          'status'      => 'Active',
          'no_telp'     => $phone_number,
          'alamat'      => $request->alamat,
          'password'    => Hash::make($password)
        ]);

        $addCustomer->assignRole($addCustomer->auth);

        if ($addCustomer) {
          // Menyiapkan data Email
          $data = array(
              'name'            => $addCustomer->name,
              'email'           => $addCustomer->email,
              'password'        => $password,
              'url_login'       => url('/login'),
              'nama_laundry'    => Auth::user()->nama_cabang,
              'alamat_laundry'  => Auth::user()->alamat_cabang,
          );
          // Kirim email
           if (setNotificationEmail(1) == 1) {
            dispatch(new RegisterCustomerJob($data));
           }
        }
        // F2: kalau customer ini berasal dari order online, tandai pesanannya
        // sudah ditindaklanjuti (supaya hilang dari daftar "Menunggu").
        $kodePesanan = $request->input('kode_pesanan');
        if ($kodePesanan) {
          $pesanan = \App\Models\PesananOnline::where('kode_pesanan', $kodePesanan)->first();
          if ($pesanan && Auth::user()->bolehAksesCabang($pesanan->cabang_id)) {
            $pesanan->update(['status_online' => 'Diproses', 'diproses_at' => now()]);
          }
        }

        DB::commit();
        Session::flash('success','Customer Berhasil Ditambah !');
        return redirect('customers');
      } catch (ErrorException $e) {
        DB::rollback();
        throw new ErrorException($e->getMessage());
      }
    }
}
