<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{transaksi,user,harga};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Rupiah;
use Session;

class TransaksiController extends Controller
{

    public function index()
    {
      $transaksi = transaksi::with('price')
      ->orderBy('created_at','desc')->get();

      $filter = User::select('id','name')->where('auth','Karyawan')->get();

      return view('modul_admin.transaksi.index', compact('transaksi','filter'));
    }

    /**
     * Form tambah transaksi.
     */
    public function create()
    {
      $customer = User::where('auth','Customer')->where('status','Active')->get();
      $karyawan = User::where('auth','Karyawan')->where('status','Active')->get();
      $harga    = harga::orderBy('jenis')->get();

      return view('modul_admin.transaksi.create', compact('customer','karyawan','harga'));
    }

    /**
     * Simpan transaksi baru.
     *
     * Menulis BAIK kolom lama (string) MAUPUN kolom numerik hasil Fase 1,
     * supaya laporan SUM() tetap benar dan invoice lama tidak berubah.
     */
    public function store(Request $request)
    {
      $request->validate([
        'customer_id'      => 'required',
        'harga_id'         => 'required',
        'kg'               => 'required|numeric|min:0.01',
        'tgl_transaksi'    => 'required',
        'jenis_pembayaran' => 'required|in:Tunai,Transfer',
      ]);

      $tarif = harga::findOrFail($request->harga_id);

      $kg    = (float) $request->kg;
      $harga = (float) ($tarif->harga_numeric ?? $tarif->harga);
      $disc  = (float) ($request->disc ?? 0);

      $subtotal     = $kg * $harga;
      $harga_akhir  = max($subtotal - $disc, 0);

      $customer = User::find($request->customer_id);

      $trx = new transaksi();
      $trx->invoice          = $this->buatInvoice();
      $trx->customer_id      = $customer->id;
      $trx->customer         = $customer->name;
      $trx->email_customer   = $customer->email;
      $trx->user_id          = $request->user_id ?: Auth::id();
      $trx->harga_id         = $tarif->id;
      $trx->tgl_transaksi    = $request->tgl_transaksi;
      $trx->tanggal_masuk    = $request->tgl_transaksi;
      $trx->status_order     = $request->status_order ?: 'Process';
      $trx->status_payment   = $request->status_payment ?: 'Pending';
      $trx->jenis_pembayaran = $request->jenis_pembayaran;

      // Kolom lama (string) — dipertahankan demi kompatibilitas view existing.
      $trx->kg           = (string) $kg;
      $trx->harga        = (string) $harga;
      $trx->disc         = (string) $disc;
      $trx->harga_akhir  = (string) $harga_akhir;
      $trx->tgl          = date('d', strtotime($request->tgl_transaksi));
      $trx->bulan        = date('m', strtotime($request->tgl_transaksi));
      $trx->tahun        = date('Y', strtotime($request->tgl_transaksi));

      // Kolom numerik (Fase 1) — dipakai laporan SUM().
      $trx->kg_numeric           = $kg;
      $trx->harga_numeric        = $harga;
      $trx->disc_numeric         = $disc;
      $trx->harga_akhir_numeric  = $harga_akhir;
      $trx->total_numeric        = $harga_akhir;

      $trx->save();

      Session::flash('success','Transaksi Berhasil Dibuat. Invoice: '.$trx->invoice);
      return redirect('transaksi');
    }

    /**
     * Detail transaksi — hanya baca, TIDAK ada form edit.
     *
     * Keputusan Boz: transaksi hanya boleh DITAMBAH, tidak diedit.
     * Pembatalan lewat destroy() memakai status, bukan hapus baris.
     */
    public function show($id)
    {
      $trx = transaksi::with('price','customers','user')->findOrFail($id);
      return view('modul_admin.transaksi.detail', compact('trx'));
    }

    /**
     * Nomor invoice unik: INV-YYYYMM-####
     */
    private function buatInvoice(): string
    {
      $prefix = 'INV-'.date('Ym').'-';
      $urut = transaksi::where('invoice','like',$prefix.'%')->count() + 1;

      do {
        $kode = $prefix.str_pad($urut, 4, '0', STR_PAD_LEFT);
        $urut++;
      } while (transaksi::where('invoice',$kode)->exists());

      return $kode;
    }

    /**
     * PEMBATALAN transaksi (bukan hapus permanen).
     *
     * Sesuai keputusan Boz: transaksi hanya boleh ditambah + dibatalkan.
     * Baris TETAP ADA di database supaya jejak audit & laporan keuangan
     * tidak kehilangan data; yang berubah hanya status_order = 'Batal'.
     */
    public function destroy($id)
    {
      $trx = transaksi::findOrFail($id);

      if ($trx->status_order === 'Batal') {
        Session::flash('error','Transaksi ini sudah dibatalkan sebelumnya.');
        return redirect('transaksi');
      }

      DB::transaction(function () use ($trx) {
        $trx->update([
          'status_order'   => 'Batal',
          'status_payment' => 'Pending',
        ]);
      });

      Session::flash('success','Transaksi '.$trx->invoice.' DIBATALKAN. Data tetap tersimpan sebagai jejak audit.');
      return redirect('transaksi');
    }

    // Filter Transaksi
    public function filtertransaksi(Request $request)
    {
      if ($request->user_id != 'all') {
        $transaksi = transaksi::with('price')
        ->where('user_id', $request->user_id)
        ->orderBy('created_at','desc')
        ->get();
      }elseif($request->user_id == 'all') {
        $transaksi = transaksi::with('price')
        ->orderBy('created_at','desc')
        ->get();
      }


      // Kembalikan DUA representasi dari SATU query (satu sumber kebenaran):
      // --ROWS-- untuk tabel (desktop) dan --CARDS-- untuk kartu (mobile).
      // Dipisah penanda agar JS bisa mengisi container yang tepat.
      $rows = "";
      $cards = "";
      $no=1;
      foreach($transaksi as $item) {
        $total = Rupiah::getRupiah($item->kg * $item->harga);

        // Label status (samakan dengan view)
        $stLabel = ['Done'=>'Selesai','Delivery'=>'Sudah Diambil','Process'=>'Sedang Proses'][$item->status_order] ?? $item->status_order;
        $stClass = ['Done'=>'label-success','Delivery'=>'label-info','Process'=>'label-info'][$item->status_order] ?? 'label-default';
        $payLabel = ['Success'=>'Sudah Dibayar','Pending'=>'Belum Dibayar'][$item->status_payment] ?? $item->status_payment;
        $payClass = ['Success'=>'label-success','Pending'=>'label-info'][$item->status_payment] ?? 'label-default';
        $jenis = optional($item->price)->jenis ?? '-';

        $rows .="<tr>
          <td>".$no."</td>
          <td>".$item->tgl_transaksi."</td>
          <td>".$item->customer."</td>
          <td><span class='label ".$stClass."'>".$stLabel."</span></td>
          <td><span class='label ".$payClass."'>".$payLabel."</span></td>
          <td>".$jenis."</td>
          <td>".$total."</td>";
        $rows .="<td align='center'><a href='invoice-customer/".$item->invoice."' class='btn btn-sm btn-success' style='color:white'>Invoice</a></td>";
        $rows .= "</tr>";

        $cards .="<div class='jv-mcard'>
          <div class='jv-mcard-title'><span>".$item->invoice."</span><span class='label ".$stClass."'>".$stLabel."</span></div>
          <div class='jv-mcard-row'><span class='jv-mcard-label'>Tanggal</span><span class='jv-mcard-value'>".$item->tgl_transaksi."</span></div>
          <div class='jv-mcard-row'><span class='jv-mcard-label'>Customer</span><span class='jv-mcard-value'>".$item->customer."</span></div>
          <div class='jv-mcard-row'><span class='jv-mcard-label'>Jenis</span><span class='jv-mcard-value'>".$jenis."</span></div>
          <div class='jv-mcard-row'><span class='jv-mcard-label'>Pembayaran</span><span class='jv-mcard-value'><span class='label ".$payClass."'>".$payLabel."</span></span></div>
          <div class='jv-mcard-row'><span class='jv-mcard-label'>Total</span><span class='jv-mcard-value' style='font-weight:700'>".$total."</span></div>
          <div class='jv-mcard-actions'><a href='invoice-customer/".$item->invoice."' class='btn btn-sm btn-success' style='color:white'>Invoice</a></div>
        </div>";
        $no++;
      }
      if ($cards === '') {
        $cards = "<div class='jv-mcard-empty'>Belum ada transaksi.</div>";
      }
      return $rows . "<!--CARDS-->" . $cards;
    }

    // Invoice
    public function invoice( Request $request)
    {
      $invoice = transaksi::with('price')
      ->where('invoice', $request->invoice)
      ->orderBy('id','DESC')->get();

      $dataInvoice = transaksi::with('customers','user')
      ->where('invoice', $request->invoice)
      ->first();

      return view('modul_admin.transaksi.invoice', compact('invoice','dataInvoice'));
    }
}
