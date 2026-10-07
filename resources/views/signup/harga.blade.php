<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daftar — Javacom Laundry</title>
  <meta name="description" content="Kelola bisnis laundry Anda dari satu tempat. Multi cabang, laporan otomatis, notifikasi pelanggan. Coba gratis 14 hari.">
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Segoe UI',system-ui,-apple-system,sans-serif;background:#f7f9fc;color:#1a2332;line-height:1.6}
    .wrap{max-width:1080px;margin:0 auto;padding:0 20px}
    /* Hero */
    .hero{background:linear-gradient(135deg,#0b1326 0%,#16324f 100%);color:#fff;padding:64px 0 72px;text-align:center}
    .hero .badge{display:inline-block;background:rgba(255,215,0,.15);color:#ffd700;border:1px solid rgba(255,215,0,.35);
      padding:6px 16px;border-radius:100px;font-size:13px;font-weight:600;margin-bottom:20px}
    .hero h1{font-size:38px;line-height:1.25;margin-bottom:14px;font-weight:700}
    .hero h1 .gold{color:#ffd700}
    .hero p{font-size:17px;color:#b8c4d6;max-width:600px;margin:0 auto 28px}
    .hero .cta{display:inline-block;background:#ffd700;color:#0b1326;padding:14px 34px;border-radius:10px;
      font-weight:700;text-decoration:none;font-size:16px;transition:.2s}
    .hero .cta:hover{background:#ffc400;transform:translateY(-2px)}
    .hero .sub{margin-top:14px;font-size:13px;color:#8798ad}

    /* Fitur */
    .fitur{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin:-40px auto 0;
      position:relative;z-index:2}
    .fitur .item{background:#fff;border-radius:12px;padding:22px;box-shadow:0 4px 20px rgba(11,19,38,.08)}
    .fitur .ico{font-size:26px;margin-bottom:8px}
    .fitur h4{font-size:15px;margin-bottom:4px;color:#0b1326}
    .fitur p{font-size:13px;color:#5a6b80;margin:0}

    /* Paket */
    .sec-title{text-align:center;margin:64px 0 8px;font-size:28px;color:#0b1326}
    .sec-sub{text-align:center;color:#5a6b80;margin-bottom:36px;font-size:15px}
    .paket-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:24px;margin-bottom:56px}
    .paket{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:28px 24px;display:flex;
      flex-direction:column;position:relative;transition:.2s}
    .paket:hover{box-shadow:0 8px 30px rgba(11,19,38,.1);transform:translateY(-3px)}
    .paket.populer{border:2px solid #ffd700;box-shadow:0 8px 30px rgba(255,215,0,.18)}
    .paket .tagpop{position:absolute;top:-12px;left:50%;transform:translateX(-50%);background:#ffd700;color:#0b1326;
      padding:4px 16px;border-radius:100px;font-size:11px;font-weight:800;letter-spacing:.5px}
    .paket h3{font-size:20px;margin-bottom:4px}
    .paket .desc{font-size:13px;color:#5a6b80;min-height:38px;margin-bottom:16px}
    .paket .harga{font-size:30px;font-weight:800;color:#0b1326}
    .paket .harga small{font-size:13px;font-weight:500;color:#8798ad}
    .paket ul{list-style:none;margin:18px 0 22px;flex:1}
    .paket li{font-size:13.5px;padding:6px 0 6px 24px;position:relative;color:#33445c}
    .paket li:before{content:"✓";position:absolute;left:0;color:#14b8a6;font-weight:800}
    .paket li.no{color:#a0aec0}
    .paket li.no:before{content:"—";color:#cbd5e0}
    .paket .btn{display:block;text-align:center;padding:12px;border-radius:9px;text-decoration:none;
      font-weight:700;font-size:14px;background:#0b1326;color:#fff;transition:.2s}
    .paket .btn:hover{background:#16324f}
    .paket.populer .btn{background:#ffd700;color:#0b1326}
    .paket.populer .btn:hover{background:#ffc400}

    /* Tabel diskon */
    .diskon-box{background:#fff;border-radius:14px;padding:28px;box-shadow:0 4px 20px rgba(11,19,38,.07);margin-bottom:56px}
    .diskon-box h3{margin-bottom:6px;font-size:19px}
    .diskon-box .note{font-size:13px;color:#5a6b80;margin-bottom:18px}
    table{width:100%;border-collapse:collapse;font-size:14px}
    th,td{padding:10px 12px;text-align:left;border-bottom:1px solid #eef2f7}
    th{background:#f7f9fc;font-size:12px;text-transform:uppercase;letter-spacing:.4px;color:#5a6b80;font-weight:700}
    td.angka{text-align:right;font-variant-numeric:tabular-nums}
    .hijau{color:#14b8a6;font-weight:700}

    footer{background:#0b1326;color:#8798ad;text-align:center;padding:32px 20px;font-size:13px}
    footer a{color:#ffd700;text-decoration:none}
    @media(max-width:640px){
      .hero h1{font-size:27px}.hero{padding:44px 0 56px}
      .fitur{margin-top:-24px}.sec-title{font-size:22px;margin-top:48px}
      .paket .harga{font-size:25px}
      table{font-size:12.5px}th,td{padding:8px 6px}
    }
  </style>
</head>
<body>

<section class="hero">
  <div class="wrap">
    <div class="badge">COBA GRATIS 14 HARI</div>
    <h1>Kelola Bisnis Laundry Anda<br>dari <span class="gold">Satu Tempat</span></h1>
    <p>Multi cabang, laporan otomatis, dan notifikasi pelanggan. Tanpa perlu instalasi — langsung pakai dari browser.</p>
    <a href="{{ route('signup.form') }}" class="cta">Mulai Daftar Sekarang</a>
    <div class="sub">Tidak perlu kartu kredit &middot; Batalkan kapan saja</div>
  </div>
</section>

<div class="wrap">
  <div class="fitur">
    <div class="item"><div class="ico">🏪</div><h4>Multi Cabang</h4><p>Kelola beberapa gerai dari satu akun, pantau semuanya sekaligus.</p></div>
    <div class="item"><div class="ico">📊</div><h4>Laporan Otomatis</h4><p>Pendapatan, transaksi, dan performa tiap cabang tanpa hitung manual.</p></div>
    <div class="item"><div class="ico">💬</div><h4>Notifikasi Pelanggan</h4><p>Beritahu pelanggan saat cucian selesai lewat WhatsApp & Telegram.</p></div>
    <div class="item"><div class="ico">🧾</div><h4>Invoice &amp; Cetak</h4><p>Nota digital siap cetak untuk setiap transaksi pelanggan.</p></div>
  </div>
</div>

<div class="wrap">
  <h2 class="sec-title">Pilih Paket Sesuai Skala Usaha</h2>
  <p class="sec-sub">Semua paket termasuk trial 14 hari. Tanpa biaya pendaftaran.</p>

  <div class="paket-grid">
    @foreach ($paket as $p)
      @php
        $populer = $p->kode === 'pro';
        $rp = fn ($n) => number_format((float) $n, 0, ',', '.');
        $f = is_array($p->fitur) ? $p->fitur : json_decode($p->fitur ?? '{}', true);
        $fitur = is_array($f) ? $f : [];
      @endphp
      <div class="paket {{ $populer ? 'populer' : '' }}">
        @if ($populer) <div class="tagpop">PALING POPULER</div> @endif
        <h3>{{ $p->nama }}</h3>
        <div class="desc">{{ $p->deskripsi }}</div>
        <div class="harga">Rp {{ $rp($p->harga_bulanan) }}<small>/bulan</small></div>
        <ul>
          <li>{{ $p->batas_cabang == -1 ? 'Cabang tanpa batas' : $p->batas_cabang . ' cabang' }}</li>
          <li>{{ $p->batas_user == -1 ? 'Pengguna tanpa batas' : $p->batas_user . ' pengguna' }}</li>
          <li>{{ $p->batas_transaksi_bulanan == -1 ? 'Transaksi tanpa batas' : $rp($p->batas_transaksi_bulanan) . ' transaksi/bulan' }}</li>
          <li class="{{ ! empty($fitur['whatsapp']) ? '' : 'no' }}">Notifikasi WhatsApp</li>
          <li class="{{ ! empty($fitur['telegram']) ? '' : 'no' }}">Notifikasi Telegram</li>
          <li class="{{ ! empty($fitur['laporan_lintas_cabang']) ? '' : 'no' }}">Laporan lintas cabang</li>
          <li class="{{ ! empty($fitur['api']) ? '' : 'no' }}">Integrasi API</li>
        </ul>
        <a href="{{ route('signup.form', ['paket' => $p->kode]) }}" class="btn">
          {{ $populer ? 'Mulai dengan Pro' : 'Pilih ' . $p->nama }}
        </a>
      </div>
    @endforeach
  </div>

  @if (count($paket) > 0 && isset($pratinjau))
    <div class="diskon-box">
      <h3>💰 Makin Banyak Cabang, Makin Hemat</h3>
      <p class="note">
        Harga dihitung <strong>per cabang</strong>, dengan diskon otomatis sesuai jumlah cabang.
        Contoh paket <strong>Pro</strong> (Rp 350.000/cabang/bulan):
      </p>
      <table>
        <thead>
          <tr><th>Jumlah Cabang</th><th class="angka">Sebelum Diskon</th><th class="angka">Diskon</th><th class="angka">Bayar / Bulan</th></tr>
        </thead>
        <tbody>
          @php
            $pro = $paket->firstWhere('kode', 'pro') ?? $paket->first();
            $rp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
          @endphp
          @foreach ([1, 2, 3] as $jml)
            @php $h = $pratinjau[$pro->id][$jml] ?? null; @endphp
            @if ($h)
              <tr>
                <td>{{ $jml }} cabang</td>
                <td class="angka">{{ $rp($h['subtotal']) }}</td>
                <td class="angka">{{ $h['diskon_persen'] > 0 ? $h['diskon_persen'] . '%' : '—' }}</td>
                <td class="angka hijau">{{ $rp($h['total']) }}</td>
              </tr>
            @endif
          @endforeach
        </tbody>
      </table>
      <p class="note" style="margin-top:12px;margin-bottom:0">
        * Angka di atas ilustrasi. Diskon berlaku otomatis sesuai setelan paket Anda.
      </p>
    </div>
  @endif
</div>

<footer>
  <div>&copy; {{ date('Y') }} Javacom &middot; Digital Growth Partner</div>
  <div style="margin-top:6px">Butuh bantuan? <a href="{{ route('login') }}">Masuk ke akun Anda</a></div>
</footer>

</body>
</html>
