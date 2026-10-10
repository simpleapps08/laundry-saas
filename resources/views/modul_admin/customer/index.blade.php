@extends('layouts.backend')
@section('title','Admin - Data Customer')
@section('header','Data Customer')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"> Data Customer </h4>

                {{-- ===== DESKTOP (>=768px): TABEL ===== --}}
                <div class="table-responsive m-t-0 d-none d-md-block">
                    <table id="myTable" class="table display table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>No Telpon</th>
                                <th>Kelamin</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; ?>
                            @foreach ($customer as $item)
                            <tr>
                                <td>{{$no}}</td>
                                <td>{{$item->name}}</td>
                                <td>{{$item->alamat}}</td>
                                <td>{{$item->no_telp}}</td>
                                <td>
                                    @if ($item->kelamin == 'L')
                                        <span class="label label-success">Laki-laki</span>
                                    @else
                                        <span class="label label-info">Perempuan</span>
                                    @endif
                                </td>
                                <td>
                                  <a href="{{route('customer.show', $item->id)}}" class="btn btn-info btn-sm">Info</a>
                                </td>
                            </tr>
                            <?php $no++; ?>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ===== MOBILE (<768px): KARTU ===== --}}
                <div class="jv-cards-mobile m-t-0">
                    @forelse ($customer as $item)
                      <div class="jv-mcard">
                        <div class="jv-mcard-title">
                          <span>{{ $item->name }}</span>
                          @if ($item->kelamin == 'L')<span class="label label-success">Laki-laki</span>
                          @else<span class="label label-info">Perempuan</span>@endif
                        </div>
                        <div class="jv-mcard-row"><span class="jv-mcard-label">Alamat</span><span class="jv-mcard-value">{{ $item->alamat ?: '-' }}</span></div>
                        <div class="jv-mcard-row"><span class="jv-mcard-label">No Telpon</span><span class="jv-mcard-value">{{ $item->no_telp ?: '-' }}</span></div>
                        <div class="jv-mcard-actions">
                          <a href="{{route('customer.show', $item->id)}}" class="btn btn-info btn-sm">Info</a>
                        </div>
                      </div>
                    @empty
                      <div class="jv-mcard-empty">Belum ada customer.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        if ($.fn.DataTable && $('#myTable').length) {
            $('#myTable').DataTable();
        }
    });
</script>
@endsection
