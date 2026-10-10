@extends('layouts.backend')
@section('title','Admin - Data Transaksi')
@section('content')
<div class="row">
  <div class="col-lg-12">
      <div class="card">
          <div class="card-body">
              <h4 class="card-title"> Data Transaksi
                <div class="row">
                      <div class="col-4">
                          <select name="user_id" id="user_id" class="form-control">
                              <option value="all">--Semua Transaksi--</option>
                                  @foreach ($filter as $item)
                                      <option value="{{$item->id}}">Karyawan {{$item->name}}</option>
                                  @endforeach
                          </select>
                  </div>
                  <div class="cl-3">
                      <button class="btn btn-primary" id="filter">Filter</button>
                  </div>
                </div>
              </h4>

              {{-- ===== DESKTOP (>=768px): TABEL ===== --}}
              <div class="table-responsive m-t-0 d-none d-md-block">
                  <table id="myTable" class="table display table-bordered table-striped">
                      <thead>
                          <tr>
                              <th>#</th>
                              <th>TGL Transaksi</th>
                              <th>Customer</th>
                              <th>Status Order</th>
                              <th>Status Pembayaran</th>
                              <th>Jenis Laundri</th>
                              <th>Total</th>
                              <th>Action</th>
                          </tr>
                      </thead>
                      <tbody id="refresh_body">
                          @foreach ($transaksi as $key => $item)
                          <tr>
                              <td>{{$key+1}}</td>
                              <td>{{carbon\carbon::parse($item->tgl_transaksi)->format('d-m-y')}}</td>
                              <td>{{$item->customer}}</td>
                              <td>
                                  @if ($item->status_order == 'Done')
                                      <span class="label label-success">Selesai</span>
                                  @elseif($item->status_order == 'Delivery')
                                      <span class="label label-info">Sudah Diambil</span>
                                  @elseif($item->status_order == 'Process')
                                      <span class="label label-info">Sedang Proses</span>
                                  @endif
                              </td>
                              <td>
                                  @if ($item->status_payment == 'Success')
                                      <span class="label label-success">Sudah Dibayar</span>
                                  @elseif($item->status_payment == 'Pending')
                                      <span class="label label-info">Belum Dibayar</span>
                                  @endif
                              </td>
                              <td>{{ optional($item->price)->jenis ?? '-' }}</td>
                              <td>
                                <p>{{Rupiah::getRupiah($item->harga_akhir)}}</p>
                              </td>
                              <td align="center">
                                <a href="{{url('invoice-customer', $item->invoice)}}" class="btn btn-sm btn-success" style="color:white">Invoice</a>
                              </td>
                          </tr>
                          @endforeach
                      </tbody>
                  </table>
              </div>

              {{-- ===== MOBILE (<768px): KARTU ===== --}}
              <div class="jv-cards-mobile m-t-0" id="refresh_cards_mobile">
                  @forelse ($transaksi as $item)
                    <?php
                      $stLabel = ['Done'=>'Selesai','Delivery'=>'Sudah Diambil','Process'=>'Sedang Proses'][$item->status_order] ?? $item->status_order;
                      $stClass = ['Done'=>'label-success','Delivery'=>'label-info','Process'=>'label-info'][$item->status_order] ?? 'label-default';
                      $payLabel = ['Success'=>'Sudah Dibayar','Pending'=>'Belum Dibayar'][$item->status_payment] ?? $item->status_payment;
                      $payClass = ['Success'=>'label-success','Pending'=>'label-info'][$item->status_payment] ?? 'label-default';
                    ?>
                    <div class="jv-mcard">
                      <div class="jv-mcard-title">
                        <span>{{ $item->invoice }}</span>
                        <span class="label {{ $stClass }}">{{ $stLabel }}</span>
                      </div>
                      <div class="jv-mcard-row"><span class="jv-mcard-label">Tanggal</span><span class="jv-mcard-value">{{ carbon\carbon::parse($item->tgl_transaksi)->format('d-m-y') }}</span></div>
                      <div class="jv-mcard-row"><span class="jv-mcard-label">Customer</span><span class="jv-mcard-value">{{ $item->customer }}</span></div>
                      <div class="jv-mcard-row"><span class="jv-mcard-label">Jenis</span><span class="jv-mcard-value">{{ optional($item->price)->jenis ?? '-' }}</span></div>
                      <div class="jv-mcard-row"><span class="jv-mcard-label">Pembayaran</span><span class="jv-mcard-value"><span class="label {{ $payClass }}">{{ $payLabel }}</span></span></div>
                      <div class="jv-mcard-row"><span class="jv-mcard-label">Total</span><span class="jv-mcard-value" style="font-weight:700">{{ Rupiah::getRupiah($item->harga_akhir) }}</span></div>
                      <div class="jv-mcard-actions">
                        <a href="{{url('invoice-customer', $item->invoice)}}" class="btn btn-sm btn-success" style="color:white">Invoice</a>
                      </div>
                    </div>
                  @empty
                    <div class="jv-mcard-empty">Belum ada transaksi.</div>
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

// Filter karyawan: perbarui tabel (desktop) DAN kartu (mobile) sekaligus.
// Controller mengembalikan "<rows><!--CARDS--><cards>".
$("#filter").click(function(){
    var user_id  = $("#user_id").val();
    $.get('filter-transaksi',{'_token': $('meta[name=csrf-token]').attr('content'),user_id:user_id}, function(resp){
        var parts = resp.split('<!--CARDS-->');
        var rows  = parts[0] || '';
        var cards = parts[1] || '';
        $("#refresh_body").html(rows);

        var cardBox = $("#refresh_cards_mobile");
        cardBox.html(cards);
        // bila hasil kosong, tampilkan placeholder
        if ($.trim(cards) === '') {
            cardBox.html('<div class="jv-mcard-empty">Belum ada transaksi.</div>');
        }
        // beri tahu DataTables agar tidak menampilkan data lama
        if ($.fn.DataTable && $.fn.dataTable.isDataTable('#myTable')) {
            $('#myTable').DataTable().rows().invalidate().draw();
        }
    });
});
</script>
@endsection
