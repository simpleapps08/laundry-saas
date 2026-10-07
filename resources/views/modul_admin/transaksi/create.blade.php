@extends('layouts.backend')
@section('title','Transaksi - Tambah')
@section('header','Tambah Transaksi')
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
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Transaksi Baru</h4>
        <form action="{{ route('transaksi.store') }}" method="POST">
          @csrf
          <div class="form-group">
            <label>Customer <span class="text-danger">*</span></label>
            <select name="customer_id" class="form-control @error('customer_id') is-invalid @enderror">
              <option value="">-- Pilih Customer --</option>
              @foreach ($customer as $c)
                <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->email }})</option>
              @endforeach
            </select>
            @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Karyawan Penanggung Jawab</label>
            <select name="user_id" class="form-control">
              <option value="">-- Saya sendiri --</option>
              @foreach ($karyawan as $k)
                <option value="{{ $k->id }}" {{ old('user_id') == $k->id ? 'selected' : '' }}>{{ $k->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Jenis Layanan <span class="text-danger">*</span></label>
            <select name="harga_id" id="harga_id" class="form-control @error('harga_id') is-invalid @enderror">
              <option value="">-- Pilih Layanan --</option>
              @foreach ($harga as $h)
                <option value="{{ $h->id }}" data-harga="{{ $h->harga_numeric ?? $h->harga }}" {{ old('harga_id') == $h->id ? 'selected' : '' }}>
                  {{ $h->jenis }} — Rp {{ number_format($h->harga_numeric ?? $h->harga, 0, ',', '.') }}/kg
                </option>
              @endforeach
            </select>
            @error('harga_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Berat (kg) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0.01" name="kg" id="kg" class="form-control @error('kg') is-invalid @enderror" value="{{ old('kg') }}" placeholder="0.00">
            @error('kg')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Diskon (Rp)</label>
            <input type="number" step="1" min="0" name="disc" id="disc" class="form-control" value="{{ old('disc', 0) }}">
          </div>
          <div class="form-group">
            <label>Total</label>
            <input type="text" id="total" class="form-control" value="Rp 0" readonly>
            <small class="text-muted">Total dihitung otomatis (berat &times; harga &minus; diskon).</small>
          </div>
          <div class="form-group">
            <label>Tanggal Transaksi <span class="text-danger">*</span></label>
            <input type="date" name="tgl_transaksi" class="form-control @error('tgl_transaksi') is-invalid @enderror" value="{{ old('tgl_transaksi', date('Y-m-d')) }}">
            @error('tgl_transaksi')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Status Order</label>
            <select name="status_order" class="form-control">
              <option value="Process">Process</option>
              <option value="Done">Done</option>
              <option value="Delivery">Delivery</option>
            </select>
          </div>
          <div class="form-group">
            <label>Status Pembayaran</label>
            <select name="status_payment" class="form-control">
              <option value="Pending">Pending</option>
              <option value="Success">Success</option>
            </select>
          </div>
          <div class="form-group">
            <label>Jenis Pembayaran <span class="text-danger">*</span></label>
            <select name="jenis_pembayaran" class="form-control @error('jenis_pembayaran') is-invalid @enderror">
              <option value="Tunai" {{ old('jenis_pembayaran') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
              <option value="Transfer" {{ old('jenis_pembayaran') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
            </select>
            @error('jenis_pembayaran')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
          <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Batal</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
  function hitungTotal() {
    var opt   = $('#harga_id').find(':selected');
    var harga = parseFloat(opt.data('harga')) || 0;
    var kg    = parseFloat($('#kg').val()) || 0;
    var disc  = parseFloat($('#disc').val()) || 0;
    var total = (kg * harga) - disc;
    if (total < 0) total = 0;
    $('#total').val('Rp ' + total.toLocaleString('id-ID'));
  }
  $(document).on('change keyup', '#harga_id, #kg, #disc', hitungTotal);
  hitungTotal();
</script>
@endsection
