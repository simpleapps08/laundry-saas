@extends('layouts.backend')

@section('title', 'Panel Super Admin')
@section('header', 'Panel Super Admin')
@section('breadcrumb', 'Super Admin')

@php
  $rp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
@endphp

@section('content')
<div class="row">
  {{-- KPI --}}
  <div class="col-lg-3 col-md-6 col-12">
    <div class="card">
      <div class="card-content">
        <div class="card-body">
          <h4 class="card-title mb-1">{{ $totalCabang }}</h4>
          <p class="card-text text-muted mb-0">Total Cabang</p>
          <small class="text-success">{{ $cabangAktif }} aktif</small>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-12">
    <div class="card">
      <div class="card-content">
        <div class="card-body">
          <h4 class="card-title mb-1">{{ $totalUser }}</h4>
          <p class="card-text text-muted mb-0">Total Pengguna</p>
          <small class="text-muted">lintas semua cabang</small>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-12">
    <div class="card">
      <div class="card-content">
        <div class="card-body">
          <h4 class="card-title mb-1">{{ $rp($mrr) }}</h4>
          <p class="card-text text-muted mb-0">Pendapatan Langganan</p>
          <small class="text-muted">per bulan (aktif)</small>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-12">
    <div class="card">
      <div class="card-content">
        <div class="card-body">
          <h4 class="card-title mb-1 {{ $nilaiTertunggak > 0 ? 'text-danger' : '' }}">
            {{ $rp($nilaiTertunggak) }}
          </h4>
          <p class="card-text text-muted mb-0">Tagihan Tertunggak</p>
          <small class="text-muted">{{ $tagihanTertunggak->count() }} tagihan</small>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  {{-- Pendapatan laundry seluruh cabang --}}
  <div class="col-lg-6 col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Pendapatan Laundry — Semua Cabang</h4>
      </div>
      <div class="card-content">
        <div class="card-body">
          <div class="d-flex justify-content-between mb-1">
            <span class="text-muted">Bulan ini</span>
            <strong>{{ $rp($pendapatanBulan) }}</strong>
          </div>
          <div class="d-flex justify-content-between">
            <span class="text-muted">Total keseluruhan</span>
            <strong>{{ $rp($totalPendapatan) }}</strong>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Langganan jatuh tempo --}}
  <div class="col-lg-6 col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Perlu Perhatian</h4>
      </div>
      <div class="card-content">
        <div class="card-body">
          @if ($jatuhTempo->isEmpty() && $akanBerakhir->isEmpty())
            <p class="text-muted mb-0">Semua langganan sehat.</p>
          @else
            @foreach ($jatuhTempo as $l)
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span>
                  <i class="feather icon-alert-circle text-danger"></i>
                  {{ $l->cabang?->nama }}
                </span>
                <span class="badge badge-danger">Jatuh tempo {{ $l->berakhir?->format('d/m/Y') }}</span>
              </div>
            @endforeach
            @foreach ($akanBerakhir as $l)
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span>
                  <i class="feather icon-clock text-warning"></i>
                  {{ $l->cabang?->nama }}
                </span>
                <span class="badge badge-warning">{{ $l->sisaHari() }} hari lagi</span>
              </div>
            @endforeach
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <h4 class="card-title">Tagihan Belum Lunas</h4>
        <a href="{{ url('super-admin/tagihan') }}" class="btn btn-sm btn-outline-primary">Lihat semua</a>
      </div>
      <div class="card-content">
        <div class="card-body">

          {{-- ===== DESKTOP (>=768px): TABEL ===== --}}
          <div class="table-responsive d-none d-md-block">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th>Nomor</th><th>Cabang</th><th>Jumlah</th>
                  <th>Jatuh Tempo</th><th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($tagihanTertunggak as $t)
                  <tr>
                    <td>{{ $t->nomor }}</td>
                    <td>{{ $t->cabang?->nama ?? '-' }}</td>
                    <td>{{ $rp($t->jumlah) }}</td>
                    <td class="{{ $t->terlambat() ? 'text-danger' : '' }}">
                      {{ $t->jatuh_tempo?->format('d/m/Y') }}
                      @if ($t->terlambat()) <span class="badge badge-danger">terlambat</span> @endif
                    </td>
                    <td><span class="badge badge-secondary">{{ $t->status }}</span></td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center text-muted">Tidak ada tagihan tertunggak</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>

          {{-- ===== MOBILE (<768px): KARTU ===== --}}
          <div class="jv-cards-mobile">
            @forelse ($tagihanTertunggak as $t)
              <div class="jv-mcard">
                <div class="jv-mcard-title">
                  <span>{{ $t->nomor }}</span>
                  <span class="badge badge-secondary">{{ $t->status }}</span>
                </div>
                <div class="jv-mcard-row"><span class="jv-mcard-label">Cabang</span><span class="jv-mcard-value">{{ $t->cabang?->nama ?? '-' }}</span></div>
                <div class="jv-mcard-row"><span class="jv-mcard-label">Jumlah</span><span class="jv-mcard-value" style="font-weight:700">{{ $rp($t->jumlah) }}</span></div>
                <div class="jv-mcard-row">
                  <span class="jv-mcard-label">Jatuh Tempo</span>
                  <span class="jv-mcard-value {{ $t->terlambat() ? 'text-danger' : '' }}">
                    {{ $t->jatuh_tempo?->format('d/m/Y') }}
                    @if ($t->terlambat()) <span class="badge badge-danger">terlambat</span> @endif
                  </span>
                </div>
              </div>
            @empty
              <div class="jv-mcard-empty">Tidak ada tagihan tertunggak</div>
            @endforelse
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
