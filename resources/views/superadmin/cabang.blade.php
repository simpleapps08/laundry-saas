@extends('layouts.backend')

@section('title', 'Kelola Cabang')
@section('header', 'Kelola Cabang')
@section('breadcrumb', 'Super Admin / Cabang')

@php $rp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.'); @endphp

@section('content')
<div class="card">
  <div class="card-header">
    <h4 class="card-title">Daftar Cabang ({{ $cabang->count() }})</h4>
  </div>
  <div class="card-content">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Kode</th><th>Nama</th><th>Paket</th>
              <th>User</th><th>Transaksi</th><th>Pendapatan</th>
              <th>Berakhir</th><th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($cabang as $c)
              @php
                $s = $stat[$c->id] ?? null;
                // pilih langganan yang masih berlaku dari koleksi ter-eager-load
                $l = $c->langganan
                    ->filter(fn ($x) => in_array($x->status, ['trial', 'aktif']))
                    ->first();
              @endphp
              <tr>
                <td><code>{{ $c->kode }}</code></td>
                <td>
                  <strong>{{ $c->nama }}</strong>
                  @if ($c->alamat) <br><small class="text-muted">{{ $c->alamat }}</small> @endif
                </td>
                <td>
                  @if ($l?->paket)
                    <span class="badge badge-primary">{{ $l->paket->nama }}</span>
                  @else
                    <span class="badge badge-secondary">tanpa paket</span>
                  @endif
                </td>
                <td>{{ $c->jumlah_user }}</td>
                <td>{{ $s->jml ?? 0 }}</td>
                <td>{{ $rp($s->total ?? 0) }}</td>
                <td>
                  @if ($l?->berakhir)
                    {{ $l->berakhir->format('d/m/Y') }}
                    @php $sisa = $l->sisaHari(); @endphp
                    @if ($sisa >= 0 && $sisa <= 30)
                      <br><small class="text-warning">{{ $sisa }} hari lagi</small>
                    @elseif ($sisa < 0)
                      <br><small class="text-danger">lewat {{ abs($sisa) }} hari</small>
                    @endif
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  <span class="badge badge-{{ $c->status === 'aktif' ? 'success' : 'secondary' }}">
                    {{ $c->status }}
                  </span>
                </td>
              </tr>
            @empty
              <tr><td colspan="8" class="text-center text-muted">Belum ada cabang</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
