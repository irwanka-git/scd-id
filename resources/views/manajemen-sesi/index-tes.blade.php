<?php
// tabel: quiz_sesi_template
// quiz_sesi_template
// id_quiz_template int
// nama_sesi        varchar
// skoring_tabel    varchar
// uuid             char


loadHelper('akses,function');

$id_role_biro = 5;
$main_path = Request::segment(1);
$isAdminSistem = isAdminSistem();
$isAdminBiro = isAdminBiro();
$currentURL = URL::current();
?>
@extends('layout')
@section("css")
<link rel="stylesheet" href="//cdn.quilljs.com/1.3.6/quill.snow.css" />
@endsection
@section("pagetitle")

@endsection

@section('content')

 <div class="container-fluid p-0">
 					@if($isAdminSistem)
					<a href="#" data-bs-toggle="modal" data-bs-target="#modal-tambah" class="btn btn-primary float-end mt-n1"><i class="fas fa-plus"></i> Sesi Tes Baru</a>
					@endif
					<h1 class="h3 mb-3">Manajemen Sesi Tes</h1>
					<nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
					  <ol class="breadcrumb">
					    <li class="breadcrumb-item"><a href="{{url('/manajemen-sesi')}}">Manajemen Sesi Tes</a></li>
					    <li class="breadcrumb-item"><a href="{{url('/manajemen-sesi/explore/'.$data_tahun['tahun'])}}">Tahun {{$data_tahun['tahun']}}</a></li>
					    <li class="breadcrumb-item"><a href="{{url('/manajemen-sesi/explore/'.$data_tahun['tahun'].'/'.$data_biro['id_user_biro'])}}">{{$data_biro['nama_biro']}} ({{$data_biro['jumlah_tes']}})</a></li>
					    <li class="breadcrumb-item"><a href="{{url('/manajemen-sesi/explore/'.$data_tahun['tahun'].'/'.$data_biro['id_user_biro'].'/'.$data_provinsi->id)}}">{{$data_provinsi->name}} ({{$data_provinsi->jumlah_tes}})</a></li>
					    <li class="breadcrumb-item active">
					    	{{$data_lokasi->nama_lokasi}}
					    </li>
					  </ol>
					</nav>
					<div class="row">
						<div class="col-12">
							<div class="card">
								<div class="card-header px-4 pt-4">
									<i class="las la-map-marker-alt"></i> {{$data_lokasi->kabupaten}}<br>
									<b><i class="las la-university"></i> {{$data_lokasi->nama_lokasi}}</b>
								</div>
							</div>
						</div>
					<style type="text/css">
						.badge-open{
							--bs-bg-opacity: 1;
    						background-color: rgba(var(--bs-success-rgb),var(--bs-bg-opacity))!important;
    						font-size: 0.85em !important;
						}
						.badge-close{
							--bs-bg-opacity: 1;
    						background-color: rgba(var(--bs-danger-rgb),var(--bs-bg-opacity))!important;
    						font-size: 0.85em !important;
						}
					</style>
					<div class="row">
						@foreach($list_tes as $r)
						
							<div class="col-12 col-md-6 col-lg-3">
								<div class="card">
									@if($r->open==1)
									<div class="badge badge-sidebar-primary badge-open">Buka</div>
									@else
									<div class="badge badge-sidebar-primary badge-close">Tutup</div>
									@endif
								<img class="card-img-top" src="{{asset('gambar/'.$r->gambar)}}" alt="Unsplash">
								<div class="card-body px-4 pt-2">
										{{tgl_indo_lengkap($r->tanggal)}}
										<a href="{{url('/manajemen-sesi/detil/'.Crypt::encrypt($r->id_quiz).'?back='.$currentURL)}}"><h5 class="card-title mb-0">{{$r->nama_sesi}}</h5></a>
									 
										<span class="mb-2 fw-bold"><i class="las la-tag"></i> {{$r->jenis_tes}}</span>
									 <p class="pt-2">
										<span class="mb-2 fw-bold"><i class="las la-user-alt"></i> Peserta <span class="float-end">{{$r->peserta}}</span></span><br>
										<span class="mb-2 fw-bold"><i class="las la-list-ol"></i> Jumlah Sesi <span class="float-end">{{$r->jumlah_sesi}}</span></span>
										

									</p>
								</div>
								</div>
							</div>

						@endforeach
						@if(count($list_tes)==0)
						<div class="col-12">
							<div class="alert alert-primary mb-5" role="alert">
								<div class="alert-message">
									<strong>Perhatian:</strong> Untuk saaat sesi tes belum tersedia, silahkan hubungi admin <b>Si Cerdas Indonesia</b> untuk mendaftarkan sesi tes yang akan diselenggarakan!.
								</div>
							</div>
						</div>
						@endif
					</div>
				</div>

@endsection

@section("js")
<script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script type="text/javascript">
	$(function() {
		//var toolbarOptions = [['bold', 'italic'],['link', 'image']];
	})
</script>
@if($isAdminSistem)
@include("manajemen-sesi.form-tambah")
@endif
@endsection