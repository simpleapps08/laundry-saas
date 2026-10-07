@extends('layouts.backend')
@section('title','Customer - Tambah')
@section('header','Tambah Customer')
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
        <h4 class="card-title">Form Customer Baru</h4>
        <form action="{{ route('customer.store') }}" method="POST">
          @csrf
          <div class="form-group">
            <label>Nama <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Nama Customer">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Email">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>No. Telp</label>
            <input type="text" name="no_telp" class="form-control" value="{{ old('no_telp') }}" placeholder="08xxxx">
          </div>
          <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control" rows="3">{{ old('alamat') }}</textarea>
          </div>
          <div class="form-group">
            <label>Password <span class="text-danger">*</span></label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <button type="submit" class="btn btn-primary">Simpan</button>
          <a href="{{ route('customer.index') }}" class="btn btn-secondary">Batal</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
