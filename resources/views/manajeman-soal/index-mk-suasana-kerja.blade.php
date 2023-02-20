<?php
$main_path = Request::segment(1);
loadHelper('akses'); 
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
						<h5 class="card-title">Skala Suasana Kerja</h5>
						<h6 class="card-subtitle text-muted">Fitur Ini Digunakan Untuk Manajemen Kegiatan / Pernyataan Tentang Skala Skala Suasana Kerja </h6>
					</div>
					<div class="card-body">
						@if(ucc())
				   		{{Html::btnModal('<i class="la la-plus-circle"></i> Tambah Kegiatan','modal-tambah','primary')}}
				   		<hr>
				   		@endif
						<table id="datatable" class="table table-striped table-hover table-sm" style="width:100%">
							<thead>
								<tr>
									<th width="5%">#</th>
									<th width="5%">No.</th>
									<th>Kegiatan</th>
									<th width="20%">Keterangan</th>
									<th width="20%">Deskripsi</th>
									<th width="15%">Actions</th>
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
	{{Html::mOpenLG('modal-tambah','Tambah Kegiatan')}}

		{{ Form::bsTextField('Nomor','nomor','',true,'md-8') }}
		<div class="mb-3">
			<label class="form-label">Kegiatan  <star>*</star> </label>
			<div  id="kegiatan_tambah"></div>
			<textarea style="display: none;" name="kegiatan" id="kegiatan"  required="required"></textarea>
		</div>
		<div class="mb-3">
			<div class="input-group">
				<button id="btn_gambar" class="btn btn-primary btn-upload-gambar" data-field="gambar" 
					data-form="form-tambah" type="button"><i class="la la-upload"></i> Upload Gambar</button>
				<input type="text"  name="gambar" id="gambar" class="form-control">
				<button data-field="gambar" data-form="form-tambah" class="btn btn-secondary btn-lihat-gambar" type="button"><i class="la la-eye"></i> Lihat</button>
			</div>
		</div>
		{{ Form::bsTextField('Keterangan','keterangan','',true,'md-8') }}
		<div class="mb-3">
			<label class="form-label">Deskripsi  <star>*</star> </label>
			<div  id="deskripsi_tambah"></div>
			<textarea style="display: none;" name="deskripsi" id="deskripsi"  required="required"></textarea>
		</div>
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif


@if(ucu())
 <!-- MODAL FORM TAMBAH -->
{{ Form::bsOpen('form-edit',url($main_path."/update")) }}
	{{Html::mOpenLG('modal-edit','Ubah Soal')}}

		{{ Form::bsTextField('Nomor','nomor','',true,'md-8') }}
		<div class="mb-3">
			<label class="form-label">Kegiatan  <star>*</star> </label>
			<div  id="kegiatan_edit"></div>
			<textarea style="display: none;" name="kegiatan" id="kegiatan"  required="required"></textarea>
		</div>
		<div class="mb-3">
			<div class="input-group">
				<button id="btn_gambar" class="btn btn-primary btn-upload-gambar" data-field="gambar" 
					data-form="form-edit" type="button"><i class="la la-upload"></i> Upload Gambar</button>
				<input type="text"  name="gambar" id="gambar" class="form-control">
				<button data-field="gambar" data-form="form-edit" class="btn btn-secondary btn-lihat-gambar" type="button"><i class="la la-eye"></i> Lihat</button>
			</div>
		</div>
		{{ Form::bsTextField('Keterangan','keterangan','',true,'md-8') }}
		<div class="mb-3">
			<label class="form-label">Deskripsi  <star>*</star> </label>
			<div  id="deskripsi_edit"></div>
			<textarea style="display: none;" name="deskripsi" id="deskripsi"  required="required"></textarea>
		</div>
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


{{Html::mOpenLG('modal-lihat-soal','Kegiatan')}}
	<div id="panel-lihat-soal">
		
	</div>
{{Html::mCloseLG()}}


{{ Form::bsOpen('form-upload-gambar',url($main_path."/upload-gambar")) }}
	 <input type="file" style="display: none;" id="upload-gambar" name="image" accept="image/*">
{{ Form::bsClose()}}

@endsection

@section("js")
<script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script type="text/javascript">
	$(function(){

			var toolbarOptions = [['bold', 'italic'], ['link', 'image'],[{'list': 'bullet'}]];
			var $tabel1 = $('#datatable').DataTable({
			    processing: true,
			    responsive: true,
			    fixedHeader: true,
			    serverSide: true,
			    ajax: "{{url('soal-mk-suasana-kerja/dt')}}",
			    "iDisplayLength": 25,
			    columns: [
			    	 {data:'DT_Row_Index' , orderable:false, searchable: false,sClass:""},
			         {data:'nomor' , name:"nomor" , orderable:true, searchable: false,sClass:"text-center"},
			         {data:'kegiatan' , name:"kegiatan" , orderable:false, searchable: false,sClass:""},
			         {data:'keterangan' , name:"keterangan" , orderable:false, searchable: false,sClass:""},
			         {data:'deskripsi' , name:"deskripsi" , orderable:false, searchable: false,sClass:""},
			         {data:'action' , orderable:false, searchable: false,sClass:"text-center"},
			        ],
			        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
			        $(nRow).addClass( aData["rowClass"] );
			        return nRow;
			    },
			    "drawCallback": function( settings ) {
			        initKonfirmDelete();
			    }
			});

			$tabel1.on( 'responsive-display', function ( e, datatable, columns ) {
			    initKonfirmDelete();;
			});

			//handle lihat soal
			 $("#modal-lihat-soal").on('show.bs.modal', function(e){
				$uuid  = $(e.relatedTarget).data('uuid');
				$("#panel-lihat-soal").html('<center>Sedang Proses Ambil Data..</center>');
				$.get("{{url('soal-mk-suasana-kerja/lihat-soal')}}/"+$uuid, function(respon){
					 $("#panel-lihat-soal").html(respon);
				})
			});

		 //HANDLE UPLOAD GAMBAR
		 @if(ucc() || ucu() || ucd())
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
						    content: '<div width="100%"><center><img src="'+$base_url_image+'/'+$filename+'" class="img-fluid rounded-lg" ></center></div>',
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
		 @endif

			@if(ucc())

			 var quill_kegiatan_tambah = new Quill('#kegiatan_tambah', {
			    placeholder: 'Tulis Isi Kegiatan.....',
			    theme: 'snow',
			    modules: {
				    toolbar: toolbarOptions
				  }
			  });
 			 quill_kegiatan_tambah.on('text-change', function(delta, oldDelta, source) {
 			 		$("#form-tambah #kegiatan").val($("#kegiatan_tambah .ql-editor").html());
 			 });


 			 var quill_deskripsi_tambah = new Quill('#deskripsi_tambah', {
			    placeholder: 'Tulis Isi Deskripsi.....',
			    theme: 'snow',
			    modules: {
				    toolbar: toolbarOptions
				  }
			  });
 			 quill_deskripsi_tambah.on('text-change', function(delta, oldDelta, source) {
 			 		$("#form-tambah #deskripsi").val($("#deskripsi_tambah .ql-editor").html());
 			 });

			$validator_form_tambah = $("#form-tambah").validate();
			$("#modal-tambah").on('show.bs.modal', function(e){
				$validator_form_tambah.resetForm();
				$("#form-tambah").clearForm();
				$("#kegiatan_tambah .ql-editor").html('');
				$("#form-tambah #kegiatan").val('');
				$("#form-tambah #deskripsi").val('');
				enableButton("#form-tambah button[type=submit]")
			});

			$('#form-tambah').ajaxForm({
				beforeSubmit:function(){
					disableButton("#form-tambah button[type=submit]")
				},
				success:function($respon){
					if ($respon.status==true){
						 $("#modal-tambah").modal('hide'); 
						 successNotify($respon.message);
						 $tabel1.ajax.reload(null, true);
					}else{
						errorNotify($respon.message);
					}
					enableButton("#form-tambah button[type=submit]")
				},
				error:function(){
					$("#form-tambah button[type=submit]").button('reset');
					$("#modal-tambah").modal('hide'); 
					errorNotify('Terjadi Kesalahan!');
				}
			}); 
			@endif

 		
		@if(ucu())

		var quill_kegiatan_edit = new Quill('#kegiatan_edit', {
		    placeholder: 'Tulis Isi Pernyataan.....',
		    theme: 'snow',
		    modules: {
			    toolbar: toolbarOptions
			  }
		  });
			 quill_kegiatan_edit.on('text-change', function(delta, oldDelta, source) {
			 		$("#form-edit #kegiatan").val($("#kegiatan_edit .ql-editor").html());
			 });

		var quill_deskripsi_edit = new Quill('#deskripsi_edit', {
		    placeholder: 'Tulis Isi Deskripsi.....',
		    theme: 'snow',
		    modules: {
			    toolbar: toolbarOptions
			  }
		  });
			 quill_deskripsi_edit.on('text-change', function(delta, oldDelta, source) {
			 		$("#form-edit #deskripsi").val($("#deskripsi_edit .ql-editor").html());
			 });

		$validator_form_edit = $("#form-edit").validate();
		$("#modal-edit").on('show.bs.modal', function(e){
			$uuid  = $(e.relatedTarget).data('uuid');
			$validator_form_edit.resetForm();
			$("#form-edit").clearForm();
			disableButton("#form-edit button[type=submit]");
			$("#kegiatan_edit .ql-editor").html('');
			$("#deskripsi_edit .ql-editor").html('');
			$.get("{{url('soal-mk-suasana-kerja/get-data')}}/"+$uuid, function(respon){
				if(respon.status){
					quill_kegiatan_edit.clipboard.dangerouslyPasteHTML(respon.data.kegiatan);
					quill_deskripsi_edit.clipboard.dangerouslyPasteHTML(respon.data.deskripsi);
					$('#form-edit #kegiatan').val(respon.data.kegiatan);
					$('#form-edit #keterangan').val(respon.data.keterangan);
					$('#form-edit #deskripsi').val(respon.data.deskripsi);
					$('#form-edit #nomor').val(respon.data.nomor);
					$('#form-edit #gambar').val(respon.data.gambar);
					$('#form-edit #uuid').val(respon.data.uuid);
					enableButton("#form-edit button[type=submit]");
				}else{
					errorNotify(respon.message);
				}
			})
		});

		$('#form-edit').ajaxForm({
			beforeSubmit:function(){disableButton("#form-edit button[type=submit]")},
			success:function($respon){
				if ($respon.status==true){
					 $("#modal-edit").modal('hide'); 
					 successNotify($respon.message);
					 $tabel1.ajax.reload(null, true);
				}else{
					errorNotify($respon.message);
				}
				enableButton("#form-edit button[type=submit]")
			},
			error:function(){
				$("#form-edit button[type=submit]").button('reset');
				$("#modal-edit").modal('hide'); 
				errorNotify('Terjadi Kesalahan!');
			}
		}); 
		@endif



		@if(ucd())
		$('#form-delete').ajaxForm({
			beforeSubmit:function(){},
			success:function($respon){
				if ($respon.status==true){
					 successNotify($respon.message);
					 $tabel1.ajax.reload(null, true);
				}else{
					errorNotify($respon.message);
				}
			},
			error:function(){errorNotify('Terjadi Kesalahan!');}
		}); 
		var initKonfirmDelete= function(){
			$('.btn-konfirm-delete').on('click', function(e){
				$uuid  = $(this).data('uuid');
				 
				$.get("{{url('soal-mk-suasana-kerja/get-data')}}/"+$uuid, function(respon){
					if(respon.status){
						$("#form-delete #uuid").val(respon.data.uuid);
						$.confirm({
						    title: 'Yakin Hapus Data?',
						    content: respon.informasi,
						    buttons: {
						        cancel :{
						        	text: 'Batalkan'
						        },
						        confirm: {
						        	text: 'Hapus',
						        	btnClass: 'btn-danger',
						        	action:function(){$("#form-delete").submit()}
						        },
						    }
						});
					}else{
						errorNotify(respon.message);
					}
				})
			})
		}
		@endif
	})
</script>
@endsection