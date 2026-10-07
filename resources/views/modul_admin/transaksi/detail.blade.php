@extends('layouts.backend')
@section('title','Transaksi - Detail')
@section('header','Detail Transaksi')
@section('content')
@if ($message = Session::get('success'))
  <div class="alert alert-success alert-block">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>{ $message }</strong>
  </div>
@elseif($message = Session::get('error'))
  <div class="alert alert-danger alert-block">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>{ $message }</strong>
  </div>
@endif

<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Invoice {{ $trx->invoice }}</h4>
        <div class="alert alert-info">
          <strong>Catatan:</strong> Transaksi bersifat <em>read-only</em>. Bila ada kekeliruan,
          gunakan tombol <strong>Batalkan</strong> di halaman Transaksi &mdash; data tetap tersimpan sebagai jejak audit.
        </div>
        <table class="table">
          <tr><th>Invoice</th><td>{{ $trx->invoice }}</td></tr>
          <tr><th>Tanggal</th><td>{{ $trx->tgl_transaksi }}</td></tr>
          <tr><th>Customer</th><td>{{ $trx->customer }} ({{ $trx->email_customer }})</td></tr>
          <tr><th>Karyawan</th><td>{{ $trx->user->name ?? '-' }}</td></tr>
          <tr><th>Jenis Layanan</th><td>{{ $trx->price->jenis ?? '-' }}</td></tr>
          <tr><th>Berat</th><td>{{ $trx->kg }} kg</td></tr>
          <tr><th>Harga / kg</th><td>Rp {{ number_format($trx->harga_numeric ?? 0, 0, ',', '.') }}</td></tr>
          <tr><th>Diskon</th><td>Rp {{ number_format($trx->disc_numeric ?? 0, 0, ',', '.') }}</td></tr>
          <tr><th>Total</th><td><strong>Rp {{ number_format($trx->total_numeric ?? 0, 0, ',', '.') }}</strong></td></tr>
          <tr><th>Status Order</th><td>
            @if ($trx->status_order == 'Batal')
              <span class="label label-danger">Batal</span>
            @else
              {{ $trx->status_order }}
            @endif
          </td></tr>
          <tr><th>Status Pembayaran</th><td>{{ $trx->status_payment }}</td></tr>
          <tr><th>Jenis Pembayaran</th><td>{{ $trx->jenis_pembayaran }}</td></tr>
        </table>
        <a href="{{ url('invoice-customer', $trx->invoice) }}" class="btn btn-success">Lihat Invoice</a>
        @if ($trx->status_order != 'Batal')
        <form action="{{ route('transaksi.destroy', $trx->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan transaksi ini? Data tetap tersimpan sebagai jejak audit.');">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Batalkan Transaksi</button>
        </form>
        @endif
        <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Kembali</a>
      </div>
    </div>
  </div>
</div>
@endsection
