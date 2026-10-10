@extends('layouts.backend')
@section('title','Admin - Data Karyawan')
@section('header','Data Karyawan')
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
<div class="row">
  <div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title"> Data Karyawan / Cabang
                <a href="{{route('karyawan.create')}}" class="btn btn-primary">Tambah</a>
            </h4>

            {{-- ===== DESKTOP (>=768px): TABEL ===== --}}
            <div class="table-responsive d-none d-md-block">
                <table class="table zero-configuration">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Karyawan</th>
                            <th>Email</th>
                            <th>Alamat Cabang</th>
                            <th>Nama Cabang</th>
                            <th>No Telp</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; ?>
                        @foreach ($kry as $item)
                        <tr>
                            <td>{{$no}}</td>
                            <td>{{$item->name}}</td>
                            <td>{{$item->email}}</td>
                            <td>{{$item->alamat_cabang}}</td>
                            <td>{{$item->nama_cabang}}</td>
                            <td>{{$item->no_telp}}</td>
                            <td>
                                @if ($item->status == 'Active')
                                    <span class="label label-success">Aktif</span>
                                @else
                                    <span class="label label-danger">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                              <form action="{{ route('karyawan.destroy',$item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <a class="btn btn-sm btn-{{$item->status == 'Active' ? 'primary' : 'danger'}}" data-id-update="{{$item->id}}" id="updateStatus">{{$item->status == 'Active' ? 'Non-Aktifkan' : 'Aktifkan'}}</a>
                              </form>
                            </td>
                        </tr>
                        <?php $no++; ?>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ===== MOBILE (<768px): KARTU ===== --}}
            <div class="jv-cards-mobile">
              @forelse ($kry as $item)
                <div class="jv-mcard">
                  <div class="jv-mcard-title">
                    <span>{{ $item->name }}</span>
                    @if ($item->status == 'Active')<span class="label label-success">Aktif</span>
                    @else<span class="label label-danger">Tidak Aktif</span>@endif
                  </div>
                  <div class="jv-mcard-row"><span class="jv-mcard-label">Email</span><span class="jv-mcard-value">{{ $item->email }}</span></div>
                  <div class="jv-mcard-row"><span class="jv-mcard-label">Nama Cabang</span><span class="jv-mcard-value">{{ $item->nama_cabang ?: '-' }}</span></div>
                  <div class="jv-mcard-row"><span class="jv-mcard-label">Alamat Cabang</span><span class="jv-mcard-value">{{ $item->alamat_cabang ?: '-' }}</span></div>
                  <div class="jv-mcard-row"><span class="jv-mcard-label">No Telp</span><span class="jv-mcard-value">{{ $item->no_telp ?: '-' }}</span></div>
                  <div class="jv-mcard-actions">
                    <form action="{{ route('karyawan.destroy',$item->id) }}" method="POST" style="display:contents">
                      @csrf
                      @method('DELETE')
                      <a class="btn btn-sm btn-{{$item->status == 'Active' ? 'primary' : 'danger'}}" data-id-update="{{$item->id}}" id="updateStatus">{{$item->status == 'Active' ? 'Non-Aktifkan' : 'Aktifkan'}}</a>
                    </form>
                  </div>
                </div>
              @empty
                <div class="jv-mcard-empty">Belum ada karyawan.</div>
              @endforelse
            </div>
        </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
  // Update Status Karyawan (delegated -> berlaku di tabel & kartu)
  $(document).on('click', '[id=updateStatus]', function () {
    var id = $(this).attr('data-id-update');
    $.get('update-satatus-karyawan', {'_token' : $('meta[name=csrf-token]').attr('content'),id:id}, function(_resp){
      location.reload()
    });
  });
</script>
@endsection
