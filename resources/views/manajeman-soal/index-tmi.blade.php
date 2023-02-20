<?php
$main_path = Request::segment(1);
loadHelper('akses,function'); 
$list_kelompok = get_list_enum_values('soal_tmi','kelompok');
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
						<h5 class="card-title">Pilihan Tes Minat Indonesia</h5>
						<h6 class="card-subtitle text-muted">Fitur Ini Digunakan Untuk Input Data Pilihan Tes Minat Indonesia </h6>
					</div>
					<div class="card-body">
						@if(ucc())
				   		{{Html::btnModal('<i class="la la-plus-circle"></i> Tambah Pernyataan','modal-tambah','primary')}}
				   		<hr>
				   		@endif
						<table id="datatable" class="table table-striped table-hover table-sm" style="width:100%">
							<thead>
								<tr>
									<th width="5%">No.</th>
									<th width="18%">Kelompok/Minat</th>
									<th>Pernyataan</th>
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
	{{Html::mOpenLG('modal-tambah','Tambah Pernyataan')}}
		{{ Form::bsNumeric('Urutan','urutan','',true,'md-8') }}
		{{ Form::bsSelect2('Kelompok','kelompok',$list_kelompok,'',true,'md-8')}}
		<div class="mb-3">
			<label class="form-label">Kegiatan  <star>*</star> </label>
			<div  id="pernyataan_tambah"></div>
			<textarea style="display: none;" name="pernyataan" id="pernyataan"  required="required"></textarea>
		</div>
		{{ Form::bsTextField('Minat','minat','',true,'md-8') }}
		{{ Form::bsTextArea('Keterangan','keterangan','',true,'md-8') }}
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif


@if(ucu())
 <!-- MODAL FORM TAMBAH -->
{{ Form::bsOpen('form-edit',url($main_path."/update")) }}
	{{Html::mOpenLG('modal-edit','Ubah Soal')}}

		{{ Form::bsNumeric('Urutan','urutan','',true,'md-8') }}
		{{ Form::bsSelect2('Kelompok','kelompok',$list_kelompok,'',true,'md-8')}}
		<div class="mb-3">
			<label class="form-label">Kegiatan  <star>*</star> </label>
			<div  id="pernyataan_edit"></div>
			<textarea style="display: none;" name="pernyataan" id="pernyataan"  required="required"></textarea>
		</div>
		{{ Form::bsTextField('Minat','minat','',true,'md-8') }}
		{{ Form::bsTextArea('Keterangan','keterangan','',true,'md-8') }}
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


{{Html::mOpenLG('modal-lihat-soal','Pernyataan')}}
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
			    ajax: "{{url('soal-tmi/dt')}}",
			    "iDisplayLength": 25,
			    columns: [
			         {data:'urutan' , name:"urutan" , orderable:true, searchable: false,sClass:"text-center"},
			         {data:'kelompok' , name:"pernyataan" , orderable:false, searchable: false,sClass:"text-center"},
			         {data:'pernyataan' , name:"pernyataan" , orderable:false, searchable: false,sClass:""},
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
				$.get("{{url('soal-tmi/lihat-soal')}}/"+$uuid, function(respon){
					 $("#panel-lihat-soal").html(respon);
				})
			});
 

			@if(ucc())

			 var quill_pernyataan_tambah = new Quill('#pernyataan_tambah', {
			    placeholder: 'Tulis Isi Kegiatan.....',
			    theme: 'snow',
			    modules: {
				    toolbar: toolbarOptions
				  }
			  });
 			 quill_pernyataan_tambah.on('text-change', function(delta, oldDelta, source) {
 			 		$("#form-tambah #pernyataan").val($("#pernyataan_tambah .ql-editor").html());
 			 });

			$validator_form_tambah = $("#form-tambah").validate();
			$("#modal-tambah").on('show.bs.modal', function(e){
				$validator_form_tambah.resetForm();
				$("#form-tambah").clearForm();
				$("#pernyataan_tambah .ql-editor").html('');
				$("#form-tambah #pernyataan").val('');
				$('#form-tambah #kelompok').selectize()[0].selectize.clear();
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

		var quill_pernyataan_edit = new Quill('#pernyataan_edit', {
		    placeholder: 'Tulis Isi Pernyataan.....',
		    theme: 'snow',
		    modules: {
			    toolbar: toolbarOptions
			  }
		  });
			 quill_pernyataan_edit.on('text-change', function(delta, oldDelta, source) {
			 		$("#form-edit #pernyataan").val($("#pernyataan_edit .ql-editor").html());
			 });

		$validator_form_edit = $("#form-edit").validate();
		$("#modal-edit").on('show.bs.modal', function(e){
			$uuid  = $(e.relatedTarget).data('uuid');
			$validator_form_edit.resetForm();
			$('#form-edit #kelompok').selectize()[0].selectize.clear();
			$("#form-edit").clearForm();
			disableButton("#form-edit button[type=submit]");
			$("#pernyataan_edit .ql-editor").html('');
			$.get("{{url('soal-tmi/get-data')}}/"+$uuid, function(respon){
				if(respon.status){
					quill_pernyataan_edit.clipboard.dangerouslyPasteHTML(respon.data.pernyataan);
					$('#form-edit #pernyataan').val(respon.data.pernyataan);
					$('#form-edit #kelompok').selectize()[0].selectize.setValue(respon.data.kelompok,false);
					$('#form-edit #minat').val(respon.data.minat);
					$('#form-edit #keterangan').val(respon.data.keterangan);
					$('#form-edit #urutan').val(respon.data.urutan);
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
				 
				$.get("{{url('soal-tmi/get-data')}}/"+$uuid, function(respon){
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