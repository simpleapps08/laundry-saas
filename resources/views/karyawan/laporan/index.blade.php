@extends('layouts.backend')
@section('title','Karyawan - Laporan Laundry')
@section('content')
<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title"> Laporan Laundry
              <a href="{{url('export-excel')}}" class="btn btn-info btn-sm">Export Excel</a>
            </h4>

            {{-- ===== DESKTOP (>=768px): TABEL + DATATABLES ===== --}}
            <div class="table-responsive m-t-0 d-none d-md-block">
                <table id="myTable" class="table display table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Customer</th>
                            <th>Jenis Laundry</th>
                            <th>Jenis Pembayaran</th>
                            <th>Status Pembayaran</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="refresh_body">
                      <?php $no=1; ?>
                      @foreach ($laporan as $laporans)
                        <tr>
                          <td>{{$no}}</td>
                          <td>{{namaCustomer($laporans->customer_id)}}</td>
                          <td>{{ optional($laporans->price)->jenis ?? '-' }}</td>
                          <td>{{$laporans->jenis_pembayaran}}</td>
                          <td>{{$laporans->status_payment}}</td>
                          <td>{{Rupiah::getRupiah($laporans->harga_akhir)}}</td>
                        </tr>
                      <?php $no++; ?>
                      @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ===== MOBILE (<768px): KARTU BERTUMPUK ===== --}}
            <div class="jv-cards-mobile m-t-0">
              @forelse ($laporan as $laporans)
                <div class="jv-mcard">
                  <div class="jv-mcard-title">
                    <span>{{ namaCustomer($laporans->customer_id) }}</span>
                  </div>
                  <div class="jv-mcard-row">
                    <span class="jv-mcard-label">Jenis Laundry</span>
                    <span class="jv-mcard-value">{{ optional($laporans->price)->jenis ?? '-' }}</span>
                  </div>
                  <div class="jv-mcard-row">
                    <span class="jv-mcard-label">Jenis Bayar</span>
                    <span class="jv-mcard-value">{{ $laporans->jenis_pembayaran ?: '-' }}</span>
                  </div>
                  <div class="jv-mcard-row">
                    <span class="jv-mcard-label">Status Bayar</span>
                    <span class="jv-mcard-value">{{ $laporans->status_payment ?: '-' }}</span>
                  </div>
                  <div class="jv-mcard-row">
                    <span class="jv-mcard-label">Total</span>
                    <span class="jv-mcard-value" style="font-weight:700">{{ Rupiah::getRupiah($laporans->harga_akhir) }}</span>
                  </div>
                </div>
              @empty
                <div class="jv-mcard-empty">Belum ada data laporan.</div>
              @endforelse
            </div>
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
