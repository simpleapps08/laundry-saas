@extends('layouts.order-online')

@section('title', 'Cek Status Pesanan — Javacom Laundry')

@section('content')
<div class="oo-wrap">
  <div class="oo-card">
    <div class="oo-head">
      <h1 class="oo-title">Cek Status Pesanan</h1>
      <p class="oo-sub">Masukkan kode pesanan Anda (contoh: PO-261010-A1B2C).</p>
    </div>

    <form method="GET" action="{{ route('order-online.cek') }}" class="oo-form">
      <div class="oo-field">
        <label>Kode Pesanan</label>
        <input type="text" name="kode" value="{{ $kode }}" placeholder="PO-xxxxxx-xxxxx" autofocus>
      </div>
      <button type="submit" class="oo-btn">Cek Status</button>
    </form>

    @if ($tidakDitemukan)
      <div class="oo-alert oo-alert-error" style="margin-top:16px">
        Kode pesanan <b>{{ $kode }}</b> tidak ditemukan. Periksa kembali, atau hubungi cabang laundry.
      </div>
    @endif

    @if ($pesanan)
      <hr style="border:none;border-top:1px solid var(--line);margin:22px 0">
      <div class="oo-note">
        Status: <span class="oo-badge">{{ $pesanan->status_online }}</span><br>
        Cabang: <b>{{ $pesanan->cabang->nama ?? '-' }}</b><br>
        Atas nama: <b>{{ $pesanan->nama }}</b>
      </div>
      <ul class="oo-steps">
        <li><b>Pesanan dibuat</b> — {{ $pesanan->created_at?->format('d M Y, H:i') }}</li>
        @if ($pesanan->mode_layanan === 'pickup')
          <li><b>Dijemput</b> — kurir mengambil cucian di alamat Anda</li>
        @else
          <li><b>Antar sendiri</b> — ke cabang {{ $pesanan->cabang->nama ?? '-' }}</li>
        @endif
        @if ($pesanan->status_online === 'Diproses')
          <li><b>Sudah ditimbang &amp; diproses</b> oleh cabang
            @if ($pesanan->transaksi)
              — invoice <b>{{ $pesanan->transaksi->invoice }}</b>
            @endif
          </li>
        @else
          <li><b>Menunggu ditimbang</b> oleh cabang</li>
        @endif
      </ul>
    @endif

    <p class="oo-foot" style="margin-top:22px"><a href="{{ route('order-online.form') }}">← Pesan laundry lagi</a></p>
  </div>
</div>
@endsection
