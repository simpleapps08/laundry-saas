@extends('layouts.backend')

@section('title', 'Setelan Diskon Langganan')
@section('header', 'Setelan Diskon Langganan')
@section('breadcrumb', 'Super Admin / Setelan')

@php
  $rp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
@endphp

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Diskon Bertingkat per Jumlah Cabang</h4>
        <p class="text-muted mb-0" style="font-size:13px">
          Harga langganan dihitung: <strong>harga paket &times; jumlah cabang &times; (1 &minus; diskon)</strong>.
          Diskon diambil dari tingkatan dengan <em>minimum cabang terbesar</em> yang tidak melebihi jumlah cabang merchant.
        </p>
      </div>
      <div class="card-content">
        <div class="card-body">
          @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('superadmin.setelan.simpan') }}">
            @csrf
            <div class="table-responsive">
              <table class="table table-bordered align-middle jv-hide-mobile">
                <thead class="table-light">
                  <tr>
                    <th style="width:140px">Min. Cabang</th>
                    <th style="width:140px">Diskon (%)</th>
                    <th>Label</th>
                    <th style="width:100px">Aktif</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($diskon as $i => $d)
                    <tr>
                      <td>
                        <input type="hidden" name="diskon[{{ $i }}][id]" value="{{ $d->id }}">
                        <input type="number" class="form-control" name="diskon[{{ $i }}][min_cabang]"
                               value="{{ $d->min_cabang }}" min="1" required>
                      </td>
                      <td>
                        <input type="number" step="0.01" class="form-control" name="diskon[{{ $i }}][diskon_persen]"
                               value="{{ rtrim(rtrim(number_format((float) $d->diskon_persen, 2, '.', ''), '0'), '.') }}" min="0" max="100" required>
                      </td>
                      <td>
                        <input type="text" class="form-control" name="diskon[{{ $i }}][label]"
                               value="{{ $d->label }}" maxlength="60">
                      </td>
                      <td class="text-center">
                        <input type="hidden" name="diskon[{{ $i }}][is_aktif]" value="0">
                        <input type="checkbox" name="diskon[{{ $i }}][is_aktif]" value="1"
                               {{ $d->is_aktif ? 'checked' : '' }}>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            {{-- Kartu diskon (mobile) --}}
            <div class="jv-cards-mobile">
              @foreach ($diskon as $i => $d)
                <div class="jv-mcard">
                  <div class="jv-mcard-title">
                    <span>Tingkatan {{ $i + 1 }}</span>
                    <label class="d-flex align-items-center gap-1" style="font-weight:400;font-size:13px">
                      <input type="hidden" name="diskon[{{ $i }}][is_aktif]" value="0">
                      <input type="checkbox" name="diskon[{{ $i }}][is_aktif]" value="1"
                             {{ $d->is_aktif ? 'checked' : '' }}> Aktif
                    </label>
                  </div>
                  <input type="hidden" name="diskon[{{ $i }}][id]" value="{{ $d->id }}">
                  <div class="mb-2">
                    <label class="jv-mcard-label d-block mb-1" style="font-size:12px">Min. Cabang</label>
                    <input type="number" class="form-control" name="diskon[{{ $i }}][min_cabang]"
                           value="{{ $d->min_cabang }}" min="1" required>
                  </div>
                  <div class="mb-2">
                    <label class="jv-mcard-label d-block mb-1" style="font-size:12px">Diskon (%)</label>
                    <input type="number" step="0.01" class="form-control" name="diskon[{{ $i }}][diskon_persen]"
                           value="{{ rtrim(rtrim(number_format((float) $d->diskon_persen, 2, '.', ''), '0'), '.') }}" min="0" max="100" required>
                  </div>
                  <div>
                    <label class="jv-mcard-label d-block mb-1" style="font-size:12px">Label</label>
                    <input type="text" class="form-control" name="diskon[{{ $i }}][label]"
                           value="{{ $d->label }}" maxlength="60">
                  </div>
                </div>
              @endforeach
            </div>
            <button type="submit" class="btn btn-primary">Simpan Setelan</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Pratinjau Harga (siklus bulanan)</h4>
      </div>
      <div class="card-content">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle jv-hide-mobile">
              <thead class="table-light">
                <tr>
                  <th>Paket</th>
                  <th class="text-end">Harga/cabang</th>
                  @foreach ([1, 2, 3, 4, 5] as $jml)
                    <th class="text-end">{{ $jml }} cabang</th>
                  @endforeach
                </tr>
              </thead>
              <tbody>
                @foreach ($paket as $p)
                  <tr>
                    <td><strong>{{ $p->nama }}</strong></td>
                    <td class="text-end">{{ $rp($p->harga_bulanan) }}</td>
                    @foreach ([1, 2, 3, 4, 5] as $jml)
                      @php $h = $pratinjau[$p->id][$jml] ?? null; @endphp
                      <td class="text-end">
                        @if ($h)
                          {{ $rp($h['total']) }}
                          @if ($h['diskon_nilai'] > 0)
                            <br><small class="text-success">&minus;{{ rtrim(rtrim(number_format((float) $h['diskon_persen'], 2, '.', ''), '0'), '.') }}%</small>
                          @endif
                        @else
                          &mdash;
                        @endif
                      </td>
                    @endforeach
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          {{-- Kartu pratinjau (mobile) --}}
          <div class="jv-cards-mobile">
            @foreach ($paket as $p)
              <div class="jv-mcard">
                <div class="jv-mcard-title">
                  <span>{{ $p->nama }}</span>
                  <span class="jv-mcard-value">{{ $rp($p->harga_bulanan) }}<small class="text-muted">/cabang</small></span>
                </div>
                @foreach ([1, 2, 3, 4, 5] as $jml)
                  @php $h = $pratinjau[$p->id][$jml] ?? null; @endphp
                  <div class="jv-mcard-row">
                    <span class="jv-mcard-label">{{ $jml }} cabang</span>
                    <span class="jv-mcard-value">
                      @if ($h)
                        {{ $rp($h['total']) }}
                        @if ($h['diskon_nilai'] > 0)
                          <small class="text-success">&minus;{{ rtrim(rtrim(number_format((float) $h['diskon_persen'], 2, '.', ''), '0'), '.') }}%</small>
                        @endif
                      @else &mdash; @endif
                    </span>
                  </div>
                @endforeach
              </div>
            @endforeach
          </div>
          <p class="text-muted mb-0" style="font-size:12px">
            Angka di atas sudah termasuk diskon bertingkat sesuai setelan di tabel atas.
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
