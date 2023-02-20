<?php
// tabel: quiz_sesi_template
// quiz_sesi_template
// id_tarif int
// nama_sesi        varchar
// skoring_tabel    varchar
// uuid             char

$main_path = Request::segment(1);
loadHelper('akses,function');
 
// dd($list_mode);
?>
@extends('layout')
 
@section("pagetitle")

@endsection

@section('content')

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h5 class="card-title"><b>{{$tarif->nama_tarif}}</b></h5>
				<h6 class="card-subtitle text-muted">Fitur Ini Digunakan Untuk Manajemen Rincian Tarif Paket </h6>
			</div>
			<div class="card-body">
				<a href="{{url('tarif-paket')}}" class="btn btn-secondary"> <i class="la la-arrow-left"></i> Kembali</a>
				@if(ucc())
				{{Html::btnModal('<i class="la la-plus-circle"></i> Tambah Rincian','modal-tambah','primary')}}
				<hr>
				@endif
				<table id="datatable" class="table table-striped table-hover table-sm" style="width:100%">
					<thead>
						<tr>							
							<th width="5%">ID</th>
							<th width="10%">Urutan</th>
							<th width="55%">Nama Rincian</th>
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
	{{Html::mOpenLG('modal-tambah','Tambah Rincian Tarif')}}
		{{ Form::bsHidden('id_tarif',$tarif->id_tarif) }}
		{{ Form::bsNumeric('Urutan','urutan','',true,'md-8') }}
		{{ Form::bsTextField('Nama Rincian','nama_rincian','',true,'md-8') }} 
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif


@if(ucu())
<!-- MODAL FORM EDIT -->
{{ Form::bsOpen('form-edit',url($main_path."/update-detil")) }}
	{{Html::mOpenLG('modal-edit','Edit Rincian Tarif')}}
		{{ Form::bsNumeric('Urutan','urutan','',true,'md-8') }}
		{{ Form::bsTextField('Nama Rincian','nama_rincian','',true,'md-8') }} 
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
			ajax: "{{url($main_path.'/dt-detil/'.$tarif->uuid)}}",
			"iDisplayLength": 25,
			columns: [
				{data: 'id_tarif_rinci',name: "id_tarif_rinci",
					orderable: false,searchable: false,sClass: "text-center"},
				{data: 'urutan',name: "urutan",orderable: false,searchable: false,sClass: "text-center"},
				{data: 'nama_rincian',name: "nama_rincian",orderable: false,searchable: false,sClass: ""},
				{data: 'action',orderable: false,searchable: false,sClass: "text-center"},
			],
				"fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
					$(nRow).addClass(aData["rowClass"]);
					return nRow;
			},
			"drawCallback": function(settings) {
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
 

		$validator_form_edit = $("#form-edit").validate();
		$("#modal-edit").on('show.bs.modal', function(e) {
			$uuid = $(e.relatedTarget).data('uuid');
			$validator_form_edit.resetForm();
			$("#form-edit").clearForm();
			disableButton("#form-edit button[type=submit]"); 
			$.get("{{url($main_path.'/get-data-detil')}}/" + $uuid, function(respon) {
				if (respon.status) {
					$('#form-edit #nama_rincian').val(respon.data.nama_rincian);
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