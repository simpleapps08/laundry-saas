@extends('layouts.backend')

@section('title', 'Detail Merchant')
@section('header', 'Detail Merchant')
@section('breadcrumb', 'Super Admin / Merchant / ' . $merchant->nama)

@php
  $rp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
  $badge = ['aktif' => 'success', 'nonaktif' => 'secondary', 'suspended' => 'danger'];
@endphp

@section('content')

@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
  </div>
@endif

{{-- ── Ringkasan ── --}}
<div class="row">
  <div class="col-lg-8 col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-start flex-wrap">
        <div>
          <h4 class="card-title mb-0">{{ $merchant->nama }}</h4>
          <small class="text-muted">{{ $merchant->kode }} &middot; {{ $merchant->email ?? 'tanpa email' }}
            &middot; {{ $merchant->no_telp ?? 'tanpa telepon' }}</small><br>
          <span class="badge bg-{{ $badge[$merchant->status] ?? 'secondary' }} mt-50">{{ strtoupper($merchant->status) }}</span>
        </div>
        <div>
          <a href="{{ route('superadmin.merchant.edit', $merchant->id) }}" class="btn btn-sm btn-outline-primary">Ubah</a>
          <a href="{{ route('superadmin.merchant') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
        </div>
      </div>
      <div class="card-content">
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <table class="table table-sm table-borderless mb-0">
                <tr><td width="130">Alamat</td><td><strong>{{ $merchant->alamat ?? '—' }}</strong></td></tr>
                <tr><td>Pemilik</td><td><strong>{{ $merchant->pemilik->name ?? '—' }}</strong></td></tr>
                <tr><td>Paket Efektif</td>
                    <td><strong>{{ $merchant->paketEfektif()->nama ?? '—' }}</strong></td></tr>
                <tr><td>Kuota Cabang</td>
                    <td><strong>{{ $merchant->batasCabang() == -1 ? 'Tanpa batas' : $merchant->batasCabang() . ' cabang' }}</strong>
                        <small class="text-muted">(terpakai {{ $merchant->jumlahCabang() }})</small></td></tr>
              </table>
            </div>
            <div class="col-md-6">
              <table class="table table-sm table-borderless mb-0">
                <tr><td width="130">Jml Cabang</td><td><strong>{{ $merchant->cabangs->count() }}</strong></td></tr>
                <tr><td>Langganan Aktif</td><td><strong>{{ $merchant->jumlahLanggananAktif() }}</strong></td></tr>
                <tr><td>Diskon</td>
                    <td><strong>{{ (float) $rincian['diskon_persen'] > 0
                        ? rtrim(rtrim(number_format((float) $rincian['diskon_persen'], 2, '.', ''), '0'), '.') . '%'
                        : '—' }}</strong></td></tr>
                <tr><td>Tagihan / bulan</td>
                    <td><strong class="text-primary">{{ $rp($rincian['total']) }}</strong></td></tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-12">
    <div class="card">
      <div class="card-header"><h4 class="card-title">Aksi</h4></div>
      <div class="card-content">
        <div class="card-body">
          <form method="POST" action="{{ route('superadmin.merchant.status', $merchant->id) }}" class="mb-1">
            @csrf @method('PATCH')
            <label class="mb-25" style="font-size:13px">Ubah Status</label>
            <div class="input-group">
              <select name="status" class="form-control">
                @foreach (['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif', 'suspended' => 'Suspended'] as $v => $t)
                  <option value="{{ $v }}" {{ $merchant->status === $v ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
              </select>
              <button class="btn btn-outline-primary">Simpan</button>
            </div>
          </form>

          <form method="POST" action="{{ route('superadmin.merchant.tagihan', $merchant->id) }}"
                onsubmit="return confirm('Buat tagihan untuk semua cabang {{ $merchant->nama }}?')">
            @csrf
            <button class="btn btn-primary w-100" {{ $rincian['jumlah_cabang'] === 0 ? 'disabled' : '' }}>
              Buat Tagihan Bulan Ini
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ── Rincian harga ── --}}
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Rincian Tagihan per Cabang</h4>
        <small class="text-muted">
          {{ $rincian['jumlah_cabang'] }} cabang &middot; subtotal {{ $rp($rincian['subtotal']) }}
          &middot; diskon {{ $rp($rincian['diskon_nilai']) }}
          &middot; <strong>total {{ $rp($rincian['total']) }}</strong>
        </small>
      </div>
      <div class="card-content">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Cabang</th><th>Paket</th><th>Status</th><th>Siklus</th>
                  <th class="text-end">Harga</th><th class="text-end">Diskon</th><th class="text-end">Total</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($rincian['baris'] as $b)
                  <tr>
                    <td><strong>{{ $b['cabang'] }}</strong></td>
                    <td>{{ $b['paket'] }}</td>
                    <td><span class="badge bg-info">{{ $b['status'] }}</span></td>
                    <td>{{ $b['siklus'] }}</td>
                    <td class="text-end">{{ $rp($b['harga_satuan']) }}</td>
                    <td class="text-end text-success">
                      @if ($b['diskon_nilai'] > 0) &minus;{{ $rp($b['diskon_nilai']) }} @else — @endif
                    </td>
                    <td class="text-end"><strong>{{ $rp($b['total']) }}</strong></td>
                  </tr>
                @empty
                  <tr><td colspan="7" class="text-center text-muted">Belum ada langganan aktif.</td></tr>
                @endforelse
              </tbody>
              @if (! empty($rincian['baris']))
                <tfoot class="table-light">
                  <tr>
                    <td colspan="4" class="text-end"><strong>Subtotal</strong></td>
                    <td class="text-end"><strong>{{ $rp($rincian['subtotal']) }}</strong></td>
                    <td class="text-end text-success"><strong>&minus;{{ $rp($rincian['diskon_nilai']) }}</strong></td>
                    <td class="text-end"><strong>{{ $rp($rincian['total']) }}</strong></td>
                  </tr>
                </tfoot>
              @endif
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ── Cabang ── --}}
<div class="row">
  <div class="col-lg-7 col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title mb-0">Cabang ({{ $merchant->cabangs->count() }})</h4>
      </div>
      <div class="card-content">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle mb-0">
              <thead class="table-light">
                <tr><th>Kode</th><th>Nama</th><th>Status</th><th class="text-end">Langganan</th></tr>
              </thead>
              <tbody>
                @forelse ($merchant->cabangs as $c)
                  <tr>
                    <td><small>{{ $c->kode }}</small></td>
                    <td>{{ $c->nama }}</td>
                    <td><span class="badge bg-{{ $c->status === 'aktif' ? 'success' : 'secondary' }}">{{ $c->status }}</span></td>
                    <td class="text-end">
                      @php $la = $c->langgananAktif(); @endphp
                      @if ($la)
                        {{ $la->paket->nama ?? '—' }}
                        <small class="text-muted">({{ $la->status }})</small>
                      @else
                        <small class="text-muted">—</small>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center text-muted">Belum ada cabang.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>

          {{-- Form tambah cabang (cek kuota) --}}
          @if ($merchant->bolehTambahCabang())
            <hr>
            <h6>Tambah Cabang</h6>
            <form method="POST" action="{{ route('superadmin.merchant.tambah-cabang', $merchant->id) }}">
              @csrf
              <div class="form-row">
                <div class="form-group col-md-4">
                  <input type="text" name="nama" class="form-control" placeholder="Nama cabang" required>
                </div>
                <div class="form-group col-md-3">
                  <input type="text" name="no_telp" class="form-control" placeholder="No. telepon">
                </div>
                <div class="form-group col-md-3">
                  <input type="text" name="alamat" class="form-control" placeholder="Alamat">
                </div>
                <div class="form-group col-md-2">
                  <button class="btn btn-outline-primary w-100">Tambah</button>
                </div>
              </div>
            </form>
          @else
            <div class="alert alert-warning mb-0 mt-1">
              Kuota cabang habis ({{ $merchant->batasCabang() }}).
              Naikkan paket langganan untuk menambah cabang.
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- ── Tagihan ── --}}
  <div class="col-lg-5 col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title mb-0">Riwayat Tagihan ({{ $tagihan->count() }})</h4>
      </div>
      <div class="card-content">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle mb-0">
              <thead class="table-light">
                <tr><th>Nomor</th><th class="text-end">Jumlah</th><th>Status</th></tr>
              </thead>
              <tbody>
                @forelse ($tagihan as $t)
                  <tr>
                    <td><small>{{ $t->nomor }}</small><br>
                        <small class="text-muted">{{ $t->cabang->nama ?? '—' }}</small></td>
                    <td class="text-end">{{ $rp($t->jumlah) }}</td>
                    <td>
                      @php
                        $sb = ['lunas' => 'success', 'belum_bayar' => 'warning',
                               'menunggu_verifikasi' => 'info', 'batal' => 'secondary'][$t->status] ?? 'secondary';
                      @endphp
                      <span class="badge bg-{{ $sb }}">{{ str_replace('_', ' ', $t->status) }}</span>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="3" class="text-center text-muted">Belum ada tagihan.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
