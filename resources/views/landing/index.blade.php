<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Si Cerdas Indonesia</title>
        <link href="{{url('asset-landing/css/styles.css')}}" rel="stylesheet" />
        <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
        <link rel="icon" type="image/x-icon" href="assets/img/favicon.png" />
        <script data-search-pseudo-elements="" defer="" src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.24.1/feather.min.js" crossorigin="anonymous"></script>
    </head>
    <style type="text/css">
    	.bg-gradient-primary-to-secondary {
		    background-color: #6c63ff !important;
		    background-image: linear-gradient(135deg, #5042c7 0%, rgb(195 11 241 / 80%) 100%) !important;
		}
    </style>
    <body>
        <div id="layoutDefault">
            <div id="layoutDefault_content">
                <main>
                    <!-- Navbar-->
                    <nav class="navbar navbar-marketing navbar-expand-lg bg-transparent navbar-dark fixed-top">
                        <div class="container px-5">
                        	<img height="30px" class="mx-2" 
                                	src="{{url('asset-landing/svg/white-sicerdas.svg')}}"> 
                            <a class="navbar-brand text-white" href="{{url('/')}}">
                            	Si Cerdas Indonesia</a>
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i data-feather="menu"></i></button>
                            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                <ul class="navbar-nav ms-auto me-lg-5">
                                    <li class="nav-item"><a class="nav-link mx-2" href="{{url('/')}}">Beranda</a></li>
                                    <li class="nav-item"><a class="nav-link mx-2" href="#biaya-paket">Biaya</a></li>
                                    <li class="nav-item"><a class="nav-link mx-2" href="#kontak-kami">Kontak Kami</a></li>
                                    <li class="nav-item"><a class="nav-link mx-2" href="{{url('/login')}}">Login</a></li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                    <!-- Page Header-->
                    <header class="page-header-ui page-header-ui-dark bg-gradient-primary-to-secondary">
                        <div class="page-header-ui-content pt-5">
                            <div class="container px-5">
                                <div class="row gx-5 align-items-center">
                                    <div class="col-lg-7" data-aos="fade-up">
                                        <h1 class="page-header-ui-title">
                                        Cepat, Murah dan Akurat 
                                    	</h1>
                                        <p class="page-header-ui-text mb-2">
                                        Layanan Tes psikologi untuk penjurusan dan minat bakat. Penjurusan kuliah memberikan rekomendasi jurusan di perguruan tinggi umum, sekolah kedinasan dan uin/iain. Penjurusan SMA rekomendasi untuk peminatan di SMA, MAN maupun SMK. Minat bakat terkait dengan pengembangan diri, bakat/kecerdasan majemuk dan potensi karier masa depan.
Direkomendasikan untuk Siswa SMA, SMP maupun Mahasiswa
                                    	</p>
                                    	<hr>
                                    	<p>Belum punya aplikasi Si Cerdas Indonesia? Yuk, unduh sekarang juga.</p>
                                         <a class="btn btn-white fw-500 me-2" href="https://play.google.com/store/apps/details?id=com.sicerdas.id">
                                            <i class="mx-2 fa-brands fa-google-play"></i> 
                                             Play Store
                                         </a>
                                          <a class="btn btn-white fw-500 me-2" href="#">
                                            <i class="mx-2 fa-brands fa-app-store"></i> 
                                             App Store
                                         </a>
                                        <a class="btn btn-white fw-500 me-2" href="https://s.id/scid">
                                            <i class="mx-2 fa-brands fa-windows"></i> 
                                             Windows
                                         </a>
                                    </div>
                                    <div class="col-lg-5 d-none d-lg-block" data-aos="fade-up" data-aos-delay="100"><img class="img-fluid" src="{{url('asset-landing/svg/8.svg')}}" /></div>
                                </div>
                            </div>
                        </div>
                        <div class="svg-border-rounded text-white" id="biaya-paket">
                            <!-- Rounded SVG Border-->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 144.54 17.34" preserveAspectRatio="none" fill="currentColor"><path d="M144.54,17.34H0V0H144.54ZM0,0S32.36,17.34,72.27,17.34,144.54,0,144.54,0"></path></svg>
                        </div>
                    </header>
                     
                    <section class="bg-white pt-10" >
                        <div class="container px-5" >
                            <div class="text-center mb-5">
                                <h2>Biaya / Paket Psikotes Si Cerdas Indonesia</h2>
                            </div>
                            <div class="row gx-5 z-1">
                            	<?php
                            	$tarif_paket = DB::table('tarif_paket')->orderby('id_tarif','asc')->limit(3)->get();
                            	?>
                            	@foreach($tarif_paket as $paket)
                                <div class="col-lg-4 mb-5 mb-lg-n10" data-aos="fade-up">
                                    <div class="card pricing">
                                        <div class="card-body p-5">
                                            <div class="text-center">
                                                <div class="badge bg-primary-soft rounded-pill badge-marketing badge-sm text-primary">{{$paket->nama_tarif}}</div>
                                                <div class="pricing-price">
                                                    <span class="pricing-price-period">Rp. {{number_format($paket->tarif,0,",",".")}} /Peserta</span>
                                                </div>
                                            </div>
                                           <ul class="fa-ul pricing-list">
                                           	<?php
                                           	$tarif_paket_rincian = DB::table('tarif_paket_rinci')
                                           				->where('id_tarif', $paket->id_tarif)
                                           				->orderby('urutan','asc')->get(); 
                                           ?>
                                           @foreach($tarif_paket_rincian as $rinci)
                                                <li class="pricing-list-item">
                                                    <span class="fa-li"><i class="far fa-check-circle text-teal"></i></span>
                                                    <span class="text-dark">{{$rinci->nama_rincian}}</span>
                                                </li>
                                            @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>  
                                @endforeach
                            </div>
                        </div>
                        <div class="svg-border-rounded text-dark">
                            <!-- Rounded SVG Border-->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 144.54 17.34" preserveAspectRatio="none" fill="currentColor"><path d="M144.54,17.34H0V0H144.54ZM0,0S32.36,17.34,72.27,17.34,144.54,0,144.54,0"></path></svg>
                        </div>
                    </section>
                    <section class="bg-dark py-5">
                         
                    </section> 
                </main>
            </div>

            <div id="layoutDefault_footer">
                <footer class="footer pt-10 pb-5 mt-auto bg-dark footer-light">
                		<div class="container px-5">
	                        <div class="row gx-5">
                    		 <div class="col-lg-6">
                    		 	<div id="kontak-kami">
                                	<p>Ingin menggunakan layanan Psikotes Si Cerdas Indonesia.<br>Segera hubungi kami di:</p>
                                	<?php 
                                	$kontak = DB::table('kontak')->first();
                                	?>
                                     <a class="btn btn-white fw-500 me-2" href="{{$kontak->wa_me}} ">
                                        <span style="color: #000 !important;">
                                        	<i class="mx-2 fa-brands fa-whatsapp"></i> 
                                         	Whatsapp {{$kontak->telepon}} 
                                        </span>
                                     </a>
                                      <a class="btn btn-white fw-500 me-2" href="mailto:{{$kontak->email}}">
                                        <span style="color: #000 !important;">
                                        	<i class="mx-2 fa-solid fa-envelope"></i>
                                         	Email 
                                        </span>
                                     </a>
                                </div>
                                </div>
                               <div class="col-lg-6">
	                                <div class="mb-3">Ikuti Kami di Media Soial</div>
	                                <div class="icon-list-social mb-5">
	                                    <a class="icon-list-social-link mx-2" target="_blank"  href="http://instagram.com/sicerdasindonesia6	">
	                                    	<i class="fa-brands fa-instagram"></i>
	                                    	Instagram
	                                    </a>
	                                    <a class="icon-list-social-link  mx-2" target="_blank"  href="https://www.tiktok.com/@sicerdasind	">
	                                    	<i class="fa-brands fa-tiktok"></i>
	                                    	Tiktok
	                                    </a>
	                                    <a class="icon-list-social-link  mx-2" target="_blank" href="https://www.youtube.com/channel/UCaJquAWz8irk-q-Vl2o8Diw	">
	                                    	<i class="fa-brands fa-youtube"></i>
	                                    	Youtube
	                                    </a>
	                                </div>
	                                
	                            </div>
	                            <span class="mx-2 my-5">&copy 2022 - Si Cerdas Indonesia</span>
	                        </div>
	                        
	                    </div>
                </footer>
             </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="{{url('asset-landing/js/scripts.js')}}"></script>
        <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
         <script>
            AOS.init({
                disable: 'mobile',
                duration: 600,
                once: true,
            });
        </script>
</body>
</html>
