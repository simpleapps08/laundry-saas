@extends('layouts.backend')
@section('title','Admin - Detail')
@section('header','Detail Admin')
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
        <h4 class="card-title">Profil Admin</h4>
        <table class="table">
          <tr><th>Nama</th><td>{{ $admin->name }}</td></tr>
          <tr><th>Email</th><td>{{ $admin->email }}</td></tr>
          <tr><th>Nama Cabang</th><td>{{ $admin->nama_cabang ?? '-' }}</td></tr>
          <tr><th>Alamat Cabang</th><td>{{ $admin->alamat_cabang ?? '-' }}</td></tr>
          <tr><th>Alamat</th><td>{{ $admin->alamat ?? '-' }}</td></tr>
          <tr><th>No. Telp</th><td>{{ $admin->no_telp ?? '-' }}</td></tr>
          <tr><th>Status</th><td>
            @if ($admin->status == 'Active')
              <span class="label label-success">Aktif</span>
            @else
              <span class="label label-danger">Tidak Aktif</span>
            @endif
          </td></tr>
        </table>
        <a href="{{ route('admin.edit', $admin->id) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('admin.index') }}" class="btn btn-secondary">Kembali</a>
      </div>
    </div>
  </div>
</div>
@endsection
