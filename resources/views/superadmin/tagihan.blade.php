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
      <div class="table-responsive">
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
    </div>
  </div>
</div>
@endsection
