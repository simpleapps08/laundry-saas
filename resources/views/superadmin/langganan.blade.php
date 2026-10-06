@extends('layouts.backend')

@section('title', 'Paket & Langganan')

@section('content')
<div class="row">
  {{-- Form set langganan --}}
  <div class="col-lg-4 col-12">
    <div class="card">
      <div class="card-header"><h4 class="card-title">Set Langganan Cabang</h4></div>
      <div class="card-content">
        <div class="card-body">
          <form method="POST" action="{{ url('super-admin/langganan/simpan') }}">
            @csrf
            <div class="form-group">
              <label>Cabang</label>
              <select name="cabang_id" class="form-control" required>
                <option value="">-- pilih cabang --</option>
                @foreach (\App\Models\Cabang::orderBy('nama')->get() as $c)
                  <option value="{{ $c->id }}">{{ $c->nama }} ({{ $c->kode }})</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label>Paket</label>
              <select name="paket_id" class="form-control" required>
                @foreach ($paket as $p)
                  <option value="{{ $p->id }}">{{ $p->nama }} — Rp {{ number_format($p->harga_bulanan,0,',','.') }}/bln</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label>Siklus</label>
              <select name="siklus" class="form-control">
                <option value="bulanan">Bulanan</option>
                <option value="tahunan">Tahunan</option>
              </select>
            </div>
            <div class="form-group">
              <label>Status</label>
              <select name="status" class="form-control">
                <option value="trial">Trial</option>
                <option value="aktif">Aktif</option>
                <option value="jatuh_tempo">Jatuh Tempo</option>
                <option value="berhenti">Berhenti</option>
              </select>
            </div>
            <div class="form-group">
              <label>Mulai</label>
              <input type="date" name="mulai" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="form-group">
              <label>Berakhir</label>
              <input type="date" name="berakhir" class="form-control" value="{{ date('Y-m-d', strtotime('+1 month')) }}" required>
            </div>
            <button class="btn btn-primary btn-block">Simpan Langganan</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  {{-- Daftar langganan --}}
  <div class="col-lg-8 col-12">
    <div class="card">
      <div class="card-header"><h4 class="card-title">Daftar Langganan ({{ $langganan->count() }})</h4></div>
      <div class="card-content">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead>
                <tr><th>Cabang</th><th>Paket</th><th>Siklus</th><th>Periode</th><th>Sisa</th><th>Status</th></tr>
              </thead>
              <tbody>
                @forelse ($langganan as $l)
                  <tr>
                    <td>{{ $l->cabang?->nama ?? '-' }}</td>
                    <td>{{ $l->paket?->nama ?? '-' }}</td>
                    <td>{{ $l->siklus }}</td>
                    <td>
                      <small>{{ $l->mulai?->format('d/m/Y') }} — {{ $l->berakhir?->format('d/m/Y') }}</small>
                    </td>
                    <td>
                      @php $h = $l->sisaHari(); @endphp
                      @if ($h < 0) <span class="text-danger">lewat</span>
                      @elseif ($h <= 30) <span class="text-warning">{{ $h }} hr</span>
                      @else <span class="text-muted">{{ $h }} hr</span> @endif
                    </td>
                    <td>
                      @php
                        $warna = match($l->status) {
                          'aktif' => 'success', 'trial' => 'info',
                          'jatuh_tempo' => 'danger', default => 'secondary',
                        };
                      @endphp
                      <span class="badge badge-{{ $warna }}">{{ $l->status }}</span>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="6" class="text-center text-muted">Belum ada langganan</td></tr>
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
