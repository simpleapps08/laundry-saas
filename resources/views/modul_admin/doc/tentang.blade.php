@extends('layouts.backend')
@section('title','Tentang Aplikasi | Dokumentasi Javacom Laundry')
@section('content')
  <section id="knowledge-base-question">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-12 order-1 order-md-2">
          <div class="card">
              <img src=" {{asset('backend/images/doc/laundry-banner.png')}}">
              <div class="card-body">
                  <h4 class="card-title mb-1">
                      <i data-feather="smartphone" class="font-medium-5 mr-25"></i>
                      <span>Javacom Laundry</span> <hr>
                  </h4>
                  <p>
                    Javacom Laundry adalah sistem kasir dan manajemen usaha laundry yang dibangun untuk membantu pemilik usaha bekerja lebih rapi dan efisien. Aplikasi ini sudah mendukung <b>multi cabang</b>, sehingga Anda dapat mengelola beberapa outlet dalam satu sistem terpusat. <br>
                    <h5>Dikembangkan oleh Javacom</h5>
                    Javacom adalah Digital Growth Partner yang membantu bisnis dan organisasi membangun sistem digital yang lebih terstruktur, efisien, aman, dan siap berkembang. Setiap sistem dibangun dengan mengutamakan kesesuaian proses bisnis, keamanan data, kemudahan penggunaan, dan pemeliharaan jangka panjang.<br><br>
                    <h5>Dukungan</h5>
                    Butuh bantuan, penyesuaian fitur, atau integrasi tambahan? Tim Javacom siap membantu melalui WhatsApp atau email yang tertera di bawah.
                  </p>
              </div>
          </div>
      </div>
    </div>
  </section>
  <!-- contact me -->
  <section class="faq-contact">
      <div class="row mt-2 pt-75">
          <div class="col-12 text-center">
              <h2>Butuh Bantuan?</h2>
              <p class="mb-3">
              </p>
          </div>
          <div class="col-sm-6">
              <div class="card text-center faq-contact-card shadow-none py-1">
                  <div class="card-body">
                      <div class="avatar avatar-tag bg-light-primary mb-2 mx-auto">
                          <i class="font-medium-3 feather icon-phone-call"></i>
                      </div>
                      <h4><a href="https://wa.me/6285196269837" target="_blank">WhatsApp</a></h4>
                      <span class="text-body">Respon cepat pada jam kerja (Sen–Jum 08.00–17.00 WIB)</span>
                  </div>
              </div>
          </div>
          <div class="col-sm-6">
              <div class="card text-center faq-contact-card shadow-none py-1">
                  <div class="card-body">
                      <div class="avatar avatar-tag bg-light-primary mb-2 mx-auto">
                          <i class="font-medium-3 feather icon-mail"></i>
                      </div>
                      <h4><a href="mailto:javacom.dev@gmail.com">javacom.dev@gmail.com</a> </h4>
                      <span class="text-body">Tim Javacom siap membantu kebutuhan Anda</span>
                  </div>
              </div>
          </div>
      </div>
  </section>
  <!--/ contact me -->
@endsection