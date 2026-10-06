@extends('layouts.backend')

@section('title', 'Fitur Terkunci')
@section('header', 'Fitur Belum Tersedia')
@section('breadcrumb', 'Fitur Terkunci')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-7">
    <div class="card">
      <div class="card-content">
        <div class="card-body text-center py-5">
          <i class="feather icon-lock" style="font-size:56px;color:#ff9f43;"></i>
          <h4 class="mt-2 mb-1">Fitur Belum Tersedia</h4>
          <p class="text-muted mb-3">
            Fitur <code>{{ $fitur ?? 'ini' }}</code> tidak termasuk dalam paket langganan
            yang sedang aktif.
          </p>

          @if (auth()->user()->cabang?->langgananAktif()?->paket)
            <p class="mb-4">
              Paket Anda saat ini: <strong>{{ auth()->user()->cabang->langgananAktif()->paket->nama }}</strong>
            </p>
          @else
            <p class="mb-4 text-warning">Cabang Anda belum memiliki langganan aktif.</p>
          @endif

          <a href="{{ url('home') }}" class="btn btn-primary">Kembali ke Dashboard</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
