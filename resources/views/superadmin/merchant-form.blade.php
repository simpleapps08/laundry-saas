@extends('layouts.backend')

@section('title', $merchant ? 'Ubah Merchant' : 'Tambah Merchant')
@section('header', $merchant ? 'Ubah Merchant' : 'Onboarding Merchant Baru')
@section('breadcrumb', 'Super Admin / Merchant / ' . ($merchant ? 'Ubah' : 'Tambah'))

@php
  $rp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
  $lama = fn ($k, $d = null) => old($k, $d);
@endphp

@section('content')
<div class="row">
  <div class="col-lg-8 col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">{{ $merchant ? 'Ubah Data Merchant' : 'Data Merchant Baru' }}</h4>
        @if (! $merchant)
          <p class="text-muted mb-0" style="font-size:13px">
            Satu kali simpan akan dibuat sekaligus: <strong>akun owner</strong>,
            <strong>merchant</strong>, <strong>cabang pertama</strong>, dan
            <strong>langganan trial 14 hari</strong>.
          </p>
        @endif
      </div>
      <div class="card-content">
        <div class="card-body">
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
              </ul>
            </div>
          @endif

          <form method="POST"
                action="{{ $merchant
                    ? route('superadmin.merchant.update', $merchant->id)
                    : route('superadmin.merchant.store') }}">
            @csrf
            @if ($merchant) @method('PUT') @endif

            <h5 class="mb-1">Data Merchant</h5>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Nama Usaha <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control"
                       value="{{ $lama('nama', $merchant->nama ?? '') }}" required>
              </div>
              <div class="form-group col-md-6">
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control"
                       value="{{ $lama('email', $merchant->email ?? '') }}"
                       {{ $merchant ? '' : 'required' }}>
                @if (! $merchant)
                  <small class="text-muted">Email ini juga jadi akun login owner.</small>
                @endif
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label>No. Telepon</label>
                <input type="text" name="no_telp" class="form-control"
                       value="{{ $lama('no_telp', $merchant->no_telp ?? '') }}">
              </div>
              <div class="form-group col-md-6">
                <label>Alamat</label>
                <input type="text" name="alamat" class="form-control"
                       value="{{ $lama('alamat', $merchant->alamat ?? '') }}">
              </div>
            </div>

            @if ($merchant)
              <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                  @foreach (['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif', 'suspended' => 'Suspended'] as $v => $t)
                    <option value="{{ $v }}" {{ ($merchant->status ?? '') === $v ? 'selected' : '' }}>{{ $t }}</option>
                  @endforeach
                </select>
                <small class="text-muted">Mengubah status juga mengubah status semua cabangnya.</small>
              </div>
            @endif

            @if (! $merchant)
              <hr>
              <h5 class="mb-1">Akun Owner</h5>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>Nama Owner</label>
                  <input type="text" name="owner_nama" class="form-control"
                         value="{{ $lama('owner_nama') }}" placeholder="Kosongkan = pakai nama usaha">
                </div>
                <div class="form-group col-md-3">
                  <label>Password <span class="text-danger">*</span></label>
                  <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="form-group col-md-3">
                  <label>Ulangi Password <span class="text-danger">*</span></label>
                  <input type="password" name="password_confirmation" class="form-control" required minlength="6">
                </div>
              </div>

              <hr>
              <h5 class="mb-1">Cabang Pertama &amp; Langganan</h5>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>Nama Cabang</label>
                  <input type="text" name="nama_cabang" class="form-control"
                         value="{{ $lama('nama_cabang') }}" placeholder="Kosongkan = [Nama Usaha] - Cabang Utama">
                </div>
                <div class="form-group col-md-3">
                  <label>Paket <span class="text-danger">*</span></label>
                  <select name="paket_id" class="form-control" required>
                    @foreach ($paket as $p)
                      <option value="{{ $p->id }}" {{ (string) old('paket_id') === (string) $p->id ? 'selected' : '' }}>
                        {{ $p->nama }} — {{ $rp($p->harga_bulanan) }}/bln
                        ({{ $p->batas_cabang == -1 ? '∞' : $p->batas_cabang }} cabang)
                      </option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group col-md-3">
                  <label>Siklus <span class="text-danger">*</span></label>
                  <select name="siklus" class="form-control" required>
                    <option value="bulanan" {{ old('siklus') === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    <option value="tahunan" {{ old('siklus') === 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                  </select>
                </div>
              </div>
            @endif

            <div class="form-group">
              <label>Catatan</label>
              <textarea name="catatan" class="form-control" rows="2">{{ $lama('catatan', $merchant->catatan ?? '') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">
              {{ $merchant ? 'Simpan Perubahan' : 'Buat Merchant' }}
            </button>
            <a href="{{ route('superadmin.merchant') }}" class="btn btn-outline-secondary">Batal</a>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
