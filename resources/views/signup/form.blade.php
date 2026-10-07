<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Form Pendaftaran — Javacom Laundry</title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Segoe UI',system-ui,-apple-system,sans-serif;background:#f7f9fc;color:#1a2332;line-height:1.6}
    .top{background:#0b1326;color:#fff;padding:16px 0}
    .top .wrap{max-width:960px;margin:0 auto;padding:0 20px;display:flex;justify-content:space-between;align-items:center}
    .top a{color:#ffd700;text-decoration:none;font-size:14px}
    .top .brand{font-weight:700;font-size:16px}
    .top .brand span{color:#ffd700}

    .wrap{max-width:960px;margin:0 auto;padding:0 20px}
    .head{text-align:center;padding:36px 0 24px}
    .head h1{font-size:27px;margin-bottom:6px}
    .head p{color:#5a6b80;font-size:14.5px}

    .grid{display:grid;grid-template-columns:1.6fr 1fr;gap:24px;align-items:start;padding-bottom:56px}
    .card{background:#fff;border-radius:14px;padding:26px;box-shadow:0 4px 20px rgba(11,19,38,.07)}
    .card h3{font-size:16px;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid #eef2f7}

    label{display:block;font-size:13px;font-weight:600;color:#33445c;margin-bottom:5px}
    input[type=text],input[type=email],input[type=password],select{
      width:100%;padding:10px 12px;border:1px solid #d7dfe9;border-radius:8px;font-size:14px;
      font-family:inherit;transition:.15s;background:#fff}
    input:focus,select:focus{outline:none;border-color:#14b8a6;box-shadow:0 0 0 3px rgba(20,184,166,.12)}
    .fg{margin-bottom:14px}
    .row2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    .hint{font-size:11.5px;color:#8798ad;margin-top:4px}

    .err{background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;border-radius:9px;padding:12px 14px;
      font-size:13px;margin-bottom:16px}
    .err ul{margin:0;padding-left:18px}

    .syarat{display:flex;gap:9px;align-items:flex-start;font-size:13px;color:#33445c;margin:16px 0}
    .syarat input{width:16px;height:16px;margin-top:2px;flex-shrink:0}

    button{width:100%;background:#0b1326;color:#fff;border:none;padding:13px;border-radius:9px;font-size:15px;
      font-weight:700;cursor:pointer;font-family:inherit;transition:.2s}
    button:hover{background:#16324f}

    /* Ringkasan paket */
    .ringkas .pnam{font-size:19px;font-weight:800}
    .ringkas .phrg{font-size:26px;font-weight:800;color:#0b1326;margin:8px 0 2px}
    .ringkas .phrg small{font-size:13px;font-weight:500;color:#8798ad}
    .ringkas ul{list-style:none;margin:16px 0 0;padding:0}
    .ringkas li{font-size:13px;padding:5px 0 5px 22px;position:relative;color:#33445c}
    .ringkas li:before{content:"✓";position:absolute;left:0;color:#14b8a6;font-weight:800}
    .trial{background:#f0fdfa;border:1px solid #99f6e4;color:#0f766e;border-radius:9px;padding:10px 12px;
      font-size:12.5px;margin-top:16px;text-align:center;font-weight:600}
    .gantipaket{margin-top:14px;font-size:13px}
    .gantipaket a{color:#0d9488;font-weight:600}

    @media(max-width:820px){
      .grid{grid-template-columns:1fr}.ringkas{order:-1}
      .row2{grid-template-columns:1fr}
      .head h1{font-size:22px}
    }
  </style>
</head>
<body>

<div class="top">
  <div class="wrap">
    <div class="brand">Javacom <span>Laundry</span></div>
    <a href="{{ route('login') }}">Sudah punya akun? Masuk</a>
  </div>
</div>

<div class="wrap">
  <div class="head">
    <h1>Buat Akun Laundry Anda</h1>
    <p>Trial gratis 14 hari &middot; Tanpa kartu kredit</p>
  </div>

  <div class="grid">
    {{-- ── FORM ── --}}
    <div class="card">
      <h3>Data Pendaftaran</h3>

      @if ($errors->any())
        <div class="err">
          <ul>
            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('signup.daftar') }}">
        @csrf

        <div class="fg">
          <label>Nama Usaha Laundry <span style="color:#dc2626">*</span></label>
          <input type="text" name="nama" value="{{ old('nama') }}" required
                 placeholder="Contoh: Laundry Bersih Wangi">
        </div>

        <div class="row2">
          <div class="fg">
            <label>Nama Anda (Pemilik) <span style="color:#dc2626">*</span></label>
            <input type="text" name="owner_nama" value="{{ old('owner_nama') }}" required
                   placeholder="Nama lengkap">
          </div>
          <div class="fg">
            <label>No. WhatsApp / Telepon <span style="color:#dc2626">*</span></label>
            <input type="text" name="no_telp" value="{{ old('no_telp') }}" required
                   placeholder="0812xxxxxxx">
          </div>
        </div>

        <div class="fg">
          <label>Email <span style="color:#dc2626">*</span></label>
          <input type="email" name="email" value="{{ old('email') }}" required
                 placeholder="nama@email.com">
          <div class="hint">Email ini dipakai untuk masuk ke akun Anda.</div>
        </div>

        <div class="row2">
          <div class="fg">
            <label>Password <span style="color:#dc2626">*</span></label>
            <input type="password" name="password" required minlength="8" placeholder="Min. 8 karakter">
          </div>
          <div class="fg">
            <label>Ulangi Password <span style="color:#dc2626">*</span></label>
            <input type="password" name="password_confirmation" required minlength="8">
          </div>
        </div>

        <div class="row2">
          <div class="fg">
            <label>Nama Cabang Pertama</label>
            <input type="text" name="nama_cabang" value="{{ old('nama_cabang') }}"
                   placeholder="Kosongkan = otomatis">
          </div>
          <div class="fg">
            <label>Alamat Cabang</label>
            <input type="text" name="alamat" value="{{ old('alamat') }}" placeholder="Kota / alamat">
          </div>
        </div>

        <div class="row2">
          <div class="fg">
            <label>Paket <span style="color:#dc2626">*</span></label>
            <select name="paket_id" required>
              @foreach ($paket as $p)
                <option value="{{ $p->id }}"
                  {{ (string) old('paket_id', $terpilih->id) === (string) $p->id ? 'selected' : '' }}>
                  {{ $p->nama }} — Rp {{ number_format($p->harga_bulanan, 0, ',', '.') }}/bln
                </option>
              @endforeach
            </select>
          </div>
          <div class="fg">
            <label>Siklus Pembayaran <span style="color:#dc2626">*</span></label>
            <select name="siklus" required>
              <option value="bulanan" {{ old('siklus', 'bulanan') === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
              <option value="tahunan" {{ old('siklus') === 'tahunan' ? 'selected' : '' }}>Tahunan</option>
            </select>
          </div>
        </div>

        <label class="syarat">
          <input type="checkbox" name="setuju" value="1" {{ old('setuju') ? 'checked' : '' }} required>
          <span>Saya menyetujui syarat &amp; ketentuan serta kebijakan privasi Javacom Laundry.</span>
        </label>

        <button type="submit">Buat Akun &amp; Mulai Trial</button>
      </form>
    </div>

    {{-- ── RINGKASAN PAKET ── --}}
    <div class="card ringkas">
      <h3>Ringkasan Paket</h3>
      @php
        $rp = fn ($n) => number_format((float) $n, 0, ',', '.');
        $f = is_array($terpilih->fitur) ? $terpilih->fitur : json_decode($terpilih->fitur ?? '{}', true);
        $fitur = is_array($f) ? $f : [];
      @endphp
      <div class="pnam">{{ $terpilih->nama }}</div>
      <div class="phrg">Rp {{ $rp($terpilih->harga_bulanan) }}<small>/bulan</small></div>
      <div style="font-size:12.5px;color:#5a6b80">{{ $terpilih->deskripsi }}</div>
      <ul>
        <li>{{ $terpilih->batas_cabang == -1 ? 'Cabang tanpa batas' : $terpilih->batas_cabang . ' cabang' }}</li>
        <li>{{ $terpilih->batas_user == -1 ? 'Pengguna tanpa batas' : $terpilih->batas_user . ' pengguna' }}</li>
        <li>{{ $terpilih->batas_transaksi_bulanan == -1 ? 'Transaksi tanpa batas' : $rp($terpilih->batas_transaksi_bulanan) . ' transaksi/bulan' }}</li>
        @if (! empty($fitur['whatsapp'])) <li>Notifikasi WhatsApp</li> @endif
        @if (! empty($fitur['telegram'])) <li>Notifikasi Telegram</li> @endif
        @if (! empty($fitur['laporan_lintas_cabang'])) <li>Laporan lintas cabang</li> @endif
        @if (! empty($fitur['api'])) <li>Integrasi API</li> @endif
      </ul>
      <div class="trial">🎉 GRATIS 14 HARI — tidak ditagih sekarang</div>
      <div class="gantipaket">
        Mau paket lain? <a href="{{ route('signup.harga') }}">Lihat semua paket</a>
      </div>
    </div>
  </div>
</div>

</body>
</html>
