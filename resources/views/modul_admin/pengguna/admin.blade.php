@extends('layouts.backend')
@section('title','Admin - Data Admin')
@section('header','Data Admin')
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
        <h4 class="card-title"> Data Admin
          <a href="{{ route('admin.create') }}" class="btn btn-primary">Tambah</a>
        </h4>
        <div class="table-responsive">
          <table class="table zero-configuration">
            <thead>
              <tr>
                <th>#</th><th>Nama</th><th>Email</th><th>Nama Cabang</th>
                <th>No Telp</th><th>Status</th><th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1; ?>
              @foreach ($adm as $item)
              <tr>
                <td>{{ $no }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->email }}</td>
                <td>{{ $item->nama_cabang ?? '-' }}</td>
                <td>{{ $item->no_telp ?? '-' }}</td>
                <td>
                  @if ($item->status == 'Active')
                    <span class="label label-success">Aktif</span>
                  @else
                    <span class="label label-danger">Tidak Aktif</span>
                  @endif
                </td>
                <td>
                  <a href="{{ route('admin.show', $item->id) }}" class="btn btn-sm btn-info">Detail</a>
                  <a href="{{ route('admin.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>
                  <form action="{{ route('admin.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus admin ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                  </form>
                </td>
              </tr>
              <?php $no++; ?>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
