@extends('layouts.backend')
@section('title','Dashboard Karyawan')
@section('content')
@if ($message = Session::get('success'))
  <div class="alert alert-success alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
  </div>
@elseif ($message = Session::get('error'))
  <div class="alert alert-danger alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
  </div>
@endif
<div class="card">
    <div class="card-body">
        <h4 class="card-title">
            <a href="{{url('add-order')}}" class="btn btn-primary">Tambah</a>
        </h4>
        <h6>Info : <code> Untuk Mengubah Status Order & Pembayaran Klik Pada Bagian 'Action' Masing-masing.</code></h6>

        {{-- ===== DESKTOP (>=768px): TABEL + DATATABLES ===== --}}
        <div class="table-responsive m-t-0 d-none d-md-block">
            <table id="myTable" class="table display table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No Resi</th>
                        <th>TGL Transaksi</th>
                        <th>Customer</th>
                        <th>Status Laundry</th>
                        <th>Payment</th>
                        <th>Jenis</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; ?>
                    @foreach ($order as $item)
                    <tr>
                        <td>{{$no}}</td>
                        <td style="font-weight:bold; font-color:black">{{$item->invoice}}</td>
                        <td>{{carbon\carbon::parse($item->tgl_transaksi)->format('d-m-y')}}</td>
                        <td>{{$item->customer}}</td>
                        <td>
                            @if ($item->status_order == 'Dijemput')
                                <span class="label label-warning">Dijemput</span>
                            @elseif ($item->status_order == 'Done')
                                <span class="label label-success">Selesai</span>
                            @elseif($item->status_order == 'Diantar')
                                <span class="label label-primary">Diantar</span>
                            @elseif($item->status_order == 'Delivery')
                                <span class="label label-primary">Diambil</span>
                            @elseif($item->status_order == 'Process')
                                <span class="label label-info">Diproses</span>
                            @elseif($item->status_order == 'Batal')
                                <span class="label label-danger">Batal</span>
                            @endif
                        </td>
                        <td>
                            @if ($item->status_payment == 'Success')
                                <span class="label label-success">Lunas</span>
                            @elseif($item->status_payment == 'Pending')
                                <span class="label label-info">Pending</span>
                            @endif
                        </td>
                        <td>{{ optional($item->price)->jenis ?? '-' }}</td>
                        <td>
                            {{Rupiah::getRupiah($item->harga_akhir)}}
                        </td>
                        <td>
                            @if ($item->status_payment == 'Pending')
                            <a class="btn btn-sm btn-danger" style="color:white" data-id-update="{{$item->id}}" id="updateStatus">Bayar</a>
                            <a href="{{url('invoice-kar', $item->id)}}" class="btn btn-sm btn-warning" style="color:white">Invoice</a>
                            @elseif($item->status_payment == 'Success')
                              @if ($item->status_order == 'Dijemput')
                                <a class="btn btn-sm btn-info" style="color:white" data-id-update="{{$item->id}}" id="updateStatus">Proses Cuci</a>
                                <a href="{{url('invoice-kar', $item->id)}}" class="btn btn-sm btn-warning" style="color:white">Invoice</a>
                              @elseif ($item->status_order == 'Process')
                                <a class="btn btn-sm btn-info" style="color:white" data-id-update="{{$item->id}}" id="updateStatus">Selesai</a>
                                <a href="{{url('invoice-kar', $item->id)}}" class="btn btn-sm btn-warning" style="color:white">Invoice</a>
                              @elseif($item->status_order == 'Done')
                                @if(in_array($item->mode_layanan, ['pickup','dropoff']))
                                  <a class="btn btn-sm btn-info" style="color:white" data-id-update="{{$item->id}}" id="updateStatus">Diantar</a>
                                @else
                                  <a class="btn btn-sm btn-info" style="color:white" data-id-update="{{$item->id}}" id="updateStatus">Diambil</a>
                                @endif
                                <a href="{{url('invoice-kar', $item->id)}}" class="btn btn-sm btn-warning" style="color:white">Invoice</a>
                              @elseif($item->status_order == 'Diantar')
                                <a class="btn btn-sm btn-info" style="color:white" data-id-update="{{$item->id}}" id="updateStatus">Diterima</a>
                                <a href="{{url('invoice-kar', $item->id)}}" class="btn btn-sm btn-warning" style="color:white">Invoice</a>
                              @elseif(in_array($item->status_order, ['Delivery','Batal']))
                                <a href="{{url('invoice-kar', $item->id)}}" class="btn btn-sm btn-warning" style="color:white">Invoice</a>
                              @endif
                            @endif
                        </td>
                    </tr>
                    <?php $no++; ?>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ===== MOBILE (<768px): KARTU BERTUMPUK ===== --}}
        <div class="jv-cards-mobile m-t-0">
            @forelse ($order as $item)
              <?php
                $st = $item->status_order;
                $stLabel = ['Dijemput'=>'Dijemput','Done'=>'Selesai','Diantar'=>'Diantar','Delivery'=>'Diambil','Process'=>'Diproses','Batal'=>'Batal'][$st] ?? $st;
                $stClass = ['Dijemput'=>'label-warning','Done'=>'label-success','Diantar'=>'label-primary','Delivery'=>'label-primary','Process'=>'label-info','Batal'=>'label-danger'][$st] ?? 'label-default';
              ?>
              <div class="jv-mcard">
                <div class="jv-mcard-title">
                  <span>{{ $item->invoice }}</span>
                  <span class="label {{ $stClass }}">{{ $stLabel }}</span>
                </div>

                <div class="jv-mcard-row">
                  <span class="jv-mcard-label">Tanggal</span>
                  <span class="jv-mcard-value">{{ carbon\carbon::parse($item->tgl_transaksi)->format('d-m-y') }}</span>
                </div>
                <div class="jv-mcard-row">
                  <span class="jv-mcard-label">Customer</span>
                  <span class="jv-mcard-value">{{ $item->customer }}</span>
                </div>
                <div class="jv-mcard-row">
                  <span class="jv-mcard-label">Jenis</span>
                  <span class="jv-mcard-value">{{ optional($item->price)->jenis ?? '-' }}</span>
                </div>
                <div class="jv-mcard-row">
                  <span class="jv-mcard-label">Payment</span>
                  <span class="jv-mcard-value">
                    @if ($item->status_payment == 'Success')<span class="label label-success">Lunas</span>
                    @elseif($item->status_payment == 'Pending')<span class="label label-info">Pending</span>
                    @else {{ $item->status_payment }} @endif
                  </span>
                </div>
                <div class="jv-mcard-row">
                  <span class="jv-mcard-label">Total</span>
                  <span class="jv-mcard-value" style="font-weight:700">{{ Rupiah::getRupiah($item->harga_akhir) }}</span>
                </div>

                <div class="jv-mcard-actions">
                  @if ($item->status_payment == 'Pending')
                    <a class="btn btn-sm btn-danger" style="color:white" data-id-update="{{$item->id}}" id="updateStatus">Bayar</a>
                    <a href="{{url('invoice-kar', $item->id)}}" class="btn btn-sm btn-warning" style="color:white">Invoice</a>
                  @elseif($item->status_payment == 'Success')
                    @if ($item->status_order == 'Dijemput')
                      <a class="btn btn-sm btn-info" style="color:white" data-id-update="{{$item->id}}" id="updateStatus">Proses Cuci</a>
                    @elseif ($item->status_order == 'Process')
                      <a class="btn btn-sm btn-info" style="color:white" data-id-update="{{$item->id}}" id="updateStatus">Selesai</a>
                    @elseif($item->status_order == 'Done')
                      @if(in_array($item->mode_layanan, ['pickup','dropoff']))
                        <a class="btn btn-sm btn-info" style="color:white" data-id-update="{{$item->id}}" id="updateStatus">Diantar</a>
                      @else
                        <a class="btn btn-sm btn-info" style="color:white" data-id-update="{{$item->id}}" id="updateStatus">Diambil</a>
                      @endif
                    @elseif($item->status_order == 'Diantar')
                      <a class="btn btn-sm btn-info" style="color:white" data-id-update="{{$item->id}}" id="updateStatus">Diterima</a>
                    @endif
                    <a href="{{url('invoice-kar', $item->id)}}" class="btn btn-sm btn-warning" style="color:white">Invoice</a>
                  @endif
                </div>
              </div>
            @empty
              <div class="jv-mcard-empty">Belum ada transaksi.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">

// Update Status Laundry (delegated -> berlaku untuk tabel & kartu)
$(document).on('click', '[id=updateStatus]', function () {
  var id = $(this).attr('data-id-update');
  $.get('update-status-laundry', {'_token' : $('meta[name=csrf-token]').attr('content'),id:id}, function(_resp){
    location.reload()
  });
});

// DATATABLE — hanya aktif bila tabel terlihat (desktop).
// Di mobile tabel disembunyikan; DataTables tetap aman di-init.
$(document).ready(function() {
    if ($.fn.DataTable && $('#myTable').length) {
        $('#myTable').DataTable();
    }
});
</script>
@endsection
