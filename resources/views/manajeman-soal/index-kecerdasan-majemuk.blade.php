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
						<h5 class="card-title">Soal Kecerdasan Majemuk</h5>
						<h6 class="card-subtitle text-muted">Fitur Ini Digunakan Untuk Input Data Soal Kecerdasan Majemuk </h6>
					</div>
					<div class="card-body">
						@if(ucc())
				   		{{Html::btnModal('<i class="la la-plus-circle"></i> Tambah Soal/Bagian','modal-tambah','primary')}}
				   		<hr>
				   		@endif
						<table id="datatable" class="table table-striped table-hover table-sm" style="width:100%">
							<thead>
								<tr>
									<th width="5%">No.</th>
									<th>Deskripsi</th>
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
	{{Html::mOpenLG('modal-tambah','Tambah Soal')}}
		{{ Form::bsTextField('Nomor','nomor','',true,'md-8') }}
		{{ Form::bsTextArea('Deskripsi','deskripsi','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan A','pernyataan_a','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan B','pernyataan_b','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan C','pernyataan_c','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan D','pernyataan_d','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan E','pernyataan_e','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan F','pernyataan_f','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan G','pernyataan_g','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan H','pernyataan_h','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan I','pernyataan_i','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan J','pernyataan_j','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan K','pernyataan_k','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan L','pernyataan_l','',true,'md-8') }}

	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif


@if(ucu())
 <!-- MODAL FORM TAMBAH -->
{{ Form::bsOpen('form-edit',url($main_path."/update")) }}
	{{Html::mOpenLG('modal-edit','Ubah Soal')}}
		{{ Form::bsTextField('Nomor','nomor','',true,'md-8') }}
		{{ Form::bsTextArea('Deskripsi','deskripsi','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan A','pernyataan_a','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan B','pernyataan_b','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan C','pernyataan_c','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan D','pernyataan_d','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan E','pernyataan_e','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan F','pernyataan_f','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan G','pernyataan_g','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan H','pernyataan_h','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan I','pernyataan_i','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan J','pernyataan_j','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan K','pernyataan_k','',true,'md-8') }}
		{{ Form::bsTextField('Pernyataan L','pernyataan_l','',true,'md-8') }}
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


{{Html::mOpenLG('modal-lihat-soal','Soal')}}
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
			         {data:'nomor' , name:"nomor" , orderable:true, searchable: false,sClass:"text-center"},
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
				$.get("{{url($main_path.'/lihat-soal')}}/"+$uuid, function(respon){
					 $("#panel-lihat-soal").html(respon);
				})
			});
 

			@if(ucc())

			$validator_form_tambah = $("#form-tambah").validate();
			$("#modal-tambah").on('show.bs.modal', function(e){
				$validator_form_tambah.resetForm();
				$("#form-tambah").clearForm();
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

	 
		$validator_form_edit = $("#form-edit").validate();
		$("#modal-edit").on('show.bs.modal', function(e){
			$uuid  = $(e.relatedTarget).data('uuid');
			$validator_form_edit.resetForm();
			//$('#form-edit #kelompok').selectize()[0].selectize.clear();
			$("#form-edit").clearForm();
			disableButton("#form-edit button[type=submit]");
			$.get("{{url($main_path.'/get-data')}}/"+$uuid, function(respon){
				if(respon.status){
					$('#form-edit #nomor').val(respon.data.nomor);
					$('#form-edit #deskripsi').val(respon.data.deskripsi);
					$('#form-edit #pernyataan_a').val(respon.data.pernyataan_a);
					$('#form-edit #pernyataan_b').val(respon.data.pernyataan_b);
					$('#form-edit #pernyataan_c').val(respon.data.pernyataan_c);
					$('#form-edit #pernyataan_d').val(respon.data.pernyataan_d);
					$('#form-edit #pernyataan_e').val(respon.data.pernyataan_e);
					$('#form-edit #pernyataan_f').val(respon.data.pernyataan_f);
					$('#form-edit #pernyataan_g').val(respon.data.pernyataan_g);
					$('#form-edit #pernyataan_h').val(respon.data.pernyataan_h);
					$('#form-edit #pernyataan_i').val(respon.data.pernyataan_i);
					$('#form-edit #pernyataan_j').val(respon.data.pernyataan_j);
					$('#form-edit #pernyataan_k').val(respon.data.pernyataan_k);
					$('#form-edit #pernyataan_l').val(respon.data.pernyataan_l);
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
						    title: 'Yakin Hapus Soal Ini?',
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