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
					    <li class="breadcrumb-item active">Tahun {{$data_tahun['tahun']}} ({{$data_tahun['jumlah_tes']}})</li>
					  </ol>
					</nav>
					<div class="row">
						@foreach($list_biro as $r)
						<div class="col-12 col-md-6 col-lg-3">
							<div class="card">
								<div class="card-header px-4 pt-4">
									<?php 
									//$url_detil = url('/manajemen-sesi/explore/'.$r->tahun);
									$url_detil = url('/manajemen-sesi/explore/'.$data_tahun['tahun'].'/'.$r->uuid_user_biro);
									?>
									<a href="{{$url_detil}}"><h5 class="card-title mb-0"><i class="las la-user-tie"></i> {{$r->nama_biro}}</h5></a>
									<p class="mb-2 fw-bold">Jumlah Tes<span class="float-end">{{$r->jumlah_sesi}}</span></p>
								</div>
							</div>
						</div>
						@endforeach
						@if(count($list_biro)==0)
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