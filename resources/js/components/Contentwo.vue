<template>
  <!--
    Section "Mengapa Kami" — Javacom Laundry (Modern SaaS)
    Diganti 9-Okt-2026: 4 ilustrasi PNG gaya repo asli diganti kartu modern
    dengan ikon SVG inline + gradient navy→gold. Tanpa aset pihak ketiga.
  -->
  <section class="jc-fitur" id="mengapa-kami">
    <div class="jc-fitur__head">
      <span class="jc-section-badge">Kenapa Javacom Laundry</span>
      <h2 class="jc-fitur__title">
        Semua yang Kamu Butuhkan<br><span class="gold">Sudah Ada di Sini</span>
      </h2>
      <p class="jc-fitur__sub">
        Dibuat untuk pemilik laundry — bukan sekadar aplikasi kasir.
      </p>
    </div>

    <div class="row jc-fitur__grid">
      <!-- 1. Order Online -->
      <div class="col-md-3 col-sm-6 jc-fitur__col">
        <article class="jc-card">
          <span class="jc-card__icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <rect x="5" y="2" width="14" height="20" rx="3"/>
              <line x1="10" y1="18" x2="14" y2="18"/>
              <path d="M9 8h6M9 12h4"/>
            </svg>
          </span>
          <h3 class="jc-card__title">Order Online</h3>
          <p class="jc-card__desc">
            Pelanggan pesan sendiri lewat aplikasi, tanpa perlu telepon atau
            datang ke tempat.
          </p>
        </article>
      </div>

      <!-- 2. Profesional -->
      <div class="col-md-3 col-sm-6 jc-fitur__col">
        <article class="jc-card">
          <span class="jc-card__icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M12 2 4 6v6c0 5 3.4 8.6 8 10 4.6-1.4 8-5 8-10V6z"/>
              <path d="m9 12 2 2 4-4"/>
            </svg>
          </span>
          <h3 class="jc-card__title">Profesional</h3>
          <p class="jc-card__desc">
            Nota rapi, alur kerja jelas, dan laporan keuangan otomatis setiap
            hari.
          </p>
        </article>
      </div>

      <!-- 3. Terpercaya -->
      <div class="col-md-3 col-sm-6 jc-fitur__col">
        <article class="jc-card">
          <span class="jc-card__icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M3 21h18"/>
              <rect x="4" y="13" width="4" height="8" rx="1"/>
              <rect x="10" y="8" width="4" height="13" rx="1"/>
              <rect x="16" y="3" width="4" height="18" rx="1"/>
            </svg>
          </span>
          <h3 class="jc-card__title">Terpercaya</h3>
          <p class="jc-card__desc">
            Data tersimpan rapi dan aman. Pantau performa tiap cabang dari satu
            tempat.
          </p>
        </article>
      </div>

      <!-- 4. Bergaransi -->
      <div class="col-md-3 col-sm-6 jc-fitur__col">
        <article class="jc-card">
          <span class="jc-card__icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <circle cx="12" cy="9" r="6"/>
              <path d="m8.5 14-1.5 8 5-2.5 5 2.5-1.5-8"/>
              <path d="m10 9 1.5 1.5L14.5 7.5"/>
            </svg>
          </span>
          <h3 class="jc-card__title">Bergaransi</h3>
          <p class="jc-card__desc">
            Didampingi saat mulai, dibantu kalau ada kendala. Coba gratis 14 hari
            dulu.
          </p>
        </article>
      </div>
    </div>

    <div class="jc-fitur__cta">
      <a href="{{ url('/daftar') }}" class="jc-btn jc-btn--gold">Mulai Coba Gratis</a>
      <span class="jc-fitur__cta-note">Tanpa kartu kredit · Bisa berhenti kapan saja</span>
    </div>
  </section>
</template>
