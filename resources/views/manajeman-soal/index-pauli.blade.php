<?php
$main_path = Request::segment(1);
loadHelper('akses,function'); 
$list_bidang = get_list_enum_values('soal_kognitif','bidang');
$list_paket = get_list_enum_values('soal_kognitif','paket');
$list_petunjuk = DB::table('petunjuk_soal')
					->select('id_petunjuk as value','isi_petunjuk as text')->orderby('isi_petunjuk','asc')->get();
$list_jawaban = arr_to_list(['A','B','C','D','E']);
?>
@extends('layout')
@section("pagetitle")
	 
@endsection

@section('content')

		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h5 class="card-title">Soal-Soal Pauli Kraeplin</h5>
						<h6 class="card-subtitle text-muted">Fitur Ini Digunakan Untuk Input Pauli Kraeplin</h6>
					</div>
					<div class="card-body">
						@if(ucc())
				   		{{Html::btnModal('<i class="la la-plus-circle"></i> Tambah Soal','modal-tambah','primary')}}
				   		@endif
				   		<hr>
				   		 
						<table id="datatable" class="table table-striped table-hover table-sm" style="width:100%">
							<thead>
								<tr>
									<th width="5%">#</th>
									<th>Pertanyaan</th>
									<th width="10%">Jawaban</th>
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
		{{ Form::bsSelect2('Petunjuk Soal','id_petunjuk',$list_petunjuk,'',false,'md-8')}}
		{{ Form::bsNumeric('Nomor Urut','urutan','',true,'md-8') }}
		{{ Form::bsTextArea('Pertanyaan','pertanyaan','',false,'md-8') }}
		{{ Form::bsTextField('Jawaban','pilihan_jawaban','',false,'md-8') }}  
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif


@if(ucu())
 <!-- MODAL FORM TAMBAH -->
{{ Form::bsOpen('form-edit',url($main_path."/update")) }}
	{{Html::mOpenLG('modal-edit','Ubah Soal')}}
		{{ Form::bsSelect2('Petunjuk Soal','id_petunjuk',$list_petunjuk,'',false,'md-8')}}
		{{ Form::bsNumeric('Nomor Urut','urutan','',true,'md-8') }}
		{{ Form::bsTextArea('Pertanyaan','pertanyaan','',false,'md-8') }}
		{{ Form::bsTextField('Jawaban','pilihan_jawaban','',false,'md-8') }}  
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


{{ Form::bsOpen('form-upload-gambar',url($main_path."/upload-gambar")) }}
	 <input type="file" style="display: none;" id="upload-gambar" name="image" accept="image/*">
{{ Form::bsClose()}}

{{Html::mOpenLG('modal-lihat-soal','Lihat Soal')}}
	<div id="panel-lihat-soal">
		
	</div>
{{Html::mCloseLG()}}

@endsection

@section("js")
<script type="text/javascript">
	$(function(){

		var $tabel1 = $('#datatable').DataTable({
		    processing: true,
		    responsive: true,
		    fixedHeader: true,
		    serverSide: true,
		    ajax: "{{url('soal-pauli-kraeplin/dt')}}",
		    "iDisplayLength": 25,
		    columns: [
		    	 {data:'DT_Row_Index' , orderable:false, searchable: false,sClass:""},
		         {data:'pertanyaan' , name:"pertanyaan" , orderable:false, searchable: false,sClass:""},
		         {data:'pilihan_jawaban' , name:"pilihan_jawaban" , orderable:false, searchable: false,sClass:"text-center"},
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
			$.get("{{url('soal-test-koginitif/lihat-soal')}}/"+$uuid, function(respon){
				 $("#panel-lihat-soal").html(respon);
			})
		});


		@if(ucc())
		$validator_form_tambah = $("#form-tambah").validate();
		$("#modal-tambah").on('show.bs.modal', function(e){
			$validator_form_tambah.resetForm();
			$("#form-tambah").clearForm(); 
			$('#form-tambah #id_petunjuk').selectize()[0].selectize.clear(); 
			enableButton("#form-tambah button[type=submit]")
		});

		$('#form-tambah').ajaxForm({
			beforeSubmit:function(){disableButton("#form-tambah button[type=submit]")},
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
				 
				$.get("{{url('soal-pauli-kraeplin/get-data')}}/"+$uuid, function(respon){
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


		@if(ucu())
		$validator_form_edit = $("#form-edit").validate();
		$("#modal-edit").on('show.bs.modal', function(e){
			$uuid  = $(e.relatedTarget).data('uuid');
			$validator_form_edit.resetForm();
			$("#form-edit").clearForm();
			disableButton("#form-edit button[type=submit]")
			$.get("{{url('soal-pauli-kraeplin/get-data')}}/"+$uuid, function(respon){
				if(respon.status){
					 
					$('#form-edit #uuid').val(respon.data.uuid);
					$('#form-edit #urutan').val(respon.data.urutan);
					$('#form-edit #pertanyaan').val(respon.data.pertanyaan);
					$('#form-edit #pilihan_jawaban').val(respon.data.pilihan_jawaban);
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


	})
</script>
@endsection