@extends('layouts.backend')

@section('title', 'Daftar Paket')

@php $rp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.'); @endphp

@section('content')
<div class="row">
  @foreach ($paket as $p)
    <div class="col-lg-4 col-md-6 col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <h4 class="card-title mb-0">{{ $p->nama }}</h4>
          <span class="badge badge-{{ $p->is_aktif ? 'success' : 'secondary' }}">
            {{ $p->is_aktif ? 'aktif' : 'nonaktif' }}
          </span>
        </div>
        <div class="card-content">
          <div class="card-body">
            <h3 class="mb-0">{{ $rp($p->harga_bulanan) }}<small class="text-muted">/bln</small></h3>
            <small class="text-muted d-block mb-2">{{ $rp($p->harga_tahunan) }}/tahun</small>

            <p class="text-muted">{{ $p->deskripsi }}</p>

            <hr>
            <p class="mb-1"><strong>Batas:</strong></p>
            <ul class="pl-2 mb-2" style="list-style:none">
              <li>Cabang: {{ $p->batas_cabang === -1 ? 'tanpa batas' : $p->batas_cabang }}</li>
              <li>User: {{ $p->batas_user === -1 ? 'tanpa batas' : $p->batas_user }}</li>
              <li>Transaksi/bln: {{ $p->batas_transaksi_bulanan === -1 ? 'tanpa batas' : $p->batas_transaksi_bulanan }}</li>
            </ul>

            <p class="mb-1"><strong>Fitur:</strong></p>
            @foreach (($p->fitur ?? []) as $nama => $aktif)
              <div>
                <i class="feather icon-{{ $aktif ? 'check text-success' : 'x text-muted' }}"></i>
                <small class="{{ $aktif ? '' : 'text-muted' }}">{{ str_replace('_',' ', $nama) }}</small>
              </div>
            @endforeach

            <hr>
            <small class="text-muted">Dipakai {{ $pemakai[$p->id] ?? 0 }} cabang</small>
          </div>
        </div>
      </div>
    </div>
  @endforeach
</div>
@endsection
