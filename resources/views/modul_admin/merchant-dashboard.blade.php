@extends('layouts.backend')
@section('title','Dashboard Merchant')

@section('content')
<div class="row mb-1">
  <div class="col-12">
    <div class="card">
      <div class="card-body d-flex flex-wrap justify-content-between align-items-center">

        <div>
          <h4 class="card-title mb-25">
            @if(auth()->user()->merchant)
              {{ auth()->user()->merchant->nama }}
            @else
              Dashboard
            @endif
          </h4>
          <p class="mb-0 text-muted" style="font-size:13px">
            @if($mode === 'semua')
              Menampilkan <b>semua cabang</b> ({{ count($dilihat) }} cabang)
            @else
              Menampilkan <b>1 cabang</b>:
              {{ optional(collect($semuaCabang)->firstWhere('id', $cabangAktif))->nama ?? '-' }}
            @endif
          </p>
        </div>

        {{-- Pemilih cabang --}}
        @if(count($semuaCabang) > 1)
        <form method="POST" action="{{ route('merchant.ganti-cabang') }}" class="d-flex align-items-center mt-1 mt-md-0">
          @csrf
          <label class="mr-1 mb-0 text-muted" style="font-size:13px;white-space:nowrap">Lihat:</label>
          <select name="cabang_id" class="form-control form-control-sm" style="min-width:220px"
                  onchange="this.form.submit()">
            <option value="" {{ $cabangAktif === null ? 'selected' : '' }}>
              Semua Cabang ({{ count($semuaCabang) }})
            </option>
            @foreach($semuaCabang as $c)
              <option value="{{ $c->id }}" {{ (int)$cabangAktif === (int)$c->id ? 'selected' : '' }}>
                {{ $c->nama }}{{ $c->status !== 'aktif' ? ' ('.$c->status.')' : '' }}
              </option>
            @endforeach
          </select>
          <noscript><button class="btn btn-sm btn-primary ml-50">Ganti</button></noscript>
        </form>
        @endif

      </div>
    </div>
  </div>
</div>

@if(session('sukses'))
  <div class="alert alert-success" role="alert">{{ session('sukses') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
@endif

{{-- ── KARTU RINGKASAN ── --}}
<div class="row">
  @php
    $kartu = [
      ['label' => 'Total Pendapatan', 'nilai' => 'Rp '.number_format($ringkasan['pendapatan_total'],0,',','.'), 'ikon' => 'trending-up', 'warna' => 'success'],
      ['label' => 'Pendapatan Bulan Ini', 'nilai' => 'Rp '.number_format($ringkasan['pendapatan_bulan'],0,',','.'), 'ikon' => 'calendar', 'warna' => 'primary'],
      ['label' => 'Pendapatan Hari Ini',  'nilai' => 'Rp '.number_format($ringkasan['pendapatan_hari'],0,',','.'),  'ikon' => 'dollar-sign', 'warna' => 'info'],
      ['label' => 'Laundry Masuk',        'nilai' => number_format($ringkasan['masuk'],0,',','.'), 'ikon' => 'box', 'warna' => 'warning'],
      ['label' => 'Selesai',              'nilai' => number_format($ringkasan['selesai'],0,',','.'), 'ikon' => 'check-circle', 'warna' => 'success'],
      ['label' => 'Sudah Diambil',        'nilai' => number_format($ringkasan['diambil'],0,',','.'), 'ikon' => 'package', 'warna' => 'primary'],
      ['label' => 'Belum Dibayar',        'nilai' => number_format($ringkasan['belumbayar'],0,',','.'), 'ikon' => 'alert-circle', 'warna' => 'danger'],
      ['label' => 'Berat (kg) Bulan Ini', 'nilai' => number_format($ringkasan['kg_bulan'],2,',','.').' kg', 'ikon' => 'activity', 'warna' => 'info'],
    ];
  @endphp

  @foreach($kartu as $k)
  <div class="col-xl-3 col-lg-3 col-sm-6 col-12">
    <div class="card">
      <div class="card-header d-flex align-items-start pb-0">
        <div>
          <h2 class="text-bold-700 mb-0" style="font-size:1.35rem">{{ $k['nilai'] }}</h2>
          <p class="mb-0">{{ $k['label'] }}</p>
        </div>
        <div class="avatar bg-rgba-{{ $k['warna'] }} p-50 m-0">
          <div class="avatar-content">
            <i class="feather icon-{{ $k['ikon'] }} text-{{ $k['warna'] }} font-medium-5"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- ── PERBANDINGAN PER CABANG ── --}}
@if(count($perCabang) > 0)
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Perbandingan per Cabang</h4>
        <p class="card-text mb-0" style="font-size:13px">Angka pendapatan periode bulan ini ({{ date('m/Y') }}).</p>
      </div>
      <div class="card-body">
        <div class="table-responsive" style="max-height:520px;overflow:auto">
          <table class="table table-hover table-striped mb-0" style="min-width:820px;table-layout:fixed">
            <thead class="thead-light" style="position:sticky;top:0;z-index:2">
              <tr>
                <th style="width:26%">Cabang</th>
                <th style="width:11%" class="text-right">Pendapatan Bln</th>
                <th style="width:13%" class="text-right">Pendapatan Total</th>
                <th style="width:9%"  class="text-center">Masuk</th>
                <th style="width:9%"  class="text-center">Selesai</th>
                <th style="width:11%" class="text-right">Berat (kg)</th>
                <th style="width:11%" class="text-center">Belum Bayar</th>
                <th style="width:10%" class="text-center">Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($perCabang as $c)
              <tr>
                <td class="text-truncate" title="{{ $c['nama'] }}">
                  <a href="{{ route('merchant.ganti-cabang') }}"
                     onclick="event.preventDefault();document.getElementById('f-{{ $c['cabang_id'] }}').submit();">
                    {{ $c['nama'] }}
                  </a>
                  <form id="f-{{ $c['cabang_id'] }}" method="POST" action="{{ route('merchant.ganti-cabang') }}" class="d-none">
                    @csrf
                    <input type="hidden" name="cabang_id" value="{{ $c['cabang_id'] }}">
                  </form>
                  <div class="text-muted" style="font-size:11px">{{ $c['kode'] }}</div>
                </td>
                <td class="text-right text-bold-600">Rp {{ number_format($c['pendapatan_bulan'],0,',','.') }}</td>
                <td class="text-right">Rp {{ number_format($c['pendapatan_total'],0,',','.') }}</td>
                <td class="text-center">{{ $c['masuk'] }}</td>
                <td class="text-center">{{ $c['selesai'] }}</td>
                <td class="text-right">{{ number_format($c['kg_bulan'],2,',','.') }} kg</td>
                <td class="text-center">
                  @if($c['belumbayar'] > 0)
                    <span class="badge badge-danger">{{ $c['belumbayar'] }}</span>
                  @else
                    <span class="badge badge-success">0</span>
                  @endif
                </td>
                <td class="text-center">
                  @if($c['status'] === 'aktif')
                    <span class="badge badge-success">Aktif</span>
                  @else
                    <span class="badge badge-secondary">{{ ucfirst($c['status']) }}</span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
            <tfoot class="thead-light">
              <tr>
                <th>Total</th>
                <th class="text-right">Rp {{ number_format($ringkasan['pendapatan_bulan'],0,',','.') }}</th>
                <th class="text-right">Rp {{ number_format($ringkasan['pendapatan_total'],0,',','.') }}</th>
                <th class="text-center">{{ $ringkasan['masuk'] }}</th>
                <th class="text-center">{{ $ringkasan['selesai'] }}</th>
                <th class="text-right">{{ number_format($ringkasan['kg_bulan'],2,',','.') }} kg</th>
                <th class="text-center">{{ $ringkasan['belumbayar'] }}</th>
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div>
        <p class="text-muted mt-1 mb-0" style="font-size:12px">
          Klik nama cabang untuk memfilter dashboard ke cabang tersebut.
        </p>
      </div>
    </div>
  </div>
</div>

{{-- ── GRAFIK ── --}}
<div class="row">
  <div class="col-lg-6 col-12">
    <div class="card">
      <div class="card-header"><h4 class="card-title">Transaksi Harian (bulan ini)</h4></div>
      <div class="card-body">
        <div id="grafik-harian"></div>
      </div>
    </div>
  </div>
  <div class="col-lg-6 col-12">
    <div class="card">
      <div class="card-header"><h4 class="card-title">Pendapatan per Bulan ({{ date('Y') }})</h4></div>
      <div class="card-body">
        <div id="grafik-bulanan"></div>
      </div>
    </div>
  </div>
</div>
@endif
@endsection

@section('scripts')
@if(count($perCabang) > 0)
<script>
  var $tosca = '#0d9488', $toscaLight = '#5eead4';

  var opsiDasar = {
    chart: { type: 'area', height: 280, toolbar: { show: false } },
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: .45, opacityTo: .05 } },
    colors: [$tosca],
    grid: { borderColor: '#e2e8f0' },
    xaxis: { categories: '{{ $grafikHarian['label'] }}'.split(',') },
    yaxis: { labels: { formatter: function (v) { return Math.round(v); } } }
  };
  new ApexCharts(document.querySelector('#grafik-harian'), Object.assign({}, opsiDasar, {
    series: [{ name: 'Transaksi', data: '{{ $grafikHarian['nilai'] }}'.split(',').map(Number) }]
  })).render();

  new ApexCharts(document.querySelector('#grafik-bulanan'), {
    chart: { type: 'bar', height: 280, toolbar: { show: false } },
    plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
    dataLabels: { enabled: false },
    colors: [$tosca],
    grid: { borderColor: '#e2e8f0' },
    series: [{ name: 'Pendapatan (Rp)', data: '{{ $grafikBulanan['pendapatan'] }}'.split(',').map(Number) }],
    xaxis: { categories: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'] },
    yaxis: { labels: { formatter: function (v) { return 'Rp ' + Number(v).toLocaleString('id-ID'); } } },
    tooltip: { y: { formatter: function (v) { return 'Rp ' + Number(v).toLocaleString('id-ID'); } } }
  }).render();
</script>
@endif
@endsection
