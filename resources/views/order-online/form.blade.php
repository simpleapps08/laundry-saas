@extends('layouts.order-online')

@section('title', 'Pesan Laundry Online — Javacom Laundry')
@section('header', 'Pesan Laundry')

@section('content')
<div class="oo-wrap">
  <div class="oo-card">
    <div class="oo-head">
      <h1 class="oo-title">Pesan Laundry Online</h1>
      <p class="oo-sub">Isi form ini, kami jemput cucian Anda. Tanpa perlu bikin akun.</p>
    </div>

    @if ($errors->any())
      <div class="oo-alert oo-alert-error">
        <strong>Periksa kembali:</strong>
        <ul>
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('order-online.kirim') }}" class="oo-form">
      @csrf

      <div class="oo-field">
        <label>Nama Anda <span class="oo-req">*</span></label>
        <input type="text" name="nama" value="{{ old('nama') }}" placeholder="cth: Budi Santoso" required>
      </div>

      <div class="oo-field">
        <label>Nomor WhatsApp <span class="oo-req">*</span></label>
        <input type="tel" name="no_telp" value="{{ old('no_telp') }}" placeholder="cth: 08123456789" required>
        <small class="oo-hint">Kami kirim notifikasi status cucian ke nomor ini.</small>
      </div>

      <div class="oo-field">
        <label>Cabang Laundry <span class="oo-req">*</span></label>
        <select name="cabang_id" required>
          <option value="">-- Pilih cabang --</option>
          @foreach ($cabangs as $c)
            <option value="{{ $c->id }}" {{ (old('cabang_id', $cabangId) == $c->id) ? 'selected' : '' }}>
              {{ $c->nama }}@if($c->alamat) — {{ $c->alamat }}@endif
            </option>
          @endforeach
        </select>
      </div>

      <div class="oo-field">
        <label>Alamat Lengkap <span class="oo-req">*</span></label>
        <textarea name="alamat" rows="3" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, patokan..." required>{{ old('alamat') }}</textarea>
      </div>

      <div class="oo-field">
        <label>Metode <span class="oo-req">*</span></label>
        <div class="oo-radio-row">
          <label class="oo-radio">
            <input type="radio" name="mode_layanan" value="pickup" {{ old('mode_layanan', $modeDefault) === 'pickup' ? 'checked' : '' }}>
            <span class="oo-radio-body">
              <b>Dijemput</b>
              <small>Kurir kami ambil cucian di rumah</small>
            </span>
          </label>
          <label class="oo-radio">
            <input type="radio" name="mode_layanan" value="dropoff" {{ old('mode_layanan', $modeDefault) === 'dropoff' ? 'checked' : '' }}>
            <span class="oo-radio-body">
              <b>Saya antar sendiri</b>
              <small>Anda antar langsung ke toko</small>
            </span>
          </label>
        </div>
      </div>

      <div class="oo-row2">
        <div class="oo-field">
          <label>Jenis Layanan <span class="oo-opt">(opsional)</span></label>
          <select name="jenis_pakaian">
            <option value="">-- Tidak tentukan --</option>
            <option value="Cuci Kering" {{ old('jenis_pakaian') === 'Cuci Kering' ? 'selected' : '' }}>Cuci Kering</option>
            <option value="Cuci Setrika" {{ old('jenis_pakaian') === 'Cuci Setrika' ? 'selected' : '' }}>Cuci Setrika</option>
            <option value="Setrika Saja" {{ old('jenis_pakaian') === 'Setrika Saja' ? 'selected' : '' }}>Setrika Saja</option>
          </select>
          <small class="oo-hint">Bisa disesuaikan saat ditimbang.</small>
        </div>

        <div class="oo-field">
          <label>Perkiraan Berat <span class="oo-opt">(opsional)</span></label>
          <input type="number" step="0.1" min="0" name="estimasi_kg" value="{{ old('estimasi_kg') }}" placeholder="cth: 3">
          <small class="oo-hint">Berat &amp; harga final ditentukan saat penimbangan.</small>
        </div>
      </div>

      <div class="oo-field">
        <label>Catatan <span class="oo-opt">(opsional)</span></label>
        <textarea name="catatan" rows="2" placeholder="cth: pisahkan pakaian putih, jangan pakai pewangi">{{ old('catatan') }}</textarea>
      </div>

      <div class="oo-note">
        Harga dihitung <b>setelah cucian ditimbang</b>. Biaya antar-jemput ditambahkan
        sesuai jarak. Kami akan kabari totalnya sebelum diproses.
      </div>

      <button type="submit" class="oo-btn">Kirim Pesanan</button>
      <p class="oo-foot">Sudah pernah pesan? <a href="{{ route('order-online.cek') }}">Cek status pesanan</a></p>
    </form>
  </div>
</div>
@endsection
