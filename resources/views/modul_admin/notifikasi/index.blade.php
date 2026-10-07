@extends('layouts.backend')
@section('title','Notifikasi')
@section('header','Notifikasi')
@section('content')
@if ($message = Session::get('success'))
  <div class="alert alert-success alert-block">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>{ $message }</strong>
  </div>
@elseif($message = Session::get('error'))
  <div class="alert alert-danger alert-block">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>{ $message }</strong>
  </div>
@endif

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Notifikasi</h4>
        <div class="table-responsive">
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
      </div>
    </div>
  </div>
</div>
@endsection
