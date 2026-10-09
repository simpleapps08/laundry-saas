{{--
  Navbar publik — Javacom Laundry
  Ditambahkan: menu navigasi ke halaman harga/langganan (/daftar) supaya
  pengunjung bisa menemukan jalan mendaftar. Sebelumnya halaman /daftar
  ada tapi tanpa tautan dari mana pun.

  Struktur: Home · Harga · Lacak Cucian | (auth: Dashboard) (guest: Masuk + CTA)
  Bootstrap 3 — navbar-collapse untuk mobile.

  PENTING: wrapper di bawah WAJIB utuh — <div id="header" class="navbar...">
  + <div class="container"> + <div class="navbar-header">. Menghilangkan
  salah satunya membuat `.navbar .btn-nav-cta` tidak match (elemen jadi
  menggantung tanpa parent .navbar) → CSS tombol CTA tidak jalan.
--}}
<div id="header" class="header navbar navbar-default navbar-fixed-top">
  <!-- begin container -->
  <div class="container">
      <!-- begin navbar-header -->
      <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#header-navbar">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
          </button>
          <a href="{{ url('/') }}" class="navbar-brand">
              <span class="navbar-logo"></span>
              <span class="brand-text">
                  {{ $setpage != NULL ? $setpage->judul : 'Javacom Laundry' }}
              </span>
          </a>
      </div>
      <!-- end navbar-header -->

      <!-- begin #header-navbar -->
      <div class="collapse navbar-collapse" id="header-navbar">
          <ul class="nav navbar-nav navbar-right">
              <li><a href="{{ url('/') }}">Home</a></li>
              <li><a href="{{ route('signup.harga') }}">Harga</a></li>
              <li><a href="{{ url('/') }}#lacak">Lacak Cucian</a></li>

              @auth
                  <li><a href="{{ url('/home') }}">Dashboard</a></li>
              @else
                  <li><a href="{{ route('login') }}">Masuk</a></li>
                  <li>
                      <a href="{{ route('signup.harga') }}" class="btn-nav-cta">Coba Gratis 14 Hari</a>
                  </li>
              @endauth
          </ul>
      </div>
      <!-- end #header-navbar -->
  </div>
  <!-- end container -->
</div>
<!-- end #header -->
