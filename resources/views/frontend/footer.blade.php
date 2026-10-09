<div id="footer" class="footer">
    <!-- begin container -->
    <div class="container">
        <!-- begin row -->
        <div class="row">
            <!-- begin col-4 -->
            <div class="col-xl-4 col-lg-4 col-12">
                <div class="section-container">
                    <h4>Tentang Javacom Laundry</h4>
                    <p>
                      {{$setpage != NULL ? $setpage->tentang : 'Sistem kasir & manajemen usaha laundry dari Javacom — Digital Growth Partner.'}}
                    </p>
                </div>
            </div>
            <!-- end col-4 -->

            <!-- begin col-4 -->
            <div class="col-xl-4 col-lg-4 col-12">
                <div class="section-container">
                    <h4>Informasi</h4>
                    <ul class="latest-post">
                      <li>
                        <a href="{{ route('signup.harga') }}">Harga &amp; Paket</a>
                      </li>
                      <li>
                        <a href="{{ route('signup.harga') }}">Coba Gratis 14 Hari</a>
                      </li>
                      <li>
                        <a href="{{ url('/') }}#lacak">Lacak Status Cucian</a>
                      </li>
                      <li>
                        <a href="{{ route('login') }}">Masuk ke Dashboard</a>
                      </li>
                    </ul>
                </div>
            </div>
            <!-- end col-4 -->

            <!-- begin col-4 -->
            <div class="col-xl-4 col-lg-4 col-12">
                <div class="section-container">
                    <h4>Hubungi Kami</h4>
                    <ul class="new-user">
                      <li>
                        <a href="https://facebook.com/{{$setpage->facebook ?? ''}}" target="_blank">
                          <i class="fa fa-facebook-square fa-2x" style="color: #4267B2"></i>
                        </a>
                      </li>
                      <li>
                        <a href="https://instagram.com/{{$setpage->instagram ?? ''}}" target="_blank">
                          <i class="fa fa-instagram fa-2x" style="color:#5B51D8"></i>
                        </a>
                      </li>
                      <li>
                        <a href="https://twitter.com/{{$setpage->twitter ?? ''}}" target="_blank">
                          <i class="fa fa-twitter fa-2x" style="color: #1DA1F2"></i>
                        </a>
                      </li>
                      <li>
                        <a href="mailto:{{$setpage->email ?? ''}}" target="_blank">
                          <i class="fa fa-envelope fa-2x" style="color: #DB4437"></i>
                        </a>
                      </li>
                      <li>
                        <a href="tel:{{$setpage->no_telp ?? ''}}" target="_blank">
                          <i class="fa fa-phone fa-2x"></i>
                        </a>
                      </li>
                    </ul>
                </div>
            </div>
            <!-- end col-4 -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
</div>
