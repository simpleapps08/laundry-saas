@extends('layouts.order-online')

@section('title', 'Cek Status Pesanan — Javacom Laundry')

@php
  // Tentukan langkah alur berdasarkan status transaksi asli (bila sudah diproses).
  $steps = [
    'dibuat'   => ['Pesanan Dibuat',   false],
    'dijemput' => ['Dijemput Kurir',   false],
    'proses'   => ['Sedang Diproses',  false],
    'selesai'  => ['Selesai Dicuci',   false],
    'antar'    => ['Diantar / Siap',   false],
    'tuntas'   => ['Diterima',         false],
  ];
  $urutan = ['dibuat','dijemput','proses','selesai','antar','tuntas'];

  if ($pesanan) {
      $steps['dibuat'][1] = true;
      $so = optional($pesanan->transaksi)->status_order;
      $pickup = $pesanan->mode_layanan === 'pickup';

      if ($so === 'Dijemput') { $steps['dijemput'][1] = true; }
      elseif ($so === 'Process') { $steps['dijemput'][1] = $pickup; $steps['proses'][1] = true; }
      elseif ($so === 'Done') {
          $steps['dijemput'][1] = $pickup; $steps['proses'][1] = true; $steps['selesai'][1] = true;
      } elseif ($so === 'Diantar') {
          $steps['dijemput'][1]=$pickup; $steps['proses'][1]=true; $steps['selesai'][1]=true; $steps['antar'][1]=true;
      } elseif ($so === 'Delivery') {
          foreach (['dijemput','proses','selesai','antar','tuntas'] as $k) $steps[$k][1] = true;
          $steps['dijemput'][1] = $pickup;
      } elseif ($so === 'Batal') {
          $steps = []; // tampilkan pesan batal
      }
  }
@endphp

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
        @if(optional($pesanan->transaksi)->invoice)
          <br>Invoice: <b>{{ $pesanan->transaksi->invoice }}</b>
        @endif
      </div>

      @if(optional($pesanan->transaksi)->status_order === 'Batal' || $pesanan->status_online === 'Dibatalkan')
        <div class="oo-alert oo-alert-error">Pesanan ini telah dibatalkan.</div>
      @else
        <ul class="oo-steps">
          @foreach ($urutan as $k)
            @if (!isset($steps[$k]))
              @continue
            @endif
            @php [$label, $done] = $steps[$k]; @endphp
            <li style="{{ $done ? '' : 'opacity:.45' }}">
              <b>{{ $label }}</b>
              @if ($done) <span style="color:var(--tosca-dark)">✓</span> @endif
            </li>
          @endforeach
        </ul>
        <p class="oo-hint">Progres diperbarui otomatis mengikuti status di cabang.</p>
      @endif
    @endif

    <p class="oo-foot" style="margin-top:22px"><a href="{{ route('order-online.form') }}">← Pesan laundry lagi</a></p>
  </div>
</div>
@endsection
