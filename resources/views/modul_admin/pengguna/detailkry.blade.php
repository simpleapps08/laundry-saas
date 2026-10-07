@extends('layouts.backend')
@section('title','Karyawan - Detail')
@section('header','Detail Karyawan')
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
  <div class="col-lg-5">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Profil Karyawan</h4>
        <table class="table">
          <tr><th>Nama</th><td>{{ $karyawan->name }}</td></tr>
          <tr><th>Email</th><td>{{ $karyawan->email }}</td></tr>
          <tr><th>No. Telp</th><td>{{ $karyawan->no_telp ?? '-' }}</td></tr>
          <tr><th>Nama Cabang</th><td>{{ $karyawan->nama_cabang ?? '-' }}</td></tr>
          <tr><th>Alamat Cabang</th><td>{{ $karyawan->alamat_cabang ?? '-' }}</td></tr>
          <tr><th>Alamat</th><td>{{ $karyawan->alamat ?? '-' }}</td></tr>
          <tr><th>Status</th><td>
            @if ($karyawan->status == 'Active')
              <span class="label label-success">Aktif</span>
            @else
              <span class="label label-danger">Tidak Aktif</span>
            @endif
          </td></tr>
        </table>
        <a href="{{ route('karyawan.edit', $karyawan->id) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('karyawan.index') }}" class="btn btn-secondary">Kembali</a>
      </div>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Riwayat Transaksi ({{ $transaksi->count() }})</h4>
        <div class="table-responsive">
          <table class="table zero-configuration">
            <thead>
              <tr><th>#</th><th>Invoice</th><th>Tanggal</th><th>Customer</th><th>Status</th><th>Total</th></tr>
            </thead>
            <tbody>
              <?php $no = 1; ?>
              @foreach ($transaksi as $item)
              <tr>
                <td>{{ $no }}</td>
                <td>{{ $item->invoice }}</td>
                <td>{{ $item->tgl_transaksi }}</td>
                <td>{{ $item->customer }}</td>
                <td>{{ $item->status_order }}</td>
                <td>Rp {{ number_format($item->total_numeric ?? 0, 0, ',', '.') }}</td>
              </tr>
              <?php $no++; ?>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
