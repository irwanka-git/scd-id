<?php
// tabel: quiz_sesi_template
// quiz_sesi_template
// id_quiz_template int
// nama_sesi        varchar
// skoring_tabel    varchar
// uuid             char

$main_path = Request::segment(1);
loadHelper('akses,function');
$list_mode = get_list_enum_values('quiz_sesi_master', 'mode');
$list_jenis = get_list_enum_values('quiz_sesi','jenis');
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
				<h5 class="card-title">Tarif Tes</h5>
				<h6 class="card-subtitle text-muted">Fitur Ini Digunakan Untuk Manajemen Tarif Tes </h6>
			</div>
			<div class="card-body">
				@if(ucc())
				{{Html::btnModal('<i class="la la-plus-circle"></i> Tambah Tarif (Paket)','modal-tambah','primary')}}
				<hr>
				@endif
				<table id="datatable" class="table table-striped table-hover table-sm" style="width:100%">
					<thead>
						<tr>							
							<th width="5%">ID</th>
							<th width="45%">Nama Paket</th>
							<th width="20%">Tarif</th>
							<th width="15%">Kode</th>
							@if(ucu() || ucd())
							<th width="10%">Actions</th>
							@endif
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
	{{Html::mOpenLG('modal-tambah','Tambah Tarif Paket Tes')}}
		{{ Form::bsTextField('Nama Tarif / Paket','nama_tarif','',true,'md-8') }} 
		{{ Form::bsNumeric('Tarif (Rp)','tarif','',true,'md-8') }} 
		{{ Form::bsTextField('Kode (3 Digit)','kode','',true,'md-8') }} 
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif


@if(ucu())
<!-- MODAL FORM EDIT -->
{{ Form::bsOpen('form-edit',url($main_path."/update")) }}
	{{Html::mOpenLG('modal-edit','Edit Tarif Paket Tes')}}
		{{ Form::bsTextField('Nama Tarif / Paket','nama_tarif','',true,'md-8') }} 
		{{ Form::bsNumeric('Tarif (Rp)','tarif','',true,'md-8') }} 
		{{ Form::bsTextField('Kode (3 Digit)','kode','',true,'md-8') }} 
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
 
<script type="text/javascript">
	$(function() {
		$(".select2").selectize();
		//var toolbarOptions = [['bold', 'italic'],['link', 'image']];
		var $tabel1 = $('#datatable').DataTable({
			processing: true,
			responsive: true,
			fixedHeader: true,
			serverSide: true,
			ajax: "{{url($main_path.'/dt')}}",
			"iDisplayLength": 25,
			columns: [
				{data: 'id_tarif',name: "id_tarif",orderable: false,searchable: false,sClass: "text-center"},
				{data: 'nama_tarif',name: "nama_tarif",orderable: false,searchable: false,sClass: ""},
				{data: 'tarif',name: "tarif",orderable: false,searchable: false,sClass: ""},
				{data: 'kode',name: "kode",orderable: false,searchable: false,sClass: "text-center"},
				@if(ucu() || ucd())
				{data: 'action',orderable: false,searchable: false,sClass: "text-center"},
				@endif
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
				initViewGambar();
			}
		});

		$tabel1.on('responsive-display', function(e, datatable, columns) {
			initKonfirmDelete();;
		});

		 

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

		$validator_form_tambah = $("#form-tambah").validate();
		$("#modal-tambah").on('show.bs.modal', function(e) {
			$validator_form_tambah.resetForm();
			$("#form-tambah").clearForm(); 
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

		// var quill_petunjuk_edit = new Quill('#petunjuk_edit', {
		// 	placeholder: 'Tulis Isi Petunjuk Sesi.....',
		// 	theme: 'snow',
		// 	modules: {
		// 		toolbar: toolbarOptions
		// 	}
		// });
		// quill_petunjuk_edit.on('text-change', function(delta, oldDelta, source) {
		// 	$("#form-edit #petunjuk_sesi").val($("#petunjuk_edit .ql-editor").html());
		// });

		$validator_form_edit = $("#form-edit").validate();
		$("#modal-edit").on('show.bs.modal', function(e) {
			$uuid = $(e.relatedTarget).data('uuid');
			$validator_form_edit.resetForm();
			$("#form-edit").clearForm();
			disableButton("#form-edit button[type=submit]"); 
			$.get("{{url($main_path.'/get-data')}}/" + $uuid, function(respon) {
				if (respon.status) {
					
					$('#form-edit #nama_tarif').val(respon.data.nama_tarif);
					$('#form-edit #tarif').val(respon.data.tarif);
					$('#form-edit #kode').val(respon.data.kode);
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

		var initViewGambar = function(){

			$('.btn-view-image').on('click', function(e) {
				$filename = $(this).data('image');
				$base_url_image = '{{url("gambar")}}';
				if($filename){
		 			$.alert({
					    title: 'Gambar',
					    columnClass: 'col-md-6',
					    content: '<div width="100%"><center><img src="'+$base_url_image+'/'+$filename+'" class="img-fluid rounded-lg" ></center></div>',
					});
			 	}
			})
		}
	})
</script>
@endsection