<?php
$main_path = Request::segment(1);
loadHelper('akses,function'); 
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
						<h5 class="card-title">Pilihan Tes Minat Kuliah Agama</h5>
						<h6 class="card-subtitle text-muted">Fitur Ini Digunakan Untuk Input Data Pilihan Tes Minat Kuliah Agama </h6>
					</div>
					<div class="card-body">
						@if(ucc())
				   		{{Html::btnModal('<i class="la la-plus-circle"></i> Tambah Jurusan','modal-tambah','primary')}}
				   		<hr>
				   		@endif
						<table id="datatable" class="table table-striped table-hover table-sm" style="width:100%">
							<thead>
								<tr>
									<th width="5%">No.</th>
									<th width="18%">Jurusan</th>
									<th>Indikator</th>
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
	{{Html::mOpenLG('modal-tambah','Tambah Jurusan')}}
		{{ Form::bsNumeric('Urutan','urutan','',true,'md-8') }}
		{{ Form::bsTextField('Jurusan','jurusan','',true,'md-8') }}
		<div class="mb-3">
			<label class="form-label">Indikator (AItem)  <star>*</star> </label>
			<div  id="indikator_tambah"></div>
			<textarea style="display: none;" name="indikator" id="indikator"  required="required"></textarea>
		</div>
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif


@if(ucu())
 <!-- MODAL FORM TAMBAH -->
{{ Form::bsOpen('form-edit',url($main_path."/update")) }}
	{{Html::mOpenLG('modal-edit','Ubah Jurusan')}}
		{{ Form::bsNumeric('Urutan','urutan','',true,'md-8') }}
		{{ Form::bsTextField('Jurusan','jurusan','',true,'md-8') }}
		<div class="mb-3">
			<label class="form-label">Indikator (AItem)  <star>*</star> </label>
			<div  id="indikator_edit"></div>
			<textarea style="display: none;" name="indikator" id="indikator"  required="required"></textarea>
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


{{Html::mOpenLG('modal-lihat-soal','Minat')}}
	<div id="panel-lihat-soal">
		
	</div>
{{Html::mCloseLG()}}

 

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
			    ajax: "{{url($main_path.'/dt')}}",
			    "iDisplayLength": 25,
			    columns: [
			         {data:'urutan' , name:"urutan" , orderable:true, searchable: false,sClass:"text-center"},
			         {data:'jurusan' , name:"jurusan" , orderable:false, searchable: false,sClass:"text-center"},
			         {data:'indikator' , name:"indikator" , orderable:false, searchable: false,sClass:""},
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
				$.get("{{url($main_path.'/lihat-soal')}}/"+$uuid, function(respon){
					 $("#panel-lihat-soal").html(respon);
				})
			});
 

			@if(ucc())

			 var quill_indikator_tambah = new Quill('#indikator_tambah', {
			    placeholder: 'Tulis Isi Kegiatan.....',
			    theme: 'snow',
			    modules: {
				    toolbar: toolbarOptions
				  }
			  });
 			 quill_indikator_tambah.on('text-change', function(delta, oldDelta, source) {
 			 		$("#form-tambah #indikator").val($("#indikator_tambah .ql-editor").html());
 			 });

			$validator_form_tambah = $("#form-tambah").validate();
			$("#modal-tambah").on('show.bs.modal', function(e){
				$validator_form_tambah.resetForm();
				$("#form-tambah").clearForm();
				$("#indikator_tambah .ql-editor").html('');
				$("#form-tambah #indikator").val('');
				//$('#form-tambah #kelompok').selectize()[0].selectize.clear();
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

		var quill_indikator_edit = new Quill('#indikator_edit', {
		    placeholder: 'Tulis Isi indikator.....',
		    theme: 'snow',
		    modules: {
			    toolbar: toolbarOptions
			  }
		  });
			 quill_indikator_edit.on('text-change', function(delta, oldDelta, source) {
			 		$("#form-edit #indikator").val($("#indikator_edit .ql-editor").html());
			 });

		$validator_form_edit = $("#form-edit").validate();
		$("#modal-edit").on('show.bs.modal', function(e){
			$uuid  = $(e.relatedTarget).data('uuid');
			$validator_form_edit.resetForm();
			//$('#form-edit #kelompok').selectize()[0].selectize.clear();
			$("#form-edit").clearForm();
			disableButton("#form-edit button[type=submit]");
			$("#indikator_edit .ql-editor").html('');
			$.get("{{url($main_path.'/get-data')}}/"+$uuid, function(respon){
				if(respon.status){
					quill_indikator_edit.clipboard.dangerouslyPasteHTML(respon.data.indikator);
					$('#form-edit #indikator').val(respon.data.indikator);
					//$('#form-edit #kelompok').selectize()[0].selectize.setValue(respon.data.kelompok,false);
					$('#form-edit #urutan').val(respon.data.urutan);
					$('#form-edit #jurusan').val(respon.data.jurusan);
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
				 
				$.get("{{url($main_path.'/get-data')}}/"+$uuid, function(respon){
					if(respon.status){
						$("#form-delete #uuid").val(respon.data.uuid);
						$.confirm({
						    title: 'Yakin Hapus Jurusan Ini?',
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