@extends('layouts.order-online')

@section('title', 'Pesanan Diterima — Javacom Laundry')

@section('content')
<div class="oo-wrap">
  <div class="oo-card">
    <div class="oo-head">
      <h1 class="oo-title">✅ Pesanan Diterima!</h1>
      <p class="oo-sub">Terima kasih, {{ $pesanan->nama }}. Simpan kode di bawah untuk cek status.</p>
    </div>

    <div class="oo-kode">{{ $pesanan->kode_pesanan }}</div>

    <div class="oo-alert oo-alert-ok">
      <b>Langkah selanjutnya:</b>
    </div>
    <ul class="oo-steps">
      <li><b>Kami hubungi Anda</b> lewat WhatsApp {{ $pesanan->no_telp }} untuk konfirmasi.</li>
      @if ($pesanan->mode_layanan === 'pickup')
        <li><b>Kurir menjemput</b> cucian di: {{ $pesanan->alamat }}</li>
      @else
        <li><b>Silakan antar</b> cucian Anda ke cabang <b>{{ $pesanan->cabang->nama ?? '-' }}</b>.</li>
      @endif
      <li><b>Ditimbang</b> — berat &amp; harga final ditentukan di sini.</li>
      <li><b>Total dikabari</b> sebelum cucian diproses.</li>
    </ul>

    <div class="oo-note">
      Status pesanan: <span class="oo-badge">{{ $pesanan->status_online }}</span><br>
      Cabang: <b>{{ $pesanan->cabang->nama ?? '-' }}</b>
    </div>

    <a href="{{ route('order-online.cek', ['kode' => $pesanan->kode_pesanan]) }}" class="oo-btn" style="display:block;text-align:center;text-decoration:none">Cek Status Pesanan</a>
  </div>
</div>
@endsection
