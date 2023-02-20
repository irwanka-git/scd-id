<?php
// tabel: quiz_sesi_template
// quiz_sesi_template
// id_quiz_template int
// nama_sesi        varchar
// skoring_tabel    varchar
// uuid             char

$main_path = Request::segment(1);
loadHelper('akses,function');
$list_kunci_waktu = json_decode(json_encode(array(
							["value"=>0, "text"=>"Fleksibel"],
							["value"=>1, "text"=>"Flat/Kaku"],
					)));

$list_sesi_master = DB::table('quiz_sesi_master')
					->select('id_sesi_master as value','nama_sesi_ujian as text')->orderby('id_sesi_master','asc')->get();
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
				<h5 class="card-title"><b>{{$template->nama_sesi}}</b></h5>
				<h6 class="card-subtitle text-muted">Fitur Ini Digunakan Untuk Manajemen Data Detil Sesi Master Template Tes </h6>
			</div>
			<div class="card-body">
				<a href="{{url('template-tes')}}" class="btn btn-secondary"> <i class="la la-arrow-left"></i> Kembali</a>
				@if(ucc())
				{{Html::btnModal('<i class="la la-plus-circle"></i> Tambah Sesi','modal-tambah','primary')}}
				<hr>
				@endif
				<table id="datatable" class="table table-striped table-hover table-sm" style="width:100%">
					<thead>
						<tr>							
							<th width="5%">ID</th>
							<th width="10%">Urutan</th>
							<th width="25%">Nama Sesi</th>
							<th width="15%">Durasi</th>
							<th width="15%">Waktu</th>
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
{{ Form::bsOpen('form-tambah',url($main_path."/insert-detil")) }}
	{{Html::mOpenLG('modal-tambah','Tambah Master Sesi')}}
		{{ Form::bsHidden('id_quiz_template',$template->id_quiz_template) }}
		{{ Form::bsNumeric('Urutan','urutan','',true,'md-8') }}
		{{ Form::bsSelect2('Sesi Master','id_sesi_master',$list_sesi_master,'',true,'md-8')}}
		{{ Form::bsNumeric('Durasi','durasi','',true,'md-8') }}
		{{ Form::bsSelect2('Tipe Waktu','kunci_waktu',$list_kunci_waktu,'',true,'md-8')}}
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif


@if(ucu())
<!-- MODAL FORM EDIT -->
{{ Form::bsOpen('form-edit',url($main_path."/update-detil")) }}
	{{Html::mOpenLG('modal-edit','Edit Master Sesi')}}
		{{ Form::bsHidden('id_quiz_template',$template->id_quiz_template) }}
		{{ Form::bsNumeric('Urutan','urutan','',true,'md-8') }}
		{{ Form::bsSelect2('Sesi Master','id_sesi_master',$list_sesi_master,'',true,'md-8')}}
		{{ Form::bsNumeric('Durasi','durasi','',true,'md-8') }}
		{{ Form::bsSelect2('Tipe Waktu','kunci_waktu',$list_kunci_waktu,'',true,'md-8')}}
		{{ Form::bsHidden('uuid','') }}
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif


@if(ucd())
<!-- FORM DELETE -->
{{ Form::bsOpen('form-delete',url($main_path."/delete-detil")) }}
	{{ Form::bsHidden('uuid','') }}
{{ Form::bsClose()}}
@endif


@endsection

@section("js")
<script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script type="text/javascript">
	$(function() {

		//var toolbarOptions = [['bold', 'italic'],['link', 'image']];
		var $tabel1 = $('#datatable').DataTable({
			processing: true,
			responsive: true,
			fixedHeader: true,
			serverSide: true,
			ajax: "{{url($main_path.'/dt-detil/'.$template->uuid)}}",
			"iDisplayLength": 25,
			columns: [
				{data: 'id_quiz_sesi_template',name: "id_quiz_sesi_template",
					orderable: false,searchable: false,sClass: "text-center"},
				{data: 'urutan',name: "urutan",orderable: false,searchable: false,sClass: "text-center"},
				{data: 'nama_sesi_ujian',name: "nama_sesi_ujian",orderable: false,searchable: false,sClass: ""},
				{data: 'durasi',name: "durasi",orderable: false,searchable: false,sClass: "text-center"},
				{data: 'kunci_waktu',name: "kunci_waktu",orderable: false,searchable: false,sClass: "text-center"},
				{data: 'action',orderable: false,searchable: false,sClass: "text-center"},
			],
				"fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
					$(nRow).addClass(aData["rowClass"]);
					return nRow;
			},
			"drawCallback": function(settings) {
				initTooltips();
				initKonfirmDelete();
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
			//$("#petunjuk_tambah .ql-editor").html('');
			//$("#form-tambah #petunjuk_sesi").val('');
			$('#form-tambah #id_sesi_master').selectize()[0].selectize.clear();
			$('#form-tambah #kunci_waktu').selectize()[0].selectize.clear();
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
			//$("#petunjuk_edit .ql-editor").html('');
			$('#form-edit #id_sesi_master').selectize()[0].selectize.clear();
			$('#form-edit #kunci_waktu').selectize()[0].selectize.clear();
			$.get("{{url($main_path.'/get-data-detil')}}/" + $uuid, function(respon) {
				if (respon.status) {
					$('#form-edit #id_sesi_master').selectize()[0].selectize.setValue(respon.data.id_sesi_master,false);
					$('#form-edit #kunci_waktu').selectize()[0].selectize.setValue(respon.data.kunci_waktu,false);
					$('#form-edit #durasi').val(respon.data.durasi);
					$('#form-edit #urutan').val(respon.data.urutan);
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

				$.get("{{url($main_path.'/get-data-detil')}}/" + $uuid, function(respon) {
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