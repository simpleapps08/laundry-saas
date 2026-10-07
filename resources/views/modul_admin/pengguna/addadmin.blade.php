@extends('layouts.backend')
@section('title','Admin - Tambah')
@section('header','Tambah Admin')
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
        <h4 class="card-title">Form Admin Baru</h4>
        <form action="{{ route('admin.store') }}" method="POST">
          @csrf
          <div class="form-group">
            <label>Nama <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Nama Cabang</label>
            <input type="text" name="nama_cabang" class="form-control" value="{{ old('nama_cabang') }}">
          </div>
          <div class="form-group">
            <label>No. Telp</label>
            <input type="text" name="no_telp" class="form-control" value="{{ old('no_telp') }}">
          </div>
          <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control" rows="3">{{ old('alamat') }}</textarea>
          </div>
          <div class="form-group">
            <label>Alamat Cabang</label>
            <textarea name="alamat_cabang" class="form-control" rows="3">{{ old('alamat_cabang') }}</textarea>
          </div>
          <div class="form-group">
            <label>Password <span class="text-danger">*</span></label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <button type="submit" class="btn btn-primary">Simpan</button>
          <a href="{{ route('admin.index') }}" class="btn btn-secondary">Batal</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
