<?php
$main_path = Request::segment(1);
loadHelper('akses,function');
$isAdminSistem = isAdminSistem();
$back_url = Request::get('back');
?>
@extends('layout')
@section("css")
<link rel="stylesheet" href="//cdn.quilljs.com/1.3.6/quill.snow.css" />
@endsection
@section("pagetitle")

@endsection

@section('content')
<style type="text/css">
	.img-border{
		border: 1px solid #454545 !important;
	}
</style>
<div class="row mb-3">
	<div class="col-12 mb-xl-3">
		<a href="{{$back_url}}" class="btn btn-secondary"> <i class="la la-arrow-left"></i> Kembali</a> 
		<span class="float-end">
			<p>
				@if(ucu())
					<button  data-bs-toggle="modal" data-bs-target="#modal-asesor"  class="btn btn-outline-secondary"><i class="las la-user"></i> Pengaturan Asesor</button>&nbsp;
					<button  data-bs-toggle="modal" data-bs-target="#modal-edit"  class="btn btn-outline-secondary"><i class="las la-edit"></i> Ubah Data</button>&nbsp;
					@if($quiz->open==0)
					<button id="btn-konfirm-status" class="btn  btn-outline-secondary btn-block"><i class="las la-unlock-alt"></i>Buka Sesi Tes</button>&nbsp;
					@endif
					@if($quiz->open==1)
					<button id="btn-konfirm-status"  class="btn  btn-outline-secondary btn-block"><i class="las la-unlock-alt"></i>Tutup Sesi Tes</button>&nbsp;
					@endif
					
				@endif
			</p>
		</span>
		
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-md-4 col-lg-3">
		<div class="card">
			<img class="card-img-top" src="{{url('gambar/'.$quiz->cover)}}" alt="Unsplash">
			<div class="card-body">
				<center>
					<span class='badge bg-secondary' style="padding: 4px;"><i class="las la-sticky-note"></i> Token: {{$quiz->token}} </span>

					@if($quiz->open==1)
					<span class='badge bg-success' style="padding: 4px;"><i class="las la-play"></i> Sesi Tes Dibuka</span>
					@else
					<span class='badge bg-danger'  style="padding: 4px;"><i class="las la-lock"></i> Sesi Tes Ditutup</span>
					@endif


					<h4 class="card-title mb-1 mt-4">{{$quiz->nama_sesi}}</h4>
					<p class="card-text">
						<i class="las la-building"></i> {{$quiz->nama_lokasi}} <br>
						<i class="las la-calendar"></i> {{tgl_indo_lengkap($quiz->tanggal) ? tgl_indo_lengkap($quiz->tanggal) : ' Tidak Ada Jadwal' }}
						<br>
						<i class="las la-tag"></i> {{$quiz->jenis_tes}}<br>
						@if($quiz->nama_asesor)
						<hr> Asesor: <br><b>{{$quiz->nama_asesor}}</b><br>
						<small>SIPP: {{$quiz->nomor_sipp}}</small>
						@endif
					</p>
				</center>
				<div class="d-grid gap-2">
					@if($quiz->nama_asesor=="")
						<span class="bg-danger p-1">
							<center style="color:white;">Data Asesor Belum Diatur!</center>
						</span>
					@endif
					{{-- <button data-bs-toggle="modal" data-bs-target="#modal-gambar" 
					class="btn btn-outline-secondary btn-block"><i class="las la-upload"></i> Ganti Gambar / Cover</button> --}}
				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-md-8 col-lg-9">
		<div class="card">
			<div class="card-body">
				<ul class="nav nav-tabs" id="myTab" role="tablist">

					<li class="nav-item" role="presentation">
						<button class="nav-link active" id="peserta-tab" data-bs-toggle="tab" data-bs-target="#daftar-peserta" type="button" role="tab" aria-controls="daftar-peserta" aria-selected="false">Peserta <span class="result-jumlah-peserta"></span></button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link " id="sesi-tab" data-bs-toggle="tab" data-bs-target="#daftar-sesi" type="button" role="tab" aria-controls="daftar-sesi" aria-selected="true">Informasi Sesi</button>
					</li>
					<!-- TAMBAHAN KHUSUS -->
					@if($quiz->skoring_tabel=='skoring_minat_smk' || $quiz->skoring_tabel=='skoring_minat_smk_v2')
					<li class="nav-item" role="presentation">
						<button class="nav-link " id="sesi-tab" data-bs-toggle="tab" data-bs-target="#addons-pilihan-smk" type="button" role="tab" aria-controls="daftar-sesi" aria-selected="true">Pilihan SMK</button>
					</li>
					@endif					 
					<!-- TAMBAHAN KHUSUS -->
				</ul>
				<div class="tab-content" id="myTabContent">

					<div class="tab-pane fade show active  p-1  pt-4" id="daftar-peserta" role="tabpanel" aria-labelledby="peserta-tab">
						
						<div class="float-end">
							<div class="col-auto ms-auto text-end mt-n1">
								<div class="dropdown me-2 d-inline-block">
									<a class="btn btn-light bg-white shadow-sm dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-display="static">
										Actions
									</a>
									<div class="dropdown-menu dropdown-menu-end">
										@if(ucu())
											<a class="dropdown-item" href="#" 
											data-bs-toggle="modal" 
											data-bs-target="#modal-tambah-peserta" ><i class="las la-plus-circle"></i> Tambah Peserta</a>
											<a class="dropdown-item" href="#"
											data-bs-toggle="modal" 
											data-bs-target="#modal-upload-peserta" ><i class="las la-cloud-upload-alt"></i> Upload Peserta</a>
											<a class="dropdown-item" href="#"
											data-bs-toggle="modal" 
											data-bs-target="#modal-salin-peserta" ><i class="las la-copy"></i> Salin Peserta</a>
											<a class="dropdown-item" id="btn-kosongkan-peserta" href="#"><i class="las la-trash"></i> Kosongkan Peserta</a>
											<a class="dropdown-item" id="btn-batal-skoring-all" href="#"><i class="las la-recycle"></i> Batalkan Skoring (Semua)</a>
											<div class="dropdown-divider"></div>
											<a class="dropdown-item" id="btn-publish-all" href="#"><i class="las la-paper-plane"></i> Publish Hasil Tes (per 100 Peserta)</a>
											<a class="dropdown-item" id="btn-batal-publish-all" href="#"><i class="las la-paper-plane"></i> Batalkan Publish (Semua)</a>
										@endif
										<a class="dropdown-item" href="{{url('manajemen-sesi/export-excel/'.$quiz->uuid)}}"><i class="las la-file-excel"></i> Export Data Hasil (Excel)</a>
										<a class="dropdown-item" href="#"
										data-bs-toggle="modal" 
										data-bs-target="#modal-download-all-report"><i class="las la-file-archive"></i> Download Report (Semua PDF)</a>
										<a class="dropdown-item" href="#"
										data-bs-toggle="modal" 
										data-bs-target="#modal-download-all-report-doc"><i class="las la-file-archive"></i> Download Report (Semua Doc)</a>
									</div>
								</div>
							</div>
						</div>
						
						<h6 class="mb-4">
							<button class=" btn btn-primary" id="btn-refresh-data"  type="button"> <i class="la la-refresh"></i> Refresh Data</button>
							<div class="float-end">
							<p>
								<span class="badge bg-warning" id="rekap-submit">Submit: {{(int)$rekap_status_peserta->submit}}</span>
								<span class="badge bg-primary" id="rekap-skoring">Skoring: {{(int)$rekap_status_peserta->skoring}}</span>
								<span class="badge bg-success" id="rekap-publish">Publish: {{(int)$rekap_status_peserta->publish}}</span>
								<span class="badge bg-danger" id="rekap-belum-publish">Belum Publish: {{(int)$rekap_status_peserta->skoring - $rekap_status_peserta->publish}}</span>
								&nbsp;
								&nbsp;
								&nbsp;
							</p>
							</div>
						</h6>
						<hr>

						<table id="tabel-peserta" class="table table-striped table-hover table-sm" style="width:100%">
							<thead>
								<tr>							
									<th width="4%">No</th>
									<th width="28%">User</th>
									<th width="20%">Asal/Sekolah/Organisasi</th>
									<th width="7%">Start</th>
									<th width="7%">Submit</th>
									<th width="7%">Skoring</th>
									<th width="7%">Publish</th>
									<th width="18%">Actions</th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>

					<div class="tab-pane  p-1 pt-4 fade " id="daftar-sesi" role="tabpanel" aria-labelledby="sesi-tab">
						@if($isAdminSistem && $quiz->open==1)
						<a href="{{$quiz->json_url}}" target="_blank" class="btn btn-secondary"><i class="la la-archive"></i> Download Soal / Konfigurasi</a>
						<hr>
						@endif
						<h4>Daftar Urutan Sesi Tes</h4>
						<hr>
						<table  class="table table-striped table-hover table-sm" style="width:100%">
							<thead>
								<tr>							
									<th width="5%">No</th>
									<th width="50%">Nama Sesi Tes</th>
									<th width="15%">Metode Tes</th>
									<th width="15%">Durasi (Menit)</th>
									<th width="15%">Waktu</th>
									@if($isAdminSistem)
									<th width="2%"></th>
									@endif
								</tr>
							</thead>
							<tbody>
								@foreach($quiz_sesi as $r)
								<tr>
									<td align="center">{{$r->urutan}}</td>
									<td>{{$r->nama_sesi_ujian}}</td>
									<td align="center">{{get_nama_metode_tes($r->mode)}}</td>
									<td align="center">{{$r->durasi}} Menit</td>
									<td align="center">
										@if($r->kunci_waktu) <i class="las la-lock"></i> Flat/Kaku @endif
										@if($r->kunci_waktu==0) <i class="las la-clock"></i> Fleksibel @endif
									</td>
									@if($isAdminSistem && $quiz->open==0)
									<td align="center">
										 <button data-bs-tip="tooltip" data-bs-placement="top" data-bs-original-title="Pengaturan Waktu" data-uuid="{{$r->id_quiz_sesi}}" 
										 	data-nama = "{{$r->nama_sesi_ujian}}"
										 	data-durasi = "{{$r->durasi}}"
										 	data-kunci = "{{$r->kunci_waktu}}"
										 	class="btn btn-atur-waktu 
                            btn-light btn-xs" type="button"><ion-icon name="create-outline"></ion-icon></button>
									</td>
									@endif
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>

					@if($quiz->skoring_tabel=='skoring_minat_smk' || $quiz->skoring_tabel=='skoring_minat_smk_v2')
						<div class="tab-pane p-1 pt-4 fade " id="addons-pilihan-smk" role="tabpanel" aria-labelledby="sesi-tab">
						</div>
					@endif
				</div>
			</div>
		</div>		
	</div>

</div>

@endsection

@section("modal")

@if(ucu())
<!-- MODAL FORM EDIT -->
{{ Form::bsOpen('form-gambar',url($main_path."/update-gambar")) }}
{{Html::mOpenLG('modal-gambar','Ubah Gambar')}}
<div class="mb-3">
	<p>Ukuran gambar yang direkomendasikan adalah 400 x 250 pixel</p>
	<div class="input-group">
		<button id="btn_gambar" class="btn btn-primary btn-upload-gambar" data-field="gambar" 
		data-form="form-gambar" type="button"><i class="la la-upload"></i> Upload Gambar</button>
		<input type="text" required="required"  readonly="readonly" 
		name="gambar" id="gambar" class="form-control">
		<button data-field="gambar" data-form="form-gambar" class="btn btn-secondary btn-lihat-gambar" type="button"><i class="la la-eye"></i> Lihat</button>
	</div>
</div>
{{ Form::bsHidden('uuid',$quiz->uuid) }}
{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}

<?php 
 
 $id_role_biro = 5;
 $list_biro = DB::select("select a.uuid as value, a.nama_pengguna as text  
							from users a , user_role as b 
								where a.id= b.id_user and b.id_role = $id_role_biro ");

 $list_lokasi = DB::select("select uuid as id , nama_lokasi as title,
								c.name as kabupaten , b.name as provinsi
								from lokasi as a, provinces as b, regencies as c 
								where a.kode_kabupaten = c.id and a.kode_provinsi= b.id 
								order by a.kode_kabupaten");
?>

<!-- MODAL FORM EDIT -->
{{ Form::bsOpen('form-edit',url($main_path."/update-info")) }}
	{{Html::mOpenLG('modal-edit','Ubah Data Tes')}}
	@if($isAdminSistem)
	{{ Form::bsSelect2('Biro','id_user_biro',$list_biro,'',true,'md-8')}}
	@endif
	{{ Form::bsReadOnly('Jenis Tes','jenis_tes','',true,'md-8') }}
	{{ Form::bsTextField('Nama Sesi','nama_sesi','',true,'md-8') }}
	{{ Form::bsTextField('Tanggal','tanggal','',false,'md-8') }}
	{{ Form::bsSelect2('Lokasi','id_lokasi',[],'',true,'md-8')}}
	{{ Form::bsTextField('Kota','kota','',true,'md-8') }}
	{{ Form::bsHidden('uuid',$quiz->uuid) }}
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}} 


{{ Form::bsOpen('form-asesor',url($main_path."/update-asesor")) }}
{{Html::mOpenLG('modal-asesor','Pengaturan Asesor')}}
{{ Form::bsTextField('Nama Asesor','nama_asesor',$quiz->nama_asesor,true,'md-8') }}
{{ Form::bsTextField('Nomor SIPP','nomor_sipp',$quiz->nomor_sipp,true,'md-8') }}
<div class="mb-3">
	<div class="input-group">
		<button id="btn_ttd_asesor" class="btn btn-primary btn-upload-ttd" data-field="ttd_asesor" 
			data-form="form-asesor" type="button"><i class="la la-upload"></i> Upload TTD</button>
		<input type="text"  name="ttd_asesor" value="{{$quiz->ttd_asesor}}" id="ttd_asesor" class="form-control">
		<button data-field="ttd_asesor" data-form="form-asesor" class="btn btn-secondary btn-lihat-gambar" type="button"><i class="la la-eye"></i> Lihat</button>
	</div>
	<small>Ukuran/Resolusi Gambar 250 x 150 piksel (Wajib) </small><br>
	<small><a href="https://createmysignature.com/">Untuk pembuatan gambar TTD dapat melalui website https://createmysignature.com/ </a></small>
</div>
{{ Form::bsHidden('uuid',$quiz->uuid) }}
{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}

{{ Form::bsOpen('form-status',url($main_path."/update-status")) }}
{{ Form::bsHidden('uuid',$quiz->uuid) }}
{{ Form::bsClose()}}

@if($isAdminSistem)
	{{ Form::bsOpen('form-hapus',url($main_path."/delete")) }}
	{{ Form::bsHidden('uuid',$quiz->uuid) }}
	{{ Form::bsClose()}}

	<?php 
		$list_kunci_waktu = json_decode(json_encode(array(
				["value"=>0, "text"=>"Fleksibel"],
				["value"=>1, "text"=>"Flat/Kaku"],
		)));
	?>
	{{ Form::bsOpen('form-waktu',url($main_path."/update-waktu")) }}
		{{Html::mOpenLG('modal-atur-waktu','Pengaturan Waktu Sesi Tes')}}
		{{ Form::bsReadOnly('Nama Sesi Tes','nama_sesi_tes','',true,'md-8') }}
		{{ Form::bsNumeric('Durasi','durasi','',true,'md-8') }}
		{{ Form::bsSelect2('Tipe Waktu','kunci_waktu',$list_kunci_waktu,'',true,'md-8')}}
		{{ Form::bsHidden('id_quiz_sesi',null) }}
		{{Html::mCloseSubmitLG('Simpan')}}
	{{ Form::bsClose()}} 

@endif


{{ Form::bsOpen('form-upload-peserta',url($main_path."/submit-upload-peserta")) }}
{{Html::mOpenLG('modal-upload-peserta','Upload Peserta Tes')}}
{{ Form::bsHidden('uuid',$quiz->uuid) }}
<div class="mb-3" id="panel-upload">
	<p>Format Excel Upload Peserta dapat didownload <a href="{{url('template/Format-Upload-Peserta.xlsx')}}">Disini</a></p>
	<div class="input-group">
		<button id="btn_upload_excel" class="btn btn-primary" type="button"><i class="la la-upload"></i> Upload Excel</button>
		<input type="text" required="required"  readonly="readonly" 
		name="token_upload_excel" id="token-upload-excel" class="form-control">
	</div>
</div>
<div class="mb-3 mt-4">
	<div class="card" style="100%">
		<div class="card-body">
			<div id="cek-tabel-upload"></div>
		</div>
	</div>
</div>
{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}


{{ Form::bsOpen('form-upload-excel',url($main_path."/upload-excel")) }}
{{ Form::bsHidden('uuid',$quiz->uuid) }}
<input type="file" style="display: none;" id="upload-excel" name="excel" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
{{ Form::bsClose()}}



{{Html::mOpenLG('modal-tambah-peserta','Tambah Peserta')}}
<p>Silahkan Pilih Peserta dari Akun yang sudah ada. <br>Klik Tombol  <i class="las la-plus"></i> Untuk Menambahkan Akun ke Daftar Peserta Tes</p>
<div class="mb-3 mt-4">
	<div class="card" style="100%">
		<div class="card-body" id="datatable-tambah-peserta">

		</div>
	</div>
</div>

{{Html::mCloseLG('')}}

{{Html::mOpenLG('modal-salin-peserta','Salin Peserta')}}
<p>Silahkan Salin Peserta dari Sesi Tes Lain. Klik Tombol  <b><i class="las la-copy"></i> Salin</b> Untuk Menyalin Data Peserta ke Sesi Tes Ini</p>
<div class="mb-3 mt-4">
	<div class="card" style="100%">
		<div class="card-body" id="datatable-salin-peserta">

		</div>
	</div>
</div>

{{Html::mCloseLG('')}}



{{ Form::bsOpen('form-tambah-peserta',url($main_path."/insert-peserta")) }}
{{ Form::bsHidden('uuid_quiz',$quiz->uuid) }}
{{ Form::bsHidden('uuid_user','') }}
{{ Form::bsClose()}}

{{ Form::bsOpen('form-salin-peserta',url($main_path."/salin-peserta")) }}
{{ Form::bsHidden('uuid_dst',$quiz->uuid) }}
{{ Form::bsHidden('uuid_src','') }}
{{ Form::bsClose()}}

{{ Form::bsOpen('form-kosongkan-peserta',url($main_path."/clear-peserta")) }}
{{ Form::bsHidden('uuid_quiz',$quiz->uuid) }}
{{ Form::bsClose()}}

{{ Form::bsOpen('form-delete-peserta',url($main_path."/delete-peserta")) }}
{{ Form::bsHidden('uuid','') }}
{{ Form::bsClose()}}

{{ Form::bsOpen('form-reset-peserta',url($main_path."/reset-peserta")) }}
{{ Form::bsHidden('uuid','') }}
{{ Form::bsClose()}}

{{ Form::bsOpen('form-publish-all',url($main_path."/publish-all-peserta")) }}
{{ Form::bsHidden('uuid',$quiz->uuid) }}
{{ Form::bsClose()}}

{{ Form::bsOpen('form-batal-publish-all',url($main_path."/batal-publish-all-peserta")) }}
{{ Form::bsHidden('uuid',$quiz->uuid) }}
{{ Form::bsClose()}}

{{ Form::bsOpen('form-batal-skoring-all',url($main_path."/batal-skoring-all-peserta")) }}
{{ Form::bsHidden('uuid',$quiz->uuid) }}
{{ Form::bsClose()}}



@endif


{{Html::mOpenLG('modal-view-result','Lihat Hasil Tes Peserta')}}
<div class="row">
	<div class="col-12">
		<div id="view-result" data-uuid=""></div>
	</div>
</div>
{{Html::mCloseLG('')}}


{{Html::mOpenLG('modal-input-rekom','Input Saran / Rekomendasi Hasil Peserta')}}
<div class="row">
	<div class="col-12">
		<div id="view-input-rekom" data-uuid=""></div>
	</div>
</div>
{{Html::mCloseLG('')}}

{{Html::mOpenLG('modal-download-all-report','Download Hasil Tes Semua Peserta')}}
<div class="row">
	<div class="col-12">
		<div id="link-info-download"></div>
	</div>
</div>
{{Html::mCloseLG('')}}

{{Html::mOpenLG('modal-download-all-report-doc','Download Hasil Tes Semua Peserta Format Doc')}}
<div class="row">
	<div class="col-12">
		<div id="link-info-download-doc"></div>
	</div>
</div>
{{Html::mCloseLG('')}}

{{ Form::bsOpen('form-upload-gambar',url($main_path."/upload-gambar")) }}
<input type="file" style="display: none;" id="upload-gambar" name="image" accept="image/*">
{{ Form::bsClose()}}

{{ Form::bsOpen('form-upload-ttd',url($main_path."/upload-ttd")) }}
<input type="file" style="display: none;" id="upload-ttd" name="image" accept="image/*">
{{ Form::bsClose()}}

@endsection

@section("js")
<script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script type="text/javascript">
	var $tabel_peserta = null;
	$(function() {

		@if(ucu())

		$field_gambar = '';
		$form_gambar = '';
		$field_gambar_lihat = '';
		$form_gambar_lihat = '';
		$base_url_image = '{{url("gambar")}}';

		$(".btn-upload-gambar").on('click', function(){
			$field_gambar = $(this).data('field');
			$form_gambar = $(this).data('form');
			$("#upload-gambar").trigger('click');
		});

		$(".btn-lihat-gambar").on('click', function(){
			$field_gambar_lihat = $(this).data('field');
			$form_gambar_lihat = $(this).data('form');
			$filename = $("#"+$form_gambar_lihat +" #"+$field_gambar_lihat).val();
			if($filename){
				$.alert({
					title: 'Gambar',
					columnClass: 'col-md-6',
					content: '<div width="100%"><center><img src="'+$base_url_image+'/'+$filename+'" class="img-fluid img-border rounded-lg" ></center></div>',
				});
			}
		});


		$("#upload-gambar").on('change', function(){
			if($(this).val()){
				$("#form-upload-gambar").submit();
			}
		});

		$('#form-upload-gambar').ajaxForm({
			beforeSubmit:function(){ disableButton("#"+$form_gambar +" #btn_"+$field_gambar) },
			success:function($respon){
				enableButton("#"+$form_gambar +" #btn_"+$field_gambar);
				if ($respon.status==true){
					$("#upload-gambar").val('');
					$("#"+$form_gambar +" #"+$field_gambar).val($respon.filename);
				}else{
					errorNotify($respon.message);
				}
			},
			error:function(){errorNotify('Terjadi Kesalahan!');}
		}); 


		$(".btn-upload-ttd").on('click', function(){
			$field_gambar = $(this).data('field');
			$form_gambar = $(this).data('form');
			$("#upload-ttd").trigger('click');
		});

		$("#upload-ttd").on('change', function(){
			if($(this).val()){
				$("#form-upload-ttd").submit();
			}
		});

		$('#form-upload-ttd').ajaxForm({
			beforeSubmit:function(){ disableButton("#"+$form_gambar +" #btn_"+$field_gambar) },
			success:function($respon){
				enableButton("#"+$form_gambar +" #btn_"+$field_gambar);
				if ($respon.status==true){
					$("#upload-ttd").val('');
					$("#"+$form_gambar +" #"+$field_gambar).val($respon.filename);
					successNotify('Gambar TTD Berhasil Diupload');
				}else{
					errorNotify($respon.message);
				}
			},
			error:function(){errorNotify('Terjadi Kesalahan!');}
		}); 
		@endif

		@if(ucu())

		$validator_form_gambar = $("#form-gambar").validate();
		$("#modal-gambar").on('show.bs.modal', function(e) {
			$validator_form_gambar.resetForm();
			$("#form-gambar").clearForm();
			enableButton("#form-gambar button[type=submit]")
		});
		$('#form-gambar').ajaxForm({
			beforeSubmit: function() {
				disableButton("#form-gambar button[type=submit]")
			},
			success: function($respon) {
				if ($respon.status == true) {
					$("#modal-gambar").modal('hide');
					successNotify($respon.message);
						//$tabel1.ajax.reload(null, true);
						location.reload();
					} else {
						errorNotify($respon.message);
					}
					enableButton("#form-gambar button[type=submit]")
				},
				error: function() {
					$("#form-gambar button[type=submit]").button('reset');
					$("#modal-gambar").modal('hide');
					errorNotify('Terjadi Kesalahan!');
				}
			});

		@if($isAdminSistem)
		$("#form-edit #id_user_biro").selectize();
		@endif
		$('#form-edit #id_lokasi').selectize({
		    valueField: 'id',
		    searchField: 'title',
		    options: {!! json_encode($list_lokasi) !!},
		    render: {
		        option: function(data, escape) {
		            return '<div class="option">' +
		                    '<span class="title">' + escape(data.title) + '</span><br>' +
		                    '<small>' + escape(data.kabupaten) + ', ' + escape(data.provinsi) + '</small>' +
		                '</div>';
		        },
		        item: function(data, escape) {
		            return '<div class="item">' + escape(data.title) + ', ' + escape(data.kabupaten) + '</div>';
		        }
		    },
		    create: false
		});

		$("#form-edit #tanggal").daterangepicker({
			singleDatePicker: true,
			showDropdowns: true,
			locale: {"format": "YYYY-MM-DD"},
			autoApply:true
		});

		$validator_form_edit = $("#form-edit").validate();
		$("#modal-edit").on('show.bs.modal', function(e) {
			$uuid = '{{$quiz->uuid}}';
			$validator_form_edit.resetForm();
			$("#form-edit").clearForm();
			@if($isAdminSistem)
			$('#form-edit #id_user_biro').selectize()[0].selectize.clear();
			@endif
			$('#form-edit #id_lokasi').selectize()[0].selectize.clear();
			disableButton("#form-edit button[type=submit]");
			$.get("{{url($main_path.'/get-data')}}/" + $uuid, function(respon) {
				if (respon.status) {
					$('#form-edit #nama_sesi').val(respon.data.nama_sesi);
					$('#form-edit #lokasi').val(respon.data.lokasi);
					$('#form-edit #kota').val(respon.data.kota);
					$('#form-edit #tanggal').val(respon.data.tanggal);
					$('#form-edit #jenis_tes').val(respon.data.template.nama_sesi);
					$('#form-edit #uuid').val(respon.data.uuid);
					@if($isAdminSistem)
					$('#form-edit #id_user_biro').selectize()[0].selectize.setValue(respon.data.id_user_biro,false);
					@endif
					$('#form-edit #id_lokasi').selectize()[0].selectize.setValue(respon.data.id_lokasi,false);
					enableButton("#form-edit button[type=submit]");
				} else {
					errorNotify(respon.message);
				}
			})
		});

		$('#form-edit').ajaxForm({
			beforeSubmit: function() {
				disableButton("#form-edit button[type=submit]")
			},
			success: function($respon) {
				if ($respon.status == true) {
					$("#modal-edit").modal('hide');
					successNotify($respon.message);
					location.reload();
						//$tabel1.ajax.reload(null, true);
					} else {
						errorNotify($respon.message);
					}
					enableButton("#form-edit button[type=submit]")
				},
				error: function() {
					$("#form-edit button[type=submit]").button('reset');
					$("#modal-edit").modal('hide');
					errorNotify('Terjadi Kesalahan!');
				}
			});

		$('#form-asesor').ajaxForm({
			beforeSubmit: function() {
				disableButton("#form-asesor button[type=submit]")
			},
			success: function($respon) {
				if ($respon.status == true) {
					$("#modal-asesor").modal('hide');
					successNotify($respon.message);
					location.reload();
						//$tabel1.ajax.reload(null, true);
					} else {
						errorNotify($respon.message);
					}
					enableButton("#form-asesor button[type=submit]")
				},
				error: function() {
					$("#form-asesor button[type=submit]").button('reset');
					$("#modal-asesor").modal('hide');
					errorNotify('Terjadi Kesalahan!');
				}
			});

		$('#form-status').ajaxForm({
			beforeSubmit: function() {},
			success: function($respon) {
				if ($respon.status == true) {
					successNotify($respon.message);
					location.reload();
						//$tabel1.ajax.reload(null, true);
					} else {
						errorNotify($respon.message);
					}
				},
				error: function() {
					errorNotify('Terjadi Kesalahan!');
				}
		});

		

			$('#btn-konfirm-status').on('click', function(e) {
			$uuid = '{{$quiz->uuid}}';
				//alert($uuid);
				$.get("{{url($main_path.'/get-data')}}/" + $uuid, function(respon) {
					if (respon.status) {
						$.confirm({
							title: "{{ $quiz->open? 'Tutup': 'Buka'}} Sesi Tes?",
							content: respon.informasi,
							buttons: {
								cancel: {
									text: 'Batalkan'
								},
								confirm: {
									text: 'Ya, Lanjutkan',
									btnClass: 'btn-success',
									action: function() {
										$("#form-status").submit();
									}
								},
							}
						});
					} else {
						errorNotify(respon.message);
					}
				})
			})

			@if($isAdminSistem)

			$('#form-hapus').ajaxForm({
			beforeSubmit: function() {},
			success: function($respon) {
					if ($respon.status == true) {
						successNotify($respon.message);
						window.location = "{{$back_url}}"
							//$tabel1.ajax.reload(null, true);
						} else {
							errorNotify($respon.message);
						}
					},
					error: function() {
						errorNotify('Terjadi Kesalahan!');
					}
			});

			$('#btn-konfirm-hapus').on('click', function(e) {
			$uuid = '{{$quiz->uuid}}';
				//alert($uuid);
				$.get("{{url($main_path.'/get-data')}}/" + $uuid, function(respon) {
					if (respon.status) {
						$.confirm({
							title: "Anda Yakin Ingin Hapus Tes Ini?",
							content: respon.informasi,
							buttons: {
								cancel: {
									text: 'Batalkan'
								},
								confirm: {
									text: 'Ya, Lanjutkan',
									btnClass: 'btn-danger',
									action: function() {
										$("#form-hapus").submit();
									}
								},
							}
						});
					} else {
						errorNotify(respon.message);
					}
				})
			})
			@endif



			//handle peserta


			$tabel_peserta =  
			$('#tabel-peserta').DataTable({
				processing: true,
				responsive: true,
				fixedHeader: true,
				serverSide: true,
				ajax: "{{url($main_path.'/dt-peserta/'.$quiz->uuid)}}",
				"iDisplayLength": 10,
				columns: [
				{data:'DT_Row_Index' , orderable:false, searchable: false,sClass:""},
				{data: 'user',name: "user",orderable: false,searchable: false,sClass: ""},
				{data: 'departement',name: "departement",orderable: false,searchable: false,sClass: ""},
				{data: 'start_at',name: "start_at",orderable: false,searchable: false,sClass: ""},
				{data: 'submit',name: "submit",orderable: false,searchable: false,sClass: ""},
				{data: 'skoring',name: "skoring",orderable: false,searchable: false,sClass: ""},
				{data: 'publish',name: "publish",orderable: false,searchable: false,sClass: ""},
				{data:'action' , orderable:false, searchable: false,sClass:"text-center"},
				],
				"fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
					$(nRow).addClass(aData["rowClass"]);
					return nRow;
				},
				"drawCallback": function(settings) {
					initTooltips();
					initViewUser();
					initViewResult();
					initInputRekom();
					initRemovePeserta();
					$(".result-jumlah-peserta").text('('+settings.json.jumlah_peserta+')');
					$("#rekap-submit").text('Submit: '+settings.json.rekap_status_peserta.submit);
					$("#rekap-skoring").text('Skoring: '+settings.json.rekap_status_peserta.skoring);
					$("#rekap-publish").text('Publish: '+settings.json.rekap_status_peserta.publish);
					$("#rekap-belum-publish").text('Belum Publish: '+ (settings.json.rekap_status_peserta.skoring - settings.json.rekap_status_peserta.publish ));


				},
				"language": {
					"emptyTable": "Belum Ada Peserta.."
				}
			});

			var	initViewResult = function (){
				$(".btn-view-result").on('click', function(){
					$uuid = $(this).data('uuid');
					$("#view-result").data('uuid', $uuid);
					$("#modal-view-result").modal('show');
				})
			}

			$("#modal-view-result").on('show.bs.modal', function(e) {
				$uuid = $("#view-result").data('uuid');
				$("#view-result").html(loadingElement);
				$.get("{{url($main_path.'/view-result')}}/"+$uuid, function(respon){
					$("#view-result").html(respon);
				})
			});

			$("#modal-view-result").on('hide.bs.modal', function(){
			     $("#view-result").html(loadingElement);
			 });

			var	initInputRekom = function (){
				$(".btn-comment-result").on('click', function(){
					$uuid = $(this).data('uuid');
					$("#view-input-rekom").data('uuid', $uuid);
					$("#modal-input-rekom").modal('show');
				})
			}

			$("#modal-input-rekom").on('show.bs.modal', function(e) {
				$uuid = $("#view-input-rekom").data('uuid');
				$("#view-input-rekom").html(loadingElement);
				$.get("{{url($main_path.'/input-rekom')}}/"+$uuid, function(respon){
					$("#view-input-rekom").html(respon);
				})
			});

			$("#modal-input-rekom").on('hide.bs.modal', function(){
			     $("#view-input-rekom").html(loadingElement);
			 });
			
			var initViewUser = function(){
				$(".btn-view-user").on('click', function(e){
					$uuid = $(this).data('id');
						//alert($uuid);
						//$("#form-delete-peserta #uuid").val($uuid);
						$.get("{{url($main_path.'/get-info-user')}}/" + $uuid, function(respon) {
							$.confirm({
								title: 'Informasi User',
								content:  respon,
								buttons: {
									cancel: {
										text: 'Tutup'
									},
								}
							});
						})
					})
			}
			var initRemovePeserta = function(){
				$(".btn-reset-sesi-peserta").on('click', function (e){
					$uuid = $(this).data('uuid');
					//alert($uuid);
					$("#form-reset-peserta #uuid").val($uuid);
					$.get("{{url($main_path.'/get-data-peserta')}}/" + $uuid, function(respon) {
						if (respon.status) {
							$.confirm({
								title: 'Konfirmasi Reset',
								content:  respon.informasi + '<br>Anda Yakin Ingin Reset Sesi Tes Peserta Ini?. Seteleh direset peserta dapat mengikuti ulang sesi tes.',
								buttons: {
									cancel: {
										text: 'Batalkan'
									},
									confirm: {
										text: 'Ya, Lanjutkan',
										btnClass: 'btn-warning',
										action: function() {
											$("#form-reset-peserta").submit()
										}
									},
								}
							});
						} else {
							errorNotify(respon.message);
						}
					})

				});


				$(".btn-remove-peserta").on('click', function(e){
					$uuid = $(this).data('uuid');
					//alert($uuid);
					$("#form-delete-peserta #uuid").val($uuid);
					$.get("{{url($main_path.'/get-data-peserta')}}/" + $uuid, function(respon) {
						if (respon.status) {
							$.confirm({
								title: 'Konfirmasi Hapus',
								content:  respon.informasi + '<br>Anda Yakin Ingin Hapus dari Daftar Peserta',
								buttons: {
									cancel: {
										text: 'Batalkan'
									},
									confirm: {
										text: 'Hapus',
										btnClass: 'btn-danger',
										action: function() {
											$("#form-delete-peserta").submit()
										}
									},
								}
							});
						} else {
							errorNotify(respon.message);
						}
					})
				});
			}

			$('#form-delete-peserta').ajaxForm({
				beforeSubmit:function(){},
				success:function($respon){
					if ($respon.status==true){
						  //$("#modal-upload-peserta").modal('hide');
						  successNotify($respon.message);
						  $tabel_peserta.ajax.reload(null, false);
						}else{
							errorNotify($respon.message);
						}
					},
				error:function(){errorNotify('Terjadi Kesalahan!');}
			}); 


			$('#form-reset-peserta').ajaxForm({
				beforeSubmit:function(){},
				success:function($respon){
					if ($respon.status==true){
						  //$("#modal-upload-peserta").modal('hide');
						  successNotify($respon.message);
						  $tabel_peserta.ajax.reload(null, false);
						}else{
							errorNotify($respon.message);
						}
					},
				error:function(){errorNotify('Terjadi Kesalahan!');}
			}); 

			//HANDLE UPLOAD PESERTA

			$("#modal-upload-peserta").on('show.bs.modal', function(e) {
				//$validator_form_gambar.resetForm();
				$("#form-upload-peserta").clearForm();
				$("#cek-tabel-upload").html(null);
				enableButton("#btn_upload_excel");
				disableButton("#form-upload-peserta button[type=submit]")
			});

			$("#btn_upload_excel").on('click', function(){
				$("#upload-excel").trigger('click');
			});

			$("#upload-excel").on('change', function(){
				if($(this).val()){
					$("#form-upload-excel").submit();
				}
			});

			
			var loadingElement = '<center style="margin-top:30px;"><div class="spinner-border text-secondary" role="status"></div><p style="margin-top:10px;">Mohon Tunggu, Data Sedang Diproses...</p></center>';
			$('#form-upload-excel').ajaxForm({
				beforeSubmit:function(){disableButton("#btn_upload_excel");},
				success:function($respon){
					enableButton("#btn_upload_excel");
					if ($respon.status==true){
						$("#upload-excel").val('');
						$("#token-upload-excel").val($respon.token);
						$("#cek-tabel-upload").html(loadingElement);
						generateReaderExcelDatatable($respon.token, $respon.filename);
					}else{
						errorNotify($respon.message);
					}
				},
				error:function(){errorNotify('Terjadi Kesalahan!'); enableButton("#btn_upload_excel");}
			});

			var generateReaderExcelDatatable= function ($token, $filename){
				$.get("{{url($main_path.'/generate-import-excel')}}/{{$quiz->uuid}}/" + $token + '/'+$filename, function(respon) {
					$("#cek-tabel-upload").html(respon);
					enableButton("#form-upload-peserta button[type=submit]")
				});
			}

			$('#form-upload-peserta').ajaxForm({
				beforeSubmit:function(){
					disableButton("#form-upload-peserta button[type=submit]"); 
					disableButton("#btn_upload_excel");
					$("#cek-tabel-upload").html(loadingElement); },
					success:function($respon){
						if ($respon.status==true){
							$("#modal-upload-peserta").modal('hide');
							successNotify($respon.message);
							$tabel_peserta.ajax.reload(null, true);
						}else{
							errorNotify($respon.message);
						}
					},
					error:function(){errorNotify('Terjadi Kesalahan!');}
				}); 

			//handle tambah pserta
			var $tabel_tambah_peserta = null;

			$("#modal-tambah-peserta").on('show.bs.modal', function(e) {
				$("#datatable-tambah-peserta").html(loadingElement);
				$.get("{{url($main_path.'/generate-tabel-tambah-peserta')}}", function(respon){
					$("#datatable-tambah-peserta").html(respon);
					$tabel_tambah_peserta =  
					$('#tabel-tambah-peserta').DataTable({
						processing: true,
						responsive: true,
						fixedHeader: true,
						serverSide: true,
						ajax: "{{url($main_path.'/dt-tambah-peserta/'.$quiz->uuid)}}",
						"iDisplayLength": 10,
						columns: [
						{data:'DT_Row_Index' , orderable:false, searchable: false,sClass:""},
						{data: 'user',name: "user",orderable: false,searchable: false,sClass: ""},
						{data: 'departement',name: "departement",orderable: false,searchable: false,sClass: ""},
						{data:'action' , orderable:false, searchable: false,sClass:""},
						],
						"fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
							$(nRow).addClass(aData["rowClass"]);
							return nRow;
						},
						"drawCallback": function(settings) {
							initHandleAddPeserta();
							initViewUser();
						},
						"language": {
							"emptyTable": "Data Tidak Tersedia.."
						}
					});
				});
			});

			var initHandleAddPeserta = function(){
				$('.btn-add-peserta').on('click', function(e) {
					$uuid = $(this).data('uuid');
					$("#form-tambah-peserta #uuid_user").val($uuid);
					$("#form-tambah-peserta").submit()
				 	//
				 })
			}
			
			$('#form-tambah-peserta').ajaxForm({
				beforeSubmit: function() {},
				success: function($respon) {
					if ($respon.status == true) {
						successNotify($respon.message);
						$tabel_tambah_peserta.ajax.reload(null, false);
						$tabel_peserta.ajax.reload(null, true);
					} else {
						errorNotify($respon.message);
					}
				},
				error: function() {
					errorNotify('Terjadi Kesalahan!');
				}
			});



			//SALIN PESERTA

			//handle tambah pserta
			var $tabel_salin_peserta = null;

			$("#modal-salin-peserta").on('show.bs.modal', function(e) {
				$("#datatable-salin-peserta").html(loadingElement);
				$.get("{{url($main_path.'/generate-tabel-salin-peserta')}}", function(respon){
					$("#datatable-salin-peserta").html(respon);
					$tabel_salin_peserta =  
					$('#tabel-salin-peserta').DataTable({
						processing: true,
						responsive: true,
						fixedHeader: true,
						serverSide: true,
						ajax: "{{url($main_path.'/dt-salin-peserta/'.$quiz->uuid)}}",
						"iDisplayLength": 10,
						columns: [
						{data:'DT_Row_Index' , orderable:false, searchable: false,sClass:""},
						{data: 'nama_sesi',name: "nama_sesi",orderable: false,searchable: false,sClass: ""},
						{data: 'tanggal',name: "tanggal",orderable: false,searchable: false,sClass: ""},
						{data: 'nama_lokasi',name: "nama_lokasi",orderable: false,searchable: false,sClass: ""},
						{data: 'peserta',name: "peserta",orderable: false,searchable: false,sClass: ""},
						{data: 'jenis_tes',name: "jenis_tes",orderable: false,searchable: false,sClass: ""},
						{data:'action' , orderable:false, searchable: false,sClass:""},
						],
						"fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
							$(nRow).addClass(aData["rowClass"]);
							return nRow;
						},
						"drawCallback": function(settings) {
							initHandleSalinPeserta();
						},
						"language": {
							"emptyTable": "Data Tidak Tersedia.."
						}
					});
				});
			});

			var initHandleSalinPeserta = function(){
				$('.btn-salin-peserta-tes').on('click', function(e) {
					$uuid = $(this).data('uuid');
					$peserta_jml = $(this).data('peserta');
					$tes_nama = $(this).data('tes');
					//alert($uuid);
					$("#form-salin-peserta #uuid_src").val($uuid);
					$.confirm({
					title: 'Konfirmasi Salin Peserta',
						content: 'Anda Yakin Ingin Salin ' + $peserta_jml +' Peserta dari ' +  $tes_nama+' ke Sesi Ini?',
						buttons: {
							cancel: {
								text: 'Batalkan'
							},
							confirm: {
								text: 'Ya, Lanjutkan',
								btnClass: 'btn-primary',
								action: function() {
									$("#form-salin-peserta").submit()
								}
							},
						}
					});
				 	//
				 });
			}


			$('#form-salin-peserta').ajaxForm({
				beforeSubmit: function() {},
				success: function($respon) {
					if ($respon.status == true) {
						successNotify($respon.message);
						$("#modal-salin-peserta").modal('hide');
						$tabel_peserta.ajax.reload(null, true);
					} else {
						errorNotify($respon.message);
					}
				},
				error: function() {
					errorNotify('Terjadi Kesalahan!');
				}
			});

			$("#btn-kosongkan-peserta").on('click', function(e){
				$.confirm({
					title: 'Konfirmasi',
					content: 'Anda Yakin Ingin Kosongkan Daftar Peserta Tes?',
					buttons: {
						cancel: {
							text: 'Batalkan'
						},
						confirm: {
							text: 'Ya, Lanjutkan',
							btnClass: 'btn-danger',
							action: function() {
								$("#form-kosongkan-peserta").submit()
							}
						},
					}
				});
			})

			$('#form-kosongkan-peserta').ajaxForm({
				beforeSubmit: function() {},
				success: function($respon) {
					if ($respon.status == true) {
						successNotify($respon.message);
						$tabel_peserta.ajax.reload(null, true);
					} else {
						errorNotify($respon.message);
					}
				},
				error: function() {
					errorNotify('Terjadi Kesalahan!');
				}
			});
			

			@endif

			@if(ucu())
					@if($isAdminSistem)
						$(".btn-atur-waktu").on('click', function(e){
							$uuid = $(this).data('uuid');
							$nama = $(this).data('nama');
							$durasi = $(this).data('durasi');
							$kunci = $(this).data('kunci');
							$("#form-waktu").clearForm();
							$('#form-waktu #kunci_waktu').selectize()[0].selectize.clear();
							$("#form-waktu #nama_sesi_tes").val($nama);
							$("#form-waktu #durasi").val($durasi);
							$("#form-waktu #id_quiz_sesi").val($uuid);
							$('#form-waktu #kunci_waktu').selectize()[0].selectize.setValue($kunci,false);
							$("#modal-atur-waktu").modal('show');
						});

						$('#form-waktu').ajaxForm({
							beforeSubmit: function() {
								disableButton("#form-waktu button[type=submit]")
							},
							success: function($respon) {
								if ($respon.status == true) {
									$("#modal-atur-waktu").modal('hide');
									successNotify($respon.message);
									//$tabel1.ajax.reload(null, true);
									location.reload();
								} else {
									errorNotify($respon.message);
								}
								enableButton("#form-waktu button[type=submit]")
							},
							error: function() {
								$("#form-waktu button[type=submit]").button('reset');
								$("#modal-atur-waktu").modal('hide');
								errorNotify('Terjadi Kesalahan!');
							}
						});
					@endif

			$("#btn-publish-all").on('click', function(){
				$.confirm({
					title: 'Konfirmasi',
					content: 'Anda Yakin Ingin Publish Hasil Tes 100 Peserta?',
					buttons: {
						cancel: {
							text: 'Batalkan'
						},
						confirm: {
							text: 'Ya, Lanjutkan',
							btnClass: 'btn-primary',
							action: function() {
								$("#form-publish-all").submit();
							}
						},
					}
				});
			});

			$("#btn-batal-publish-all").on('click', function(){
				$.confirm({
					title: 'Konfirmasi',
					content: 'Anda Yakin Ingin Batalkan Publish Hasil Tes Semua Peserta?',
					buttons: {
						cancel: {
							text: 'Batalkan'
						},
						confirm: {
							text: 'Ya, Lanjutkan',
							btnClass: 'btn-warning',
							action: function() {
								$("#form-batal-publish-all").submit();
							}
						},
					}
				});
			});

			$("#btn-batal-skoring-all").on('click', function(){
				$.confirm({
					title: 'Konfirmasi',
					content: 'Anda Yakin Ingin Batalkan Skoring Tes Semua Peserta?',
					buttons: {
						cancel: {
							text: 'Batalkan'
						},
						confirm: {
							text: 'Ya, Lanjutkan',
							btnClass: 'btn-warning',
							action: function() {
								$("#form-batal-skoring-all").submit();
							}
						},
					}
				});
			});

			$('#form-publish-all').ajaxForm({
				beforeSubmit: function() {},
				success: function($respon) {
					if ($respon.status == true) {
						successNotify($respon.message);
						$tabel_peserta.ajax.reload(null, true);
					} else {
						errorNotify($respon.message);
					}
				},
				error: function() {
					errorNotify('Terjadi Kesalahan!');
				}
			});

			$('#form-batal-publish-all').ajaxForm({
				beforeSubmit: function() {
					 $.LoadingOverlay("show",{size:5});
				},
				success: function($respon) {
					$.LoadingOverlay("hide");
					if ($respon.status == true) {
						successNotify($respon.message);
						$tabel_peserta.ajax.reload(null, true);
						//loadingAjaxStop();
					} else {
						errorNotify($respon.message);
					}
				},
				error: function() {
					$.LoadingOverlay("hide");
					errorNotify('Terjadi Kesalahan!');
					//loadingAjaxStop();
				}
			});

			$('#form-batal-skoring-all').ajaxForm({
				beforeSubmit: function() {
					 $.LoadingOverlay("show",{size:5});
				},
				success: function($respon) {
					$.LoadingOverlay("hide");
					if ($respon.status == true) {
						successNotify($respon.message);
						$tabel_peserta.ajax.reload(null, true);
						//loadingAjaxStop();
					} else {
						errorNotify($respon.message);
					}
				},
				error: function() {
					$.LoadingOverlay("hide");
					errorNotify('Terjadi Kesalahan!');
					//loadingAjaxStop();
				}
			});
			@endif


			@if($quiz->skoring_tabel=='skoring_minat_smk' || $quiz->skoring_tabel=='skoring_minat_smk_v2')
				$.get('{{url($main_path."/page-mapping-smk/".$quiz->uuid)}}', function(respon){
					$("#addons-pilihan-smk").html(respon);
				});
			@endif

			$("#modal-download-all-report").on('show.bs.modal', function(e) {
				$("#link-info-download").html('<div class="alert alert-primary mb-5" role="alert"><div class="alert-message"><p><i class="fas fa-spinner fa-pulse"></i> Mohon Tunggu Sedang Menyiapkan File Report Hasil Tes Semua Peserta...</p></div></div>');
				$.get("{{url('generate-zip/'.Crypt::encrypt($quiz->id_quiz))}}", function(respon){
					if(respon.status==true){
						$("#link-info-download").html('<div class="alert alert-primary mb-5" role="alert"><div class="alert-message"><p><i class="las la-check"></i> Silahkan Download Semua Hasil Tes Peserta Pada Link Dibawah ini.</p>'+ '<a class="btn btn-primary" href="'+respon.url+'"><i class="la la-download"></i> Download</a></div></div>');
					}
				})
			});


			$("#modal-download-all-report-doc").on('show.bs.modal', function(e) {
				$("#link-info-download-doc").html('<div class="alert alert-primary mb-5" role="alert"><div class="alert-message"><p><i class="fas fa-spinner fa-pulse"></i> Mohon Tunggu Sedang Menyiapkan File Document Report Hasil Tes Peserta...</p></div></div>');
				$.get("{{url('generate-zip-doc/'.Crypt::encrypt($quiz->id_quiz))}}", function(respon){
					if(respon.status==true){
						$("#link-info-download-doc").html('<div class="alert alert-primary mb-5" role="alert"><div class="alert-message"><p><i class="las la-check"></i> Silahkan Download Semua Hasil Tes Peserta Pada Link Dibawah ini.</p>'+ '<a class="btn btn-primary" href="'+respon.url+'"><i class="la la-download"></i> Download</a></div></div>');
					}else{
						errorNotify(respon.message);
						$("#link-info-download-doc").html('<div class="alert alert-primary mb-5" role="alert"><div class="alert-message">'+respon.message+'</div></div>');
					}
				})
			});

			$("#btn-refresh-data").on('click', function(){
				$tabel_peserta.ajax.reload(null, true);
			})
		})
	</script>
	@endsection