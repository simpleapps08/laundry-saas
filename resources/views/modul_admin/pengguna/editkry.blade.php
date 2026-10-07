@extends('layouts.backend')
@section('title','Karyawan - Edit')
@section('header','Edit Karyawan')
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
        <h4 class="card-title">Edit Karyawan: {{ $karyawan->name }}</h4>
        <form action="{{ route('karyawan.update', $karyawan->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="form-group">
            <label>Nama <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $karyawan->name) }}">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $karyawan->email) }}">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Nama Cabang</label>
            <input type="text" name="nama_cabang" class="form-control" value="{{ old('nama_cabang', $karyawan->nama_cabang) }}">
          </div>
          <div class="form-group">
            <label>No. Telp</label>
            <input type="text" name="no_telp" class="form-control" value="{{ old('no_telp', $karyawan->no_telp) }}">
          </div>
          <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $karyawan->alamat) }}</textarea>
          </div>
          <div class="form-group">
            <label>Alamat Cabang</label>
            <textarea name="alamat_cabang" class="form-control" rows="3">{{ old('alamat_cabang', $karyawan->alamat_cabang) }}</textarea>
          </div>
          <div class="form-group">
            <label>Password Baru</label>
            <input type="password" name="password" class="form-control" placeholder="Kosongkan bila tidak diubah">
            <small class="text-muted">Isi hanya bila ingin mengganti password.</small>
          </div>
          <button type="submit" class="btn btn-primary">Perbarui</button>
          <a href="{{ route('karyawan.index') }}" class="btn btn-secondary">Batal</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
