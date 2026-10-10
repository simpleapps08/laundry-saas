@extends('layouts.backend')
@section('title','Order Online Masuk')
@section('header','Order Online Masuk')
@section('content')
<div class="col-md-12">
  @if($message = Session::get('success'))
    <div class="alert alert-success alert-block">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>{{ $message }}</strong>
    </div>
  @endif

  <div class="card card-outline-info">
    <div class="card-header">
      <h4 class="card-title">
        Order Online Masuk
        @if($jumlahMenunggu > 0)
          <span class="badge badge-danger">{{ $jumlahMenunggu }} menunggu</span>
        @endif
      </h4>
      <div class="mt-1">
        <a href="{{ url('order-online-masuk') }}" class="btn btn-sm {{ $status==='Menunggu' ? 'btn-primary' : 'btn-outline-primary' }}">Menunggu</a>
        <a href="{{ url('order-online-masuk?status=Diproses') }}" class="btn btn-sm {{ $status==='Diproses' ? 'btn-primary' : 'btn-outline-primary' }}">Diproses</a>
        <a href="{{ url('order-online-masuk?status=Dibatalkan') }}" class="btn btn-sm {{ $status==='Dibatalkan' ? 'btn-primary' : 'btn-outline-primary' }}">Dibatalkan</a>
        <a href="{{ url('order-online-masuk?status=semua') }}" class="btn btn-sm {{ $status==='semua' ? 'btn-primary' : 'btn-outline-primary' }}">Semua</a>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover" id="tblOrderOnline">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Tanggal</th>
              <th>Pelanggan</th>
              <th>WA</th>
              <th>Alamat</th>
              <th>Metode</th>
              <th>Est. Berat</th>
              <th>Cabang</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($pesanan as $p)
              <tr>
                <td style="font-weight:bold">{{ $p->kode_pesanan }}</td>
                <td>{{ $p->created_at ? $p->created_at->format('d-m-y H:i') : '-' }}</td>
                <td>{{ $p->nama }}</td>
                <td>{{ $p->no_telp }}</td>
                <td style="max-width:200px;white-space:normal">{{ \Illuminate\Support\Str::limit($p->alamat, 60) }}</td>
                <td>
                  @if($p->mode_layanan === 'pickup')
                    <span class="label label-info">Dijemput</span>
                  @else
                    <span class="label label-warning">Antar sendiri</span>
                  @endif
                </td>
                <td>{{ $p->estimasi_kg ? $p->estimasi_kg.' kg' : '-' }}</td>
                <td>{{ optional($p->cabang)->nama ?? '-' }}</td>
                <td>
                  @if($p->status_online === 'Menunggu')<span class="label label-warning">Menunggu</span>
                  @elseif($p->status_online === 'Diproses')<span class="label label-success">Diproses</span>
                  @else<span class="label label-default">Dibatalkan</span>@endif
                </td>
                <td>
                  @if($p->status_online === 'Menunggu')
                    <a href="{{ url('customers-create?dari_pesanan='.$p->kode_pesanan) }}" class="btn btn-sm btn-primary" style="color:white" title="Buat customer & transaksi dari pesanan ini">Proses</a>
                    <form action="{{ url('order-online-masuk/'.$p->id.'/batal') }}" method="POST" style="display:inline" onsubmit="return confirm('Batalkan pesanan ini?')">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-danger" style="color:white">Batal</button>
                    </form>
                  @elseif($p->transaksi_id)
                    <a href="{{ url('invoice-kar/'.$p->transaksi_id) }}" class="btn btn-sm btn-warning" style="color:white">Invoice</a>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="10" class="text-center text-muted">Belum ada order online.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <p class="text-muted small">
        Saat menekan <b>Proses</b>, Anda diarahkan ke form Tambah Order.
        Form customer akan terisi otomatis dari data pelanggan. Setelah customer
        tersimpan, lanjutkan ke Tambah Order untuk menimbang &amp; mengisi harga.
      </p>
    </div>
  </div>
</div>
@endsection
