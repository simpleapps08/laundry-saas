@extends('layouts.backend')

@section('title', 'Daftar Tagihan')

@php $rp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.'); @endphp

@section('content')
<div class="card">
  <div class="card-header">
    <h4 class="card-title">Tagihan Langganan ({{ $tagihan->count() }})</h4>
  </div>
  <div class="card-content">
    <div class="card-body">

      {{-- ===== DESKTOP (>=768px): TABEL ===== --}}
      <div class="table-responsive d-none d-md-block">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Nomor</th><th>Cabang</th><th>Paket</th><th>Jumlah</th>
              <th>Periode</th><th>Jatuh Tempo</th><th>Status</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($tagihan as $t)
              <tr>
                <td><code>{{ $t->nomor }}</code></td>
                <td>{{ $t->cabang?->nama ?? '-' }}</td>
                <td>{{ $t->langganan?->paket?->nama ?? '-' }}</td>
                <td>{{ $rp($t->jumlah) }}</td>
                <td><small>{{ $t->periode_mulai?->format('d/m/Y') }} — {{ $t->periode_akhir?->format('d/m/Y') }}</small></td>
                <td class="{{ $t->terlambat() ? 'text-danger' : '' }}">
                  {{ $t->jatuh_tempo?->format('d/m/Y') }}
                </td>
                <td>
                  @php
                    $warna = match($t->status) {
                      'lunas' => 'success', 'menunggu_verifikasi' => 'info',
                      'batal' => 'secondary', default => 'warning',
                    };
                  @endphp
                  <span class="badge badge-{{ $warna }}">{{ $t->status }}</span>
                </td>
                <td>
                  @if (! $t->sudahLunas())
                    <form method="POST" action="{{ url('super-admin/tagihan/lunas') }}" class="d-inline">
                      @csrf
                      <input type="hidden" name="id" value="{{ $t->id }}">
                      <input type="hidden" name="metode_bayar" value="transfer">
                      <button class="btn btn-sm btn-success">Tandai Lunas</button>
                    </form>
                  @else
                    <small class="text-muted">{{ $t->dibayar_pada?->format('d/m/Y') }}</small>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="8" class="text-center text-muted">Belum ada tagihan</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- ===== MOBILE (<768px): KARTU ===== --}}
      <div class="jv-cards-mobile">
        @forelse ($tagihan as $t)
          @php
            $warna = match($t->status) {
              'lunas' => 'success', 'menunggu_verifikasi' => 'info',
              'batal' => 'secondary', default => 'warning',
            };
          @endphp
          <div class="jv-mcard">
            <div class="jv-mcard-title">
              <span>{{ $t->nomor }}</span>
              <span class="badge badge-{{ $warna }}">{{ $t->status }}</span>
            </div>
            <div class="jv-mcard-row"><span class="jv-mcard-label">Cabang</span><span class="jv-mcard-value">{{ $t->cabang?->nama ?? '-' }}</span></div>
            <div class="jv-mcard-row"><span class="jv-mcard-label">Paket</span><span class="jv-mcard-value">{{ $t->langganan?->paket?->nama ?? '-' }}</span></div>
            <div class="jv-mcard-row"><span class="jv-mcard-label">Jumlah</span><span class="jv-mcard-value" style="font-weight:700">{{ $rp($t->jumlah) }}</span></div>
            <div class="jv-mcard-row"><span class="jv-mcard-label">Periode</span><span class="jv-mcard-value">{{ $t->periode_mulai?->format('d/m/Y') }} — {{ $t->periode_akhir?->format('d/m/Y') }}</span></div>
            <div class="jv-mcard-row">
              <span class="jv-mcard-label">Jatuh Tempo</span>
              <span class="jv-mcard-value {{ $t->terlambat() ? 'text-danger' : '' }}">{{ $t->jatuh_tempo?->format('d/m/Y') }}</span>
            </div>
            @if (! $t->sudahLunas())
              <div class="jv-mcard-actions">
                <form method="POST" action="{{ url('super-admin/tagihan/lunas') }}" style="display:contents">
                  @csrf
                  <input type="hidden" name="id" value="{{ $t->id }}">
                  <input type="hidden" name="metode_bayar" value="transfer">
                  <button class="btn btn-sm btn-success">Tandai Lunas</button>
                </form>
              </div>
            @else
              <div class="jv-mcard-row"><span class="jv-mcard-label">Dibayar</span><span class="jv-mcard-value">{{ $t->dibayar_pada?->format('d/m/Y') }}</span></div>
            @endif
          </div>
        @empty
          <div class="jv-mcard-empty">Belum ada tagihan</div>
        @endforelse
      </div>

    </div>
  </div>
</div>
@endsection
