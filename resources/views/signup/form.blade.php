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
  
  /* ── Navbar publik (dipinjam dari layout frontend) ──────────────────
     Halaman /daftar tidak memuat bootstrap3, jadi navbar di-style manual
     dengan token warna yang sama supaya konsisten dengan halaman utama. */
  .jc-nav{position:sticky;top:0;z-index:1000;background:#0b1326;
    border-bottom:1px solid rgba(255,255,255,.08);padding:0 20px}
  .jc-nav__in{max-width:1080px;margin:0 auto;display:flex;align-items:center;
    justify-content:space-between;height:64px;gap:16px}
  .jc-nav__brand{display:flex;align-items:center;gap:9px;color:#fff;font-weight:700;
    font-size:16.5px;text-decoration:none;white-space:nowrap}
  .jc-nav__brand:hover,.jc-nav__brand:focus{color:#fff;text-decoration:none}
  .jc-nav__mark{width:26px;height:26px;border-radius:7px;flex:0 0 26px;
    background:linear-gradient(135deg,#14b8a6,#0d9488);position:relative}
  .jc-nav__mark:after{content:"";position:absolute;inset:7px 6px;border-radius:2px;
    background:#fff;clip-path:polygon(0 0,100% 0,100% 42%,0 42%,0 58%,100% 58%,100% 100%,0 100%)}
  .jc-nav__links{display:flex;align-items:center;gap:6px;list-style:none;margin:0;padding:0}
  .jc-nav__links a{color:#c3cddd;text-decoration:none;font-size:14.5px;font-weight:500;
    padding:8px 13px;border-radius:8px;display:inline-block;transition:.18s}
  .jc-nav__links a:hover,.jc-nav__links a:focus{color:#fff;background:rgba(255,255,255,.07);
    text-decoration:none}
  .jc-nav__links a.jc-nav__cta{background:#ffd700;color:#0b1326;font-weight:700;
    padding:9px 18px;margin-left:6px}
  .jc-nav__links a.jc-nav__cta:hover,.jc-nav__links a.jc-nav__cta:focus{
    background:#e6c200;color:#0b1326}
  .jc-nav__toggle{display:none;background:none;border:1px solid rgba(255,255,255,.25);
    border-radius:8px;padding:7px 9px;cursor:pointer;line-height:0}
  .jc-nav__toggle span{display:block;width:20px;height:2px;background:#fff;margin:4px 0;
    border-radius:2px}
  @media(max-width:820px){
    .jc-nav__toggle{display:block}
    .jc-nav__in{flex-wrap:wrap;height:auto;min-height:64px;padding:10px 0}
    .jc-nav__links{display:none;width:100%;flex-direction:column;align-items:stretch;
      gap:2px;padding:6px 0 12px}
    .jc-nav__links.jc-open{display:flex}
    .jc-nav__links a{padding:11px 12px;font-size:15px}
    .jc-nav__links a.jc-nav__cta{margin:8px 0 0;text-align:center}
  }
  /* Kompensasi karena navbar sticky menggantikan posisi hero */
  .jc-nav + .hero{padding-top:56px}

  </style>
</head>
<body>

{{-- Navbar publik — SATU sumber dengan halaman utama (frontend/header.blade.php).
     Halaman /daftar tidak memakai layout frontend (punya CSS sendiri), jadi
     navbar-nya direplikasi di sini dengan struktur & tautan yang identik.
     Kalau mengubah menu di header.blade.php, samakan di sini juga. --}}
<nav class="jc-nav">
  <div class="jc-nav__in">
    <a href="{{ url('/') }}" class="jc-nav__brand">
      <span class="jc-nav__mark"></span>
      <span>{{ $setpage != null && $setpage->judul ? $setpage->judul : 'Javacom Laundry' }}</span>
    </a>

    <button type="button" class="jc-nav__toggle" id="jc-nav-toggle" aria-label="Buka menu">
      <span></span><span></span><span></span>
    </button>

    <ul class="jc-nav__links" id="jc-nav-links">
      <li><a href="{{ url('/') }}">Home</a></li>
      <li><a href="{{ route('signup.harga') }}">Harga</a></li>
      <li><a href="{{ url('/') }}#lacak">Lacak Cucian</a></li>
      @auth
        <li><a href="{{ url('/home') }}">Dashboard</a></li>
      @else
        <li><a href="{{ route('login') }}">Masuk</a></li>
        <li><a href="{{ route('signup.form') }}" class="jc-nav__cta">Coba Gratis 14 Hari</a></li>
      @endauth
    </ul>
  </div>
</nav>
<script>
  (function () {
    var t = document.getElementById('jc-nav-toggle');
    var l = document.getElementById('jc-nav-links');
    if (t && l) t.addEventListener('click', function () { l.classList.toggle('jc-open'); });
  })();
</script>


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
