<template>
  <!--
    Section "Solusi" — Javacom Laundry (Modern SaaS)
    Diganti 9-Okt-2026: ilustrasi flat supLy.png (gaya repo asli) diganti
    visual CSS/SVG gradient navy→gold. Tanpa foto stock / ilustrasi pihak ketiga.
  -->
  <section class="jc-solusi" id="solusi">
    <div class="row jc-solusi__row">
      <!-- Kiri: visual -->
      <div class="col-md-6 jc-solusi__visual">
        <div class="jc-visual-card">
          <div class="jc-visual-card__glow"></div>

          <!-- Mock kartu pesanan -->
          <div class="jc-mock">
            <div class="jc-mock__head">
              <span class="jc-mock__dot"></span>
              <span class="jc-mock__dot"></span>
              <span class="jc-mock__dot"></span>
              <span class="jc-mock__title">Pesanan Masuk</span>
            </div>
            <div class="jc-mock__body">
              <div class="jc-mock__row">
                <span class="jc-mock__label">TR0392928</span>
                <span class="jc-mock__pill jc-mock__pill--gold">Dijemput</span>
              </div>
              <div class="jc-mock__row">
                <span class="jc-mock__label">Cuci Kering · 3 kg</span>
                <span class="jc-mock__pill">Proses</span>
              </div>
              <div class="jc-mock__bar"><span></span></div>
              <div class="jc-mock__row jc-mock__row--muted">
                <span>Estimasi selesai</span>
                <span>Besok, 14:00</span>
              </div>
            </div>
          </div>

          <!-- Badge kurir mengambang -->
          <div class="jc-float jc-float--1">
            <i class="fa fa-motorcycle"></i>
            <div>
              <strong>Kurir di jalan</strong>
              <small>Jemput &amp; antar</small>
            </div>
          </div>

          <div class="jc-float jc-float--2">
            <i class="fa fa-check"></i>
            <div>
              <strong>Selesai</strong>
              <small>Notif ke pelanggan</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Kanan: teks -->
      <div class="col-md-6 jc-solusi__text">
        <span class="jc-section-badge">Layanan Jemput Antar</span>
        <h2 class="jc-solusi__title">
          Pelanggan Tak Perlu<br><span class="gold">Antre di Tempat</span>
        </h2>
        <p class="jc-solusi__desc">
          Pelanggan pesan online, kurir menjemput, dan status cucian bisa dilacak
          sendiri. Kamu fokus mengerjakan — sisanya jalan otomatis.
        </p>

        <ul class="jc-solusi__list">
          <li>
            <span class="jc-check"><i class="fa fa-check"></i></span>
            <div>
              <strong>Pelacakan status otomatis</strong>
              <small>Pelanggan cek sendiri lewat nomor nota</small>
            </div>
          </li>
          <li>
            <span class="jc-check"><i class="fa fa-check"></i></span>
            <div>
              <strong>Notifikasi WhatsApp &amp; Telegram</strong>
              <small>Kabari pelanggan saat cucian siap</small>
            </div>
          </li>
          <li>
            <span class="jc-check"><i class="fa fa-check"></i></span>
            <div>
              <strong>Jadwal jemput &amp; antar</strong>
              <small>Kelola rute kurir dari satu dashboard</small>
            </div>
          </li>
        </ul>

        <a href="#mengapa-kami" class="jc-link-arrow">
          Lihat fitur lainnya <i class="fa fa-arrow-right"></i>
        </a>
      </div>
    </div>
  </section>
</template>
