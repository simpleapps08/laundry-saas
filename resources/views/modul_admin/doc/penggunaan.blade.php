@extends('layouts.backend')
@section('title','Instalasi & Penggunaan | Dokumentasi Javacom Laundry')
@section('content')
  <section id="knowledge-base-question">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-12 order-1 order-md-2">
          <div class="card">
              <div class="card-body">
                  <h4 class="card-title mb-1">
                      <i data-feather="smartphone" class="font-medium-5 mr-25"></i>
                      <span>Instalasi & Penggunaan Javacom Laundry</span> <hr>
                  </h4>
                  <p>
                    <h5>Requirements</h5>
                    <ul>
                        <li>Version 1.x , 2.x or 3.x use PHP 7.3 (Framework Laravel 8) Versi 3.x</li>
                        <li> Version 3.1 use PHP 8.0 (Framework Laravel 9) Versi 3.1</li>
                        <li> Database (eg: MySQL)</li>
                        <li> Web Server (eg: Apache, Nginx, IIS)</li>
                    </ul>

                    <h5>Installation</h5>
                    <ul>
                        <li>Install Composer and Npm</li>
                        <li>Clone the repository: <code> git clone https://javacom.co.id </code> </li>
                        <li>Install dependencies: <code> composer install ; npm install ; npm run dev</code></li>
                        <li>Run cp .env.example .env for create .env file</li>
                        <li> Run php artisan migrate --seed for migration database</li>
                        <li>Run php artisan storage:link for create folder storage</li>
                        <li> Run php artisan create:admin for create user Administrator</li>
                        <li> Run php artisan queue:listen for run queue</li>
                        <li>Run <code> php artisan serve</code> for start app</li>
                    </ul>

                    <h5>Credentials</h5>
                    <ul>
                      <li> Login sebagai Karyawan
                        <ul>
                          <li>Untuk membuat akun karyawan silahkan pergi ke menu <b> Data User > Karyawan</b> </li>
                          <li>Untuk Password, silahkan masukan<code> 123456</code> </li>
                        </ul>
                      </li>
                    </ul>
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
