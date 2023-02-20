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
     {!! NoCaptcha::renderJs() !!}
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
                                    <li class="nav-item"><a class="nav-link mx-2" href="{{url('/landing')}}">Beranda</a></li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                    <!-- Page Header-->
                    <header class="page-header-ui page-header-ui-dark bg-gradient-primary-to-secondary">
                        <div class="page-header-ui-content pt-2">
                            <div class="container px-5">
                                <div class="row gx-5 align-items-center">

                                    <div class="col-lg-6 d-none d-lg-block" data-aos="fade-up" data-aos-delay="100"><img class="img-fluid" src="{{url('asset-landing/svg/data-processing.svg')}}" /></div>

                                    <div class="col-lg-1 d-none d-lg-block" data-aos="fade-up" data-aos-delay="100"></div>
                                    
                                    <div class="col-lg-5" data-aos="fade-up">
                                        <div class="card rounded-3 text-dark">
                                            <div class="card-header py-4">Login Admin Area</div>
                                            <div class="card-body">
                                                @if(Session::has('error'))
                                                    <div class="alert alert-danger" role="alert">
                                                  {{Session::get('error')}}
                                                </div>
                                                @endif
                                                <form action="/submit-login" method="post">
                                                    {{csrf_field()}}
                                                    <div class="mb-3">
                                                        <label class="small text-gray-600" for="leadCapEmail">User ID</label>
                                                        <input class="form-control form-control-lg" type="text" name="username" placeholder="Masukan username" />
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="small text-gray-600" for="leadCapCompany">Password</label>
                                                        <input class="form-control form-control-lg" type="password" name="password" placeholder="Masukan password" />
                                                    </div>
                                                     {!! NoCaptcha::display() !!}
                                                     <hr>
                                                    <div class="d-grid"><button type="submit"  class="btn btn-primary fw-500" type="submit">Masuk</button></div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                </div>
                            </div>
                        </div>
                        <div class="svg-border-rounded text-white" id="biaya-paket">
                            <!-- Rounded SVG Border-->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 144.54 17.34" preserveAspectRatio="none" fill="currentColor"><path d="M144.54,17.34H0V0H144.54ZM0,0S32.36,17.34,72.27,17.34,144.54,0,144.54,0"></path></svg>
                        </div>
                    </header>
                     
                    <section class="bg-white pt-10" >
                        
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

        @if(Session::has('error'))
            <script>
                $("#alertmodal").hide().slideDown();
                  setTimeout(function(){
                      $("#alertmodal").slideUp();        
                  }, 4000);
            </script>
            <div id="alertmsg" class="alert alert-dismissable alert-danger">
              <button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button> 
              <strong>Error</strong>: {{ Session::get('error') }}
          </div>
    @endif
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
