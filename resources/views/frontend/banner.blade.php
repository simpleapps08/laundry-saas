{{--
  Hero — Javacom Laundry (Modern SaaS)
  v2 (9-Okt-2026): foto banner dikembalikan, tapi dibungkus overlay navy pekat +
  blur/grayscale supaya (a) teks putih tetap terbaca kontras tinggi, (b) tetap
  terasa modern dan bukan sekadar "tempel foto stock repo asli".
  Semua ornamen (orb, grid, panel) tetap CSS/SVG inline — tanpa aset pihak ketiga.
--}}
<div class="hero-jc">
    <div class="hero-jc__bg" aria-hidden="true">
        {{-- Foto latar (dikembalikan sesuai permintaan Boz) --}}
        <img class="hero-jc__photo"
             src="{{ asset('frontend/img/banner.jpg') }}"
             alt="" loading="eager" fetchpriority="high" />
        {{-- Lapisan overlay navy agar teks tetap tajam --}}
        <span class="hero-jc__scrim"></span>
        <span class="hero-jc__orb hero-jc__orb--1"></span>
        <span class="hero-jc__orb hero-jc__orb--2"></span>
        <span class="hero-jc__orb hero-jc__orb--3"></span>
        <span class="hero-jc__grid"></span>
    </div>

    <div class="container hero-jc__inner" id="lacak">
        <span class="hero-jc__badge">Sistem Manajemen Laundry</span>

        <h1 class="hero-jc__title">
            Kelola Bisnis Laundry<br>
            <span class="gold">Lebih Rapi, Lebih Untung</span>
        </h1>

        <p class="hero-jc__sub">
            Kasir, pelacakan cucian, laporan keuangan, dan multi-cabang —
            semua dalam satu aplikasi. Pantau dari mana saja.
        </p>

        <div class="hero-jc__actions">
            <a href="{{ route('signup.harga') }}" class="hero-jc__btn hero-jc__btn--primary">
                Coba Gratis 14 Hari
            </a>
            <a href="#mengapa-kami" class="hero-jc__btn hero-jc__btn--ghost">
                Lihat Fitur
            </a>
        </div>

        <div class="hero-jc__track">
            <label class="hero-jc__track-label" for="search_status">
                <i class="fa fa-search"></i> Lacak status cucian kamu
            </label>
            <div class="input-group">
                <input type="text" class="form-control input-lg" id="search_status"
                       placeholder="Masukkan nomor nota, contoh: TR0392928" />
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-lg hero-jc__track-btn" id="search-btn">
                        <i class="fa fa-search"></i> <span>Cari</span>
                    </button>
                </span>
            </div>
            @include('frontend.modal')
        </div>

        <ul class="hero-jc__stats">
            <li><strong>Multi</strong><span>Cabang</span></li>
            <li><strong>24/7</strong><span>Notifikasi</span></li>
            <li><strong>14</strong><span>Hari Gratis</span></li>
        </ul>
    </div>
</div>
