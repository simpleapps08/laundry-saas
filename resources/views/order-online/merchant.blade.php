@extends('layouts.order-online')

@section('title', $merchant->nama.' — Pesan Laundry Online')
@section('header', $merchant->nama)

@section('content')
<div class="oo-wrap">

  <div class="oo-card oo-profile">
    <h1 class="oo-title">{{ $merchant->nama }}</h1>
    @if (!empty($merchant->alamat))
      <p class="oo-sub">{{ $merchant->alamat }}</p>
    @endif
    @if (!empty($merchant->no_telp))
      <p class="oo-sub">WhatsApp: {{ $merchant->no_telp }}</p>
    @endif

    <a href="{{ route('order-online.merchant.form', ['slug' => $merchant->slug]) }}"
       class="oo-btn oo-btn-inline">Pesan Laundry Online</a>
  </div>

  @if ($cabangs->count() > 0)
    <div class="oo-card" style="margin-top:18px;">
      <h2 class="oo-title" style="font-size:19px;">Cabang Kami</h2>
      <p class="oo-sub">Pilih cabang terdekat untuk memesan langsung.</p>

      <ul class="oo-cabang-list">
        @foreach ($cabangs as $c)
          <li>
            <div class="oo-cabang-info">
              <b>{{ $c->nama }}</b>
              @if ($c->alamat)<small>{{ $c->alamat }}</small>@endif
            </div>
            <a href="{{ route('order-online.cabang.form', ['slug' => $merchant->slug, 'kodeCabang' => $c->kode]) }}"
               class="oo-cabang-btn">Pesan</a>
          </li>
        @endforeach
      </ul>
    </div>
  @endif

  <p class="oo-foot" style="text-align:center;margin-top:22px;">
    <a href="{{ route('order-online.cek') }}">Cek status pesanan</a>
  </p>
</div>
@endsection
