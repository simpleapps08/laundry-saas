@extends('layouts.backend')

@section('title', 'Merchant & Tagihan')
@section('header', 'Merchant')
@section('breadcrumb', 'Super Admin / Merchant')

@php
  $rp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
@endphp

@section('content')

@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap">
          <div>
            <h4 class="card-title mb-0">Daftar Merchant &amp; Rekap Tagihan</h4>
            <p class="text-muted mb-0" style="font-size:13px">
              Harga dihitung per cabang, dengan diskon bertingkat sesuai jumlah cabang merchant.
            </p>
          </div>
          <a href="{{ route('superadmin.merchant.create') }}" class="btn btn-primary">+ Tambah Merchant</a>
        </div>
      </div>
      <div class="card-content">
        <div class="card-body">

          {{-- ===== DESKTOP (>=768px): TABEL ===== --}}
          <div class="table-responsive d-none d-md-block">
            <table class="table table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th>Merchant</th>
                  <th class="text-center">Cabang</th>
                  <th>Paket Efektif</th>
                  <th class="text-end">Subtotal/bln</th>
                  <th class="text-center">Diskon</th>
                  <th class="text-end">Tagihan/bln</th>
                  <th class="text-end">Tertunggak</th>
                  <th style="width:130px">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($merchants as $m)
                  @php
                    $r = $m->rincianTagihan();
                    $rk = $rekap[$m->id] ?? null;
                  @endphp
                  <tr>
                    <td>
                      <a href="{{ route('superadmin.merchant.show', $m->id) }}"><strong>{{ $m->nama }}</strong></a><br>
                      <small class="text-muted">{{ $m->kode }} &middot; {{ $m->email ?? 'tanpa email' }}</small>
                    </td>
                    <td class="text-center">
                      {{ $m->cabangs->count() }}
                      @if ($m->jumlahLanggananAktif() > 0)
                        <br><small class="text-muted">{{ $m->jumlahLanggananAktif() }} langganan</small>
                      @endif
                    </td>
                    <td>
                      @php $pe = $m->paketEfektif(); @endphp
                      @if ($pe)
                        <span class="badge bg-primary">{{ $pe->nama }}</span>
                        <br><small class="text-muted">kuota {{ $pe->batas_cabang == -1 ? 'tanpa batas' : $pe->batas_cabang . ' cabang' }}</small>
                      @else
                        <span class="text-muted">&mdash;</span>
                      @endif
                    </td>
                    <td class="text-end">{{ $rp($r['subtotal']) }}</td>
                    <td class="text-center">
                      @if ($r['diskon_persen'] > 0)
                        <span class="badge bg-success">&minus;{{ rtrim(rtrim(number_format((float) $r['diskon_persen'], 2, '.', ''), '0'), '.') }}%</span>
                      @else
                        <small class="text-muted">&mdash;</small>
                      @endif
                    </td>
                    <td class="text-end"><strong>{{ $rp($r['total']) }}</strong></td>
                    <td class="text-end">
                      @if ($rk && $rk->tertunggak > 0)
                        <span class="text-danger">{{ $rp($rk->tertunggak) }}</span>
                      @else
                        <small class="text-muted">&mdash;</small>
                      @endif
                    </td>
                    <td>
                      <form method="POST" action="{{ route('superadmin.merchant.tagihan', $m->id) }}"
                            onsubmit="return confirm('Buat tagihan untuk semua cabang {{ $m->nama }}?')">
                        @csrf
                        <button class="btn btn-sm btn-primary" {{ $r['jumlah_cabang'] === 0 ? 'disabled' : '' }}>
                          Buat Tagihan
                        </button>
                      </form>
                    </td>
                  </tr>
                  @if (! empty($r['baris']))
                    <tr class="table-light">
                      <td colspan="8" style="padding:6px 12px">
                        <small class="text-muted">
                          Rincian:
                          @foreach ($r['baris'] as $b)
                            <span class="me-3">
                              {{ $b['cabang'] }} &middot; {{ $b['paket'] }} &middot; {{ $rp($b['harga_satuan']) }}
                              @if ($b['diskon_nilai'] > 0) &minus;{{ $rp($b['diskon_nilai']) }} @endif
                              = <strong>{{ $rp($b['total']) }}</strong>
                            </span>
                          @endforeach
                        </small>
                      </td>
                    </tr>
                  @endif
                @empty
                  <tr><td colspan="8" class="text-center text-muted">Belum ada merchant.</td></tr>
                @endforelse
              </tbody>
              @if ($merchants->count() > 0)
                <tfoot class="table-light">
                  <tr>
                    <td colspan="5" class="text-end"><strong>Total / bulan</strong></td>
                    <td class="text-end">
                      <strong>{{ $rp($merchants->sum(fn ($m) => $m->rincianTagihan()['total'])) }}</strong>
                    </td>
                    <td colspan="2"></td>
                  </tr>
                </tfoot>
              @endif
            </table>
          </div>

          {{-- ===== MOBILE (<768px): KARTU ===== --}}
          <div class="jv-cards-mobile">
            @forelse ($merchants as $m)
              @php
                $r = $m->rincianTagihan();
                $rk = $rekap[$m->id] ?? null;
                $pe = $m->paketEfektif();
              @endphp
              <div class="jv-mcard">
                <div class="jv-mcard-title">
                  <span>{{ $m->nama }}</span>
                  @if ($pe)<span class="badge bg-primary">{{ $pe->nama }}</span>@endif
                </div>
                <div class="jv-mcard-row"><span class="jv-mcard-label">Kode</span><span class="jv-mcard-value">{{ $m->kode }}</span></div>
                <div class="jv-mcard-row"><span class="jv-mcard-label">Email</span><span class="jv-mcard-value">{{ $m->email ?? 'tanpa email' }}</span></div>
                <div class="jv-mcard-row"><span class="jv-mcard-label">Cabang</span><span class="jv-mcard-value">{{ $m->cabangs->count() }}</span></div>
                <div class="jv-mcard-row"><span class="jv-mcard-label">Subtotal/bln</span><span class="jv-mcard-value">{{ $rp($r['subtotal']) }}</span></div>
                <div class="jv-mcard-row">
                  <span class="jv-mcard-label">Diskon</span>
                  <span class="jv-mcard-value">
                    @if ($r['diskon_persen'] > 0)&minus;{{ rtrim(rtrim(number_format((float) $r['diskon_persen'], 2, '.', ''), '0'), '.') }}%@else &mdash; @endif
                  </span>
                </div>
                <div class="jv-mcard-row"><span class="jv-mcard-label">Tagihan/bln</span><span class="jv-mcard-value" style="font-weight:700">{{ $rp($r['total']) }}</span></div>
                <div class="jv-mcard-row">
                  <span class="jv-mcard-label">Tertunggak</span>
                  <span class="jv-mcard-value">
                    @if ($rk && $rk->tertunggak > 0)<span class="text-danger">{{ $rp($rk->tertunggak) }}</span>@else &mdash; @endif
                  </span>
                </div>

                @if (! empty($r['baris']))
                  <div class="jv-mcard-row" style="display:block">
                    <div class="jv-mcard-label" style="margin-bottom:4px">Rincian</div>
                    @foreach ($r['baris'] as $b)
                      <div style="font-size:.8rem;color:var(--jv-grey-600);padding:2px 0">
                        {{ $b['cabang'] }} &middot; {{ $b['paket'] }} &middot; {{ $rp($b['harga_satuan']) }}
                        @if ($b['diskon_nilai'] > 0) &minus;{{ $rp($b['diskon_nilai']) }} @endif
                        = <strong>{{ $rp($b['total']) }}</strong>
                      </div>
                    @endforeach
                  </div>
                @endif

                <div class="jv-mcard-actions">
                  <a href="{{ route('superadmin.merchant.show', $m->id) }}" class="btn btn-sm btn-info" style="color:white">Detail</a>
                  <form method="POST" action="{{ route('superadmin.merchant.tagihan', $m->id) }}" style="display:contents"
                        onsubmit="return confirm('Buat tagihan untuk semua cabang {{ $m->nama }}?')">
                    @csrf
                    <button class="btn btn-sm btn-primary" {{ $r['jumlah_cabang'] === 0 ? 'disabled' : '' }}>Buat Tagihan</button>
                  </form>
                </div>
              </div>
            @empty
              <div class="jv-mcard-empty">Belum ada merchant.</div>
            @endforelse
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
