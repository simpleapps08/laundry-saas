@extends('layouts.backend')
@section('title','Notifikasi')
@section('header','Notifikasi')
@section('content')
@if ($message = Session::get('success'))
  <div class="alert alert-success alert-block">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>{{ $message }}</strong>
  </div>
@elseif($message = Session::get('error'))
  <div class="alert alert-danger alert-block">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>{{ $message }}</strong>
  </div>
@endif

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Notifikasi</h4>

        {{-- ===== DESKTOP (>=768px): TABEL ===== --}}
        <div class="table-responsive d-none d-md-block">
          <table class="table">
            <thead>
              <tr><th>#</th><th>Judul</th><th>Isi</th><th>Kategori</th><th>Status</th><th>Waktu</th></tr>
            </thead>
            <tbody>
              <?php $no = 1; ?>
              @forelse ($notifikasi as $n)
              <tr>
                <td>{{ $no }}</td>
                <td>{{ $n->title }}</td>
                <td>{{ Str::limit($n->body, 80) }}</td>
                <td>{{ $n->kategori ?? '-' }}</td>
                <td>
                  @if ($n->is_read)
                    <span class="label label-success">Dibaca</span>
                  @else
                    <span class="label label-warning">Belum</span>
                  @endif
                </td>
                <td>{{ $n->created_at }}</td>
              </tr>
              <?php $no++; ?>
              @empty
              <tr><td colspan="6" class="text-center">Belum ada notifikasi.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- ===== MOBILE (<768px): KARTU ===== --}}
        <div class="jv-cards-mobile">
          @forelse ($notifikasi as $n)
            <div class="jv-mcard">
              <div class="jv-mcard-title">
                <span>{{ $n->title }}</span>
                @if ($n->is_read)<span class="label label-success">Dibaca</span>
                @else<span class="label label-warning">Belum</span>@endif
              </div>
              <div class="jv-mcard-row"><span class="jv-mcard-label">Isi</span><span class="jv-mcard-value">{{ Str::limit($n->body, 120) }}</span></div>
              <div class="jv-mcard-row"><span class="jv-mcard-label">Kategori</span><span class="jv-mcard-value">{{ $n->kategori ?? '-' }}</span></div>
              <div class="jv-mcard-row"><span class="jv-mcard-label">Waktu</span><span class="jv-mcard-value">{{ $n->created_at }}</span></div>
            </div>
          @empty
            <div class="jv-mcard-empty">Belum ada notifikasi.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
