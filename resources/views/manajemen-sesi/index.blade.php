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

$list_tahun = DB::select("SELECT
	date_part( 'year', A.tanggal ) AS value,
	date_part( 'year', A.tanggal ) AS text 
		FROM
			quiz_sesi AS A 
		GROUP BY
			( date_part( 'year', A.tanggal ) )");

$list_status_open = json_decode(json_encode(array(
							["value"=>0, "text"=>"Tutup"],
							["value"=>1, "text"=>"Buka"],
					)));
$list_jenis_tes = DB::select("SELECT 
							a.id_quiz_template as value, a.nama_sesi as text 
							FROM quiz_sesi_template as a ");
if($isAdminSistem){
	$list_biro = DB::select("select a.uuid as value, a.nama_pengguna as text  
							from users a , user_role as b 
								where a.id= b.id_user and b.id_role = $id_role_biro ");
}

// dd($list_mode);
?>
@extends('layout')
@section("css")
<link rel="stylesheet" href="//cdn.quilljs.com/1.3.6/quill.snow.css" />
@endsection
@section("pagetitle")

@endsection

@section('content')

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h5 class="card-title">Manajemen Data Tes</h5>
				<h6 class="card-subtitle text-muted">Fitur Ini Digunakan Untuk Manajemen Data Sesi Tes Si-Cerdas </h6>
			</div>
			<div class="card-body">
				@if($isAdminSistem)
					<div class="row">
						<div class="col-3">
							{{ Form::bsSelect2('Jenis Tes','cari_jenis',$list_jenis_tes,'',false,'md-8')}}
						</div>
						<div class="col-3">
							{{ Form::bsSelect2('Biro','cari_biro',$list_biro,'',false,'md-8')}}
						</div>
						<div class="col-3">
							{{ Form::bsSelect2('Tahun','cari_tahun',$list_tahun,'',false,'md-8')}}
						</div>
						<div class="col-3">
							 <button id="btn-cari-data" class="btn btn-secondary" style="margin-top: 1.9rem !important;"><i class="la la-search-plus"></i> Cari Data</button>&nbsp;
							 <button id="btn-reset-cari" class="btn btn-light" style="margin-top: 1.9rem !important;"><i class="la la-refresh"></i> Reset</button>
						</div>
					</div>
					<hr>
				@endif
				 
				@if(ucc())
				{{Html::btnModal('<i class="la la-plus-circle"></i> Tambah Sesi Tes','modal-tambah','primary')}}
				<hr>
				@endif
				<table id="datatable" class="table table-striped table-hover table-sm" style="width:100%">
					<thead>
						<tr>							
							<th width="5%">ID</th>
							<th width="20%">Nama Tes/Sesi</th>
							<th width="15%">Biro</th>
							<th width="15%">Kota</th>
							<th width="15%">Lokasi</th>
							<th width="15%">Tanggal</th>
							<th width="5%">Peserta</th>
							<th width="10%">Actions</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection

@section("modal")


@if(ucc())
<!-- MODAL FORM TAMBAH -->
{{ Form::bsOpen('form-tambah',url($main_path."/insert")) }}
	{{Html::mOpenLG('modal-tambah','Tambah Sesi Tes')}}
	@if($isAdminSistem)
		{{ Form::bsSelect2('Biro','id_user_biro',$list_biro,'',true,'md-8')}}
	@else
		{{ Form::bsHidden('id_user_biro',Auth::user()->uuid) }}
	@endif
		{{ Form::bsSelect2('Jenis Tes','id_quiz_template',$list_jenis_tes,'',true,'md-8')}}
		{{ Form::bsTextField('Nama Sesi','nama_sesi','',true,'md-8') }}
		{{ Form::bsTextField('Tanggal','tanggal','',false,'md-8') }}
		{{ Form::bsTextField('Lokasi','lokasi','',true,'md-8') }}
		{{ Form::bsTextField('Kota','kota','',true,'md-8') }}
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif


@if(ucu())
<!-- MODAL FORM EDIT -->
{{ Form::bsOpen('form-edit',url($main_path."/update")) }}
	{{Html::mOpenLG('modal-edit','Edit Sesi Tes')}}
		{{ Form::bsReadOnly('Jenis Tes','jenis_tes','',true,'md-8') }}
		{{ Form::bsTextField('Nama Sesi','nama_sesi','',true,'md-8') }}
		{{ Form::bsTextField('Tanggal','tanggal','',false,'md-8') }}
		{{ Form::bsTextField('Lokasi','lokasi','',true,'md-8') }}
		{{ Form::bsTextField('Kota','kota','',true,'md-8') }}
		{{ Form::bsHidden('uuid','') }}
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif


@if(ucd())
<!-- FORM DELETE -->
{{ Form::bsOpen('form-delete',url($main_path."/delete")) }}
	{{ Form::bsHidden('uuid','') }}
{{ Form::bsClose()}}
@endif


@endsection

@section("js")
<script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script type="text/javascript">
	$(function() {

		//var toolbarOptions = [['bold', 'italic'],['link', 'image']];

		var initTooltips = function (){
			var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-tip="tooltip"]'))
			var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
			  return new bootstrap.Tooltip(tooltipTriggerEl)
			})
		}
			initTooltips();
			

		var $tabel1 = $('#datatable').DataTable({
			processing: true,
			responsive: true,
			fixedHeader: true,
			serverSide: true,
			ajax: "{{url($main_path.'/dt')}}",
			"iDisplayLength": 25,
			columns: [
				{data: 'token',name: "token",orderable: false,searchable: false,sClass: "text-center"},
				{data: 'nama_sesi',name: "nama_sesi",orderable: false,searchable: false,sClass: ""},
				{data: 'nama_biro',name: "nama_biro",orderable: false,searchable: false,sClass: ""},
				{data: 'kota',name: "kota",orderable: false,searchable: false,sClass: "text-center"},
				{data: 'lokasi',name: "lokasi",orderable: false,searchable: false,sClass: "text-center"},
				{data: 'tanggal',name: "tanggal",orderable: false,searchable: false,sClass: "text-center"},
				{data: 'jumlah_peserta',name: "jumlah_peserta",orderable: false,searchable: false,sClass: "text-center"},
				{data: 'action',orderable: false,searchable: false,sClass: "text-center"},
			],
				"fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
					$(nRow).addClass(aData["rowClass"]);
					return nRow;
			},
			"drawCallback": function(settings) {
				initTooltips();
				@if(ucd())
					initKonfirmDelete();
				@endif
			}
		});

		$tabel1.on('responsive-display', function(e, datatable, columns) {
			initKonfirmDelete();;
		});

		
		@if($isAdminSistem)

			$("#btn-cari-data").on('click', function(){
				$cari_jenis = $("#cari_jenis").val();
				$cari_biro = $("#cari_biro").val();
				$cari_tahun = $("#cari_tahun").val();
				$search="?cari=1";
				$cari = 0;
				if($cari_jenis){
					$cari = 1;
					$search += "&jenis=" + $cari_jenis;
				}
				if($cari_biro){
					$cari = 1;
					$search += "&biro=" + $cari_biro;
				}
				if($cari_tahun){
					$cari = 1;
					$search += "&tahun=" + $cari_tahun;
				}
				if($cari==1){
					$tabel1.ajax.url("{{url($main_path.'/dt')}}" + $search  ).load();
				}else{
					$tabel1.ajax.url("{{url($main_path.'/dt')}}").load();
				}
			});

			$("#btn-reset-cari").on('click', function(){
				 $("#cari_jenis").selectize()[0].selectize.setValue('',false);
				 $("#cari_biro").selectize()[0].selectize.setValue('',false);
				 $("#cari_tahun").selectize()[0].selectize.setValue('',false);
				 $tabel1.ajax.url("{{url($main_path.'/dt')}}").load();
			});



		@endif
		@if(ucc())

		// var quill_petunjuk_tambah = new Quill('#petunjuk_tambah', {
		// 	placeholder: 'Tulis Isi Petunjuk.....',
		// 	theme: 'snow',
		// 	modules: {
		// 		toolbar: toolbarOptions
		// 	}
		// });
		// quill_petunjuk_tambah.on('text-change', function(delta, oldDelta, source) {
		// 	$("#form-tambah #petunjuk_sesi").val($("#petunjuk_tambah .ql-editor").html());
		// });
		$("#form-tambah #tanggal").daterangepicker({
			singleDatePicker: true,
			showDropdowns: true,
			locale: {"format": "YYYY-MM-DD"},
			autoApply:true
		});

		$validator_form_tambah = $("#form-tambah").validate();
		$("#modal-tambah").on('show.bs.modal', function(e) {
			$validator_form_tambah.resetForm();
			$("#form-tambah").clearForm();
			//$("#petunjuk_tambah .ql-editor").html('');
			$("#form-tambah #petunjuk_sesi").val('');
			//$('#form-tambah #mode').selectize()[0].selectize.clear();
			$('#form-tambah #id_quiz_template').selectize()[0].selectize.clear();
			@if($isAdminSistem)
			$('#form-tambah #id_user_biro').selectize()[0].selectize.clear();
			@endif
			//$('#form-tambah #open').selectize()[0].selectize.clear();
			enableButton("#form-tambah button[type=submit]")
		});

		$('#form-tambah').ajaxForm({
			beforeSubmit: function() {
				disableButton("#form-tambah button[type=submit]")
			},
			success: function($respon) {
				if ($respon.status == true) {
					$("#modal-tambah").modal('hide');
					successNotify($respon.message);
					$tabel1.ajax.reload(null, true);
				} else {
					errorNotify($respon.message);
				}
				enableButton("#form-tambah button[type=submit]")
			},
			error: function() {
				$("#form-tambah button[type=submit]").button('reset');
				$("#modal-tambah").modal('hide');
				errorNotify('Terjadi Kesalahan!');
			}
		});
		@endif


		@if(ucu())
 
		$("#form-edit #tanggal").daterangepicker({
			singleDatePicker: true,
			showDropdowns: true,
			locale: {"format": "YYYY-MM-DD"},
			autoApply:true
		});

		$validator_form_edit = $("#form-edit").validate();
		$("#modal-edit").on('show.bs.modal', function(e) {
			$uuid = $(e.relatedTarget).data('uuid');
			$validator_form_edit.resetForm();
			$("#form-edit").clearForm();
			disableButton("#form-edit button[type=submit]");
			//$('#form-edit #id_quiz_template').selectize()[0].selectize.clear();
			//$('#form-edit #open').selectize()[0].selectize.clear();
			$.get("{{url($main_path.'/get-data')}}/" + $uuid, function(respon) {
				if (respon.status) {
					//$('#form-edit #id_quiz_template').selectize()[0].selectize.setValue(respon.data.id_quiz_template,false);
					//$('#form-edit #open').selectize()[0].selectize.setValue(respon.data.open,false);
					$('#form-edit #nama_sesi').val(respon.data.nama_sesi);
					$('#form-edit #lokasi').val(respon.data.lokasi);
					$('#form-edit #kota').val(respon.data.kota);
					$('#form-edit #tanggal').val(respon.data.tanggal);
					$('#form-edit #jenis_tes').val(respon.data.template.nama_sesi);
					$('#form-edit #uuid').val(respon.data.uuid);
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
					$tabel1.ajax.reload(null, true);
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
		@endif
		

		@if(ucd())
		$('#form-delete').ajaxForm({
			beforeSubmit: function() {},
			success: function($respon) {
				if ($respon.status == true) {
					successNotify($respon.message);
					$tabel1.ajax.reload(null, true);
				} else {
					errorNotify($respon.message);
				}
			},
			error: function() {
				errorNotify('Terjadi Kesalahan!');
			}
		});
		var initKonfirmDelete = function() {
			$('.btn-konfirm-delete').on('click', function(e) {
				$uuid = $(this).data('uuid');
				//alert($uuid);
				$.get("{{url($main_path.'/get-data')}}/" + $uuid, function(respon) {
					if (respon.status) {
						$("#form-delete #uuid").val(respon.data.uuid);
						$.confirm({
							title: 'Yakin Hapus Data?',
							content: respon.informasi,
							buttons: {
								cancel: {
									text: 'Batalkan'
								},
								confirm: {
									text: 'Hapus',
									btnClass: 'btn-danger',
									action: function() {
										$("#form-delete").submit()
									}
								},
							}
						});
					} else {
						errorNotify(respon.message);
					}
				})
			})
		}
		@endif
	})
</script>
@endsection