<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
	<meta charset="utf-8" />
	<title>@yield('title')</title>
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
  <meta name="description" content="Javacom Laundry — sistem kasir dan manajemen usaha laundry berbasis web">
  <meta name="keywords" content="Javacom Laundry,Laundry,Javacom Laundry,Sistem Laundry">
  <meta name="author" content="Javacom">

	<!-- ================== BEGIN BASE CSS STYLE ================== -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
	<link href="{{asset('frontend/plugins/bootstrap3/css/bootstrap.min.css')}}" rel="stylesheet" />
	<link href="{{asset('frontend/plugins/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" />
	<link href="{{asset('frontend/plugins/animate/animate.min.css')}}" rel="stylesheet" />
	<link href="{{asset('frontend/css/forum/style.css')}}" rel="stylesheet" />
	<link href="{{asset('frontend/css/forum/style-responsive.min.css')}}" rel="stylesheet" />
	<link href="{{asset('frontend/css/forum/theme/default.css')}}" id="theme" rel="stylesheet" />
	{{-- Tema publik Javacom Laundry (navy #0b1326 -> gold #ffd700).
	     Dimuat PALING AKHIR supaya menimpa style bawaan repo. --}}
	<link href="{{ asset('frontend/css/javacom-laundry.css') }}?v={{ @filemtime(public_path('frontend/css/javacom-laundry.css')) }}" rel="stylesheet" />
	<!-- ================== END BASE CSS STYLE ================== -->

	<!-- ================== BEGIN BASE JS ================== -->
    <script src="{{asset('frontend/plugins/pace/pace.min.js')}}"></script>

    <!-- ================== END BASE JS ================== -->
    <style type="text/css">
        body {
            overflow-x: hidden;
        }

        /* ── Navbar CTA (menu Harga / Coba Gratis) ──────────────────
           Tombol ajakan daftar di navbar kanan. Bootstrap 3 default
           navbar-dark; tombol perlu warna kontras sendiri. */
        .navbar .btn-nav-cta {
            background: #ffd700;
            color: #0b1326 !important;
            font-weight: 700;
            border-radius: 8px;
            margin-top: 9px;
            padding: 8px 18px !important;
            line-height: 1.4 !important;
            transition: .2s;
        }
        .navbar .btn-nav-cta:hover,
        .navbar .btn-nav-cta:focus {
            background: #e6c200;
            color: #0b1326 !important;
        }
        /* Di layar kecil, tombol jadi penuh lebar supaya mudah ditekan. */
        @media (max-width: 767px) {
            .navbar .btn-nav-cta {
                margin: 6px 15px 12px;
                text-align: center;
                display: block;
            }
        }
        /* Anchor "Lacak Cucian" — beri offset supaya judul tidak
           tertutup navbar yang position:fixed. */
        #lacak { scroll-margin-top: 80px; }
    </style>
</head>
<body>
    <!-- begin #header -->
    @yield('header')
    <!-- end #header -->

    <!-- begin search-banner -->
    <div class="search-banner has-bg">
       @yield('banner')
    </div>
    <!-- end search-banner -->

    <!-- begin content -->
    <div class="content">
        <!-- begin container -->
        <div class="container-fluid">
          <div id="app">
            @yield('content')
          </div>
        </div>
        <!-- end container -->
    </div>
    <!-- end content -->

    <!-- begin #footer -->
    @yield('footer')
    <!-- end #footer -->

    <!-- begin #footer-copyright -->
    <div class="footer-copyright">
        <div class="container">
            &copy; <?php echo date("Y") ?> Build With <i class="fa fa-heart" style="color:red"></i> - <a href="https://javacom.co.id" target="_blank" style="text-decoration:none">Javacom</a>
        </div>
    </div>
    <!-- end #footer-copyright -->
	<!-- ================== BEGIN BASE JS ================== -->
  {{-- Cache-busting: token berubah tiap file di-rebuild, jadi Cloudflare
       (max-age 4 jam) langsung ambil versi baru tanpa perlu purge manual. --}}
  <script src="{{ asset('js/app.js') }}?v={{ @filemtime(public_path('js/app.js')) ?: '1' }}" ></script>
	<script src="{{asset('frontend/plugins/jquery/jquery-3.2.1.min.js')}}"></script>
	<script src="{{asset('frontend/plugins/bootstrap3/js/bootstrap.min.js')}}"></script>
	<script src="{{asset('frontend/plugins/js-cookie/js.cookie.js')}}"></script>
    <script src="{{asset('frontend/js/forum/apps.min.js')}}"></script>
    <script src="{{asset('frontend/js/swal/sweetalert2.all.min.js')}}"></script>
	<!-- ================== END BASE JS ================== -->

	<script>
	    $(document).ready(function() {
	        App.init();
	    });
    </script>
    @yield('scripts')
</body>
</html>
