{{--
  Content landing — Javacom Laundry
  Diganti 10-Okt-2026: sebelumnya isinya <contenone>/<contentwo> (komponen Vue
  bawaan fork) yang tidak relevan. Sekarang seksi statis murni (tanpa dependensi
  JS) supaya landing ringan & langsung tampil.

  Kelas yang dipakai SEMUA sudah ada di public/frontend/css/javacom-laundry.css
  (tema putih + tosca): .jc-solusi / .jc-mock* / .jc-float / .jc-fitur / .jc-card
  / .jc-section-badge / .jc-btn. Hanya .jc-cara yang ditambahkan baru.

  Struktur:
    1. Solusi   — ajakan pesan online + lacak cucian
    2. Fitur    — 6 kartu keunggulan
    3. Cara Pesan — 3 langkah (baru)
--}}

{{-- ============================ 1. SOLUSI ============================ --}}
<section class="jc-solusi" id="mengapa-kami">
  <div class="container">
    <div class="row jc-solusi__row">

      {{-- Visual kiri: mock dashboard (CSS murni, kelas .jc-mock*) --}}
      <div class="col-lg-6 col-12">
        <div class="jc-visual-card">
          <span class="jc-visual-card__glow"></span>

          <div class="jc-mock">
            <div class="jc-mock__head">
              <span class="jc-mock__dot"></span>
              <span class="jc-mock__dot"></span>
              <span class="jc-mock__dot"></span>
              <span class="jc-mock__title">Dashboard Laundry</span>
            </div>
            <div class="jc-mock__body">
              <div class="jc-mock__row">
                <span class="jc-mock__label">Nota hari ini</span>
                <span class="jc-mock__pill">128</span>
              </div>
              <div class="jc-mock__row">
                <span class="jc-mock__label">Pendapatan</span>
                <span class="jc-mock__pill jc-mock__pill--gold">Rp 4,2jt</span>
              </div>
              <div class="jc-mock__row">
                <span class="jc-mock__label">Cucian selesai</span>
                <span class="jc-mock__pill">96</span>
              </div>
              <div class="jc-mock__bar"><span></span></div>
              <div class="jc-mock__row jc-mock__row--muted">
                <span>Target bulanan</span>
                <span>68% tercapai</span>
              </div>
            </div>
          </div>

          <span class="jc-float jc-float--1">
            <i class="fa fa-check"></i>
            <div><strong>Selesai</strong><small>12 nota</small></div>
          </span>
          <span class="jc-float jc-float--2">
            <i class="fa fa-truck"></i>
            <div><strong>Dijemput</strong><small>5 pesanan</small></div>
          </span>
        </div>
      </div>

      {{-- Teks kanan --}}
      <div class="col-lg-6 col-12">
        <div class="jc-solusi__text">
          <span class="jc-section-badge">Untuk Pelanggan &amp; Pemilik Usaha</span>

          <h2 class="jc-solusi__title">
            Laundry Bersih,<br>
            <span class="gold">Pesan &amp; Pantau dari Rumah</span>
          </h2>

          <p class="jc-solusi__desc">
            Pelanggan bisa memesan langsung tanpa membuat akun — cukup isi nama, nomor
            WhatsApp, dan alamat. Cucian dijemput, diproses, lalu diantar kembali. Semua
            status bisa dicek kapan saja.
          </p>

          <ul class="jc-solusi__list">
            <li>
              <span class="jc-check"><i class="fa fa-check"></i></span>
              <div>
                <strong>Pesan tanpa akun</strong>
                <small>Cukup nama, WhatsApp, dan alamat. Selesai dalam 1 menit.</small>
              </div>
            </li>
            <li>
              <span class="jc-check"><i class="fa fa-check"></i></span>
              <div>
                <strong>Antar-jemput</strong>
                <small>Pilih dijemput kurir atau antar sendiri ke cabang terdekat.</small>
              </div>
            </li>
            <li>
              <span class="jc-check"><i class="fa fa-check"></i></span>
              <div>
                <strong>Lacak status cucian</strong>
                <small>Cek posisi cucian dengan nomor pesanan, tanpa login.</small>
              </div>
            </li>
          </ul>

          <a href="{{ route('order-online.form') }}" class="jc-btn jc-btn--gold">
            <i class="fa fa-shopping-basket"></i>&nbsp; Pesan Laundry Online
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

{{-- ============================ 2. FITUR ============================ --}}
<section class="jc-fitur" id="fitur">
  <div class="container">
    <div class="jc-fitur__head">
      <span class="jc-section-badge">Kenapa Javacom Laundry</span>
      <h2 class="jc-fitur__title">
        Satu Aplikasi untuk <span class="gold">Semua Kebutuhan Laundry</span>
      </h2>
      <p class="jc-fitur__sub">
        Dari kasir sampai laporan keuangan, dari satu cabang sampai banyak cabang.
      </p>
    </div>

    <div class="row jc-fitur__grid">

      <div class="col-lg-4 col-md-6 col-12 jc-fitur__col">
        <div class="jc-card">
          <div class="jc-card__icon"><i class="fa fa-desktop"></i></div>
          <h3 class="jc-card__title">Kasir Cepat</h3>
          <p class="jc-card__desc">
            Buat nota, hitung otomatis, cetak struk — cukup beberapa klik. Tidak perlu
            hitung manual lagi.
          </p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 col-12 jc-fitur__col">
        <div class="jc-card">
          <div class="jc-card__icon"><i class="fa fa-search"></i></div>
          <h3 class="jc-card__title">Lacak Cucian</h3>
          <p class="jc-card__desc">
            Pelanggan bisa cek status cucian kapan saja dengan nomor nota — tanpa perlu
            menelepon ke outlet.
          </p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 col-12 jc-fitur__col">
        <div class="jc-card">
          <div class="jc-card__icon"><i class="fa fa-truck"></i></div>
          <h3 class="jc-card__title">Antar-Jemput</h3>
          <p class="jc-card__desc">
            Terima pesanan online, atur jadwal penjemputan dan pengantaran cucian
            langsung dari aplikasi.
          </p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 col-12 jc-fitur__col">
        <div class="jc-card">
          <div class="jc-card__icon"><i class="fa fa-building-o"></i></div>
          <h3 class="jc-card__title">Multi Cabang</h3>
          <p class="jc-card__desc">
            Kelola beberapa outlet sekaligus. Data tiap cabang terpisah rapi dan aman.
          </p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 col-12 jc-fitur__col">
        <div class="jc-card">
          <div class="jc-card__icon"><i class="fa fa-line-chart"></i></div>
          <h3 class="jc-card__title">Laporan Keuangan</h3>
          <p class="jc-card__desc">
            Lihat pendapatan harian, bulanan, dan performa tiap cabang dalam satu
            dashboard.
          </p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 col-12 jc-fitur__col">
        <div class="jc-card">
          <div class="jc-card__icon"><i class="fa fa-bell-o"></i></div>
          <h3 class="jc-card__title">Notifikasi Otomatis</h3>
          <p class="jc-card__desc">
            Pelanggan dapat kabar saat cucian mulai diproses, selesai, dan siap diambil.
          </p>
        </div>
      </div>

    </div>

    <div class="jc-fitur__cta">
      <a href="{{ route('signup.harga') }}" class="jc-btn jc-btn--gold">
        Mulai Usaha Laundry Anda
      </a>
      <span class="jc-fitur__cta-note">Gratis 14 hari · Tanpa kartu kredit</span>
    </div>
  </div>
</section>

{{-- ======================= 3. CARA PESAN (baru) ======================= --}}
<section class="jc-cara" id="cara-pesan">
  <div class="container">
    <div class="jc-fitur__head">
      <span class="jc-section-badge">Baru di sini?</span>
      <h2 class="jc-fitur__title">
        Cara <span class="gold">Pesan Laundry Online</span>
      </h2>
      <p class="jc-fitur__sub">Tiga langkah saja. Tidak perlu daftar akun.</p>
    </div>

    <div class="row jc-fitur__grid">
      <div class="col-lg-4 col-12 jc-fitur__col">
        <div class="jc-card jc-cara__step">
          <span class="jc-cara__num">1</span>
          <h3 class="jc-card__title">Isi Form Pesanan</h3>
          <p class="jc-card__desc">
            Isi nama, nomor WhatsApp, alamat, dan pilih cabang. Tentukan apakah ingin
            dijemput atau antar sendiri.
          </p>
        </div>
      </div>

      <div class="col-lg-4 col-12 jc-fitur__col">
        <div class="jc-card jc-cara__step">
          <span class="jc-cara__num">2</span>
          <h3 class="jc-card__title">Dapat Kode Pesanan</h3>
          <p class="jc-card__desc">
            Setelah dikirim, Anda menerima kode pesanan. Simpan kode itu untuk melacak
            status cucian.
          </p>
        </div>
      </div>

      <div class="col-lg-4 col-12 jc-fitur__col">
        <div class="jc-card jc-cara__step">
          <span class="jc-cara__num">3</span>
          <h3 class="jc-card__title">Cucian Dijemput &amp; Selesai</h3>
          <p class="jc-card__desc">
            Kurir menjemput cucian, proses berjalan, dan Anda memantau statusnya sampai
            kembali bersih.
          </p>
        </div>
      </div>
    </div>

    <div class="jc-fitur__cta">
      <a href="{{ route('order-online.form') }}" class="jc-btn jc-btn--gold">
        <i class="fa fa-shopping-basket"></i>&nbsp; Pesan Sekarang
      </a>
      <a href="{{ route('order-online.cek') }}" class="jc-btn jc-btn--ghost"
         style="margin-left:10px;">
        <i class="fa fa-search"></i>&nbsp; Cek Status Pesanan
      </a>
    </div>
  </div>
</section>
