@extends('layouts.backend')
@section('title','Karyawan - Data Customer')
@section('header','Data Customer')
@section('content')
@if ($message = Session::get('success'))
  <div class="alert alert-success alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
  </div>
@elseif($message = Session::get('error'))
  <div class="alert alert-danger alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
  </div>
@endif
<div class="card">
    <div class="card-body">
        <a href="{{url('customers-create')}}" class="btn btn-primary mb-1">Tambah Customer</a>

        {{-- ===== DESKTOP (>=768px): TABEL + DATATABLES ===== --}}
        <div class="table-responsive m-t-5 d-none d-md-block">
            <table id="myTable" class="table table-bordered table-striped">
                <thead>
                    <tr align="center" style="color:black; font-weight:bold">
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Alamat</th>
                        <th>No Telpon</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; ?>
                    @foreach ($customer as $item)
                    <tr align="center" style="color:black;">
                        <td>{{$no}}</td>
                        <td>{{$item->name}}</td>
                        <td>{{$item->email}}</td>
                        <td>{{$item->alamat}}</td>
                        <td>{{$item->no_telp}}</td>
                        <td>
                          <a href=" {{url('customers', $item->id)}} " class="btn btn-sm btn-primary" style="color:white">Detail</a>
                        </td>
                    </tr>
                    <?php $no++; ?>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ===== MOBILE (<768px): KARTU BERTUMPUK ===== --}}
        <div class="jv-cards-mobile">
            @forelse ($customer as $item)
              <div class="jv-mcard">
                <div class="jv-mcard-title">
                  <span>{{ $item->name }}</span>
                </div>
                <div class="jv-mcard-row">
                  <span class="jv-mcard-label">Email</span>
                  <span class="jv-mcard-value">{{ $item->email ?: '-' }}</span>
                </div>
                <div class="jv-mcard-row">
                  <span class="jv-mcard-label">Alamat</span>
                  <span class="jv-mcard-value">{{ $item->alamat ?: '-' }}</span>
                </div>
                <div class="jv-mcard-row">
                  <span class="jv-mcard-label">No Telpon</span>
                  <span class="jv-mcard-value">{{ $item->no_telp ?: '-' }}</span>
                </div>
                <div class="jv-mcard-actions">
                  <a href="{{url('customers', $item->id)}}" class="btn btn-sm btn-primary" style="color:white">Detail</a>
                </div>
              </div>
            @empty
              <div class="jv-mcard-empty">Belum ada customer.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
// DataTable (desktop) — aman walau tabel disembunyikan di mobile
$(document).ready(function() {
    if ($.fn.DataTable && $('#myTable').length) {
        $('#myTable').DataTable();
    }
});
</script>
@endsection
