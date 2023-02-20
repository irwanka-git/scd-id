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
						<h5 class="card-title">Soal-Soal Test Kognitf</h5>
						<h6 class="card-subtitle text-muted">Fitur Ini Digunakan Untuk Input Soal-Soal Kognitif </h6>
					</div>
					<div class="card-body">
						@if(ucc())
				   		{{Html::btnModal('<i class="la la-plus-circle"></i> Tambah Soal','modal-tambah','primary')}}
				   		@endif
				   		<hr>
				   		<div class="row">
							<div class="col-3">
								{{ Form::bsSelect2('Paket Soal','cari_paket',$list_paket,'',false,'md-8')}}
							</div>
							<div class="col-3">
								{{ Form::bsSelect2('Bidang/Jenis','cari_bidang',$list_bidang,'',false,'md-8')}}
							</div>
							<div class="col-3">
								 <button id="btn-filter-data" class="btn btn-secondary" style="margin-top: 1.9rem !important;"><i class="la la-search-plus"></i> Filter</button>&nbsp;
								 <button id="btn-filter-reset" class="btn btn-light" style="margin-top: 1.9rem !important;"><i class="la la-refresh"></i> Reset</button>
							</div>
						</div>
				   		<hr>
						<table id="datatable" class="table table-striped table-hover table-sm" style="width:100%">
							<thead>
								<tr>
									<th width="5%">#</th>
									<th width="5%">Paket</th>
									<th width="20%">Bidang</th>
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
		{{ Form::bsSelect2('Paket Soal','paket',$list_paket,'',true,'md-8')}}
		{{ Form::bsSelect2('Bidang Soal','bidang',$list_bidang,'',true,'md-8')}}
		{{ Form::bsSelect2('Petunjuk Soal','id_petunjuk',$list_petunjuk,'',false,'md-8')}}
		{{ Form::bsNumeric('Nomor Urut','urutan','',true,'md-8') }}
		{{ Form::bsTextArea('Pertanyaan','pertanyaan','',false,'md-8') }}
		<div class="mb-3">
			<div class="input-group">
				<button id="btn_pertanyaan_gambar" class="btn btn-primary btn-upload-gambar" data-field="pertanyaan_gambar" 
					data-form="form-tambah" type="button"><i class="la la-upload"></i> Upload Gambar</button>
				<input type="text"  name="pertanyaan_gambar" id="pertanyaan_gambar" class="form-control">
				<button data-field="pertanyaan_gambar" data-form="form-tambah" class="btn btn-secondary btn-lihat-gambar" type="button"><i class="la la-eye"></i> Lihat</button>
			</div>
		</div>
		<hr>
		{{ Form::bsTextField('Pilihan A','pilihan_a','',false,'md-8') }}
		<div class="mb-3">
			<div class="input-group">
				<button id="btn_pilihan_a" class="btn btn-primary btn-upload-gambar" data-field="pilihan_a_gambar" 
					data-form="form-tambah" type="button"><i class="la la-upload"></i> Upload Gambar</button>
				<input type="text"  name="pilihan_a_gambar" id="pilihan_a_gambar" class="form-control">
				<button data-field="pilihan_a_gambar" data-form="form-tambah"  class="btn btn-secondary btn-lihat-gambar" type="button"><i class="la la-eye"></i> Lihat</button>
			</div>
		</div>
		<hr>
		{{ Form::bsTextField('Pilihan B','pilihan_b','',false,'md-8') }}
		<div class="mb-3">
			<div class="input-group">
				<button id="btn_pilihan_b" class="btn btn-primary btn-upload-gambar" data-field="pilihan_b_gambar" 
					data-form="form-tambah" type="button"><i class="la la-upload"></i> Upload Gambar</button>
				<input type="text"  name="pilihan_b_gambar" id="pilihan_b_gambar" class="form-control">
				<button data-field="pilihan_b_gambar" data-form="form-tambah" class="btn btn-secondary btn-lihat-gambar" type="button"><i class="la la-eye"></i> Lihat</button>
			</div>
		</div>
		<hr>
		{{ Form::bsTextField('Pilihan C','pilihan_c','',false,'md-8') }}
		<div class="mb-3">
			<div class="input-group">
				<button id="btn_pilihan_c" class="btn btn-primary btn-upload-gambar" data-field="pilihan_c_gambar" 
					data-form="form-tambah" type="button"><i class="la la-upload"></i> Upload Gambar</button>
				<input type="text"  name="pilihan_c_gambar" id="pilihan_c_gambar" class="form-control">
				<button data-field="pilihan_c_gambar" data-form="form-tambah"  class="btn btn-secondary btn-lihat-gambar" type="button"><i class="la la-eye"></i> Lihat</button>
			</div>
		</div>
		<hr>
		{{ Form::bsTextField('Pilihan D','pilihan_d','',false,'md-8') }}
		<div class="mb-3">
			<div class="input-group">
				<button id="btn_pilihan_d" class="btn btn-primary btn-upload-gambar" data-field="pilihan_d_gambar" 
					data-form="form-tambah" type="button"><i class="la la-upload"></i> Upload Gambar</button>
				<input type="text"  name="pilihan_d_gambar" id="pilihan_d_gambar" class="form-control">
				<button data-field="pilihan_d_gambar" data-form="form-tambah" class="btn btn-secondary btn-lihat-gambar" type="button"><i class="la la-eye"></i> Lihat</button>
			</div>
		</div>
		<hr>
		{{ Form::bsTextField('Pilihan E','pilihan_e','',false,'md-8') }}
		<div class="mb-3">
			<div class="input-group">
				<button id="btn_pilihan_e" class="btn btn-primary btn-upload-gambar" data-field="pilihan_e_gambar" 
					data-form="form-tambah" type="button"><i class="la la-upload"></i> Upload Gambar</button>
				<input type="text"  name="pilihan_e_gambar" id="pilihan_e_gambar" class="form-control">
				<button data-field="pilihan_e_gambar" data-form="form-tambah"  class="btn btn-secondary btn-lihat-gambar" type="button"><i class="la la-eye"></i> Lihat</button>
			</div>
		</div>
		 		 
		<hr>
		{{ Form::bsSelect2('Jawaban','pilihan_jawaban',$list_jawaban,'',true,'md-8')}}
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif


@if(ucu())
 <!-- MODAL FORM TAMBAH -->
{{ Form::bsOpen('form-edit',url($main_path."/update")) }}
	{{Html::mOpenLG('modal-edit','Ubah Soal')}}
		{{ Form::bsSelect2('Paket Soal','paket',$list_paket,'',true,'md-8')}}
		{{ Form::bsSelect2('Bidang Soal','bidang',$list_bidang,'',true,'md-8')}}
		{{ Form::bsSelect2('Petunjuk Soal','id_petunjuk',$list_petunjuk,'',false,'md-8')}}
		{{ Form::bsNumeric('Nomor Urut','urutan','',true,'md-8') }}
		{{ Form::bsTextArea('Pertanyaan','pertanyaan','',false,'md-8') }}
		<div class="mb-3">
			<div class="input-group">
				<button id="btn_pertanyaan_gambar" class="btn btn-primary btn-upload-gambar" data-field="pertanyaan_gambar" 
					data-form="form-edit" type="button"><i class="la la-upload"></i> Upload Gambar</button>
				<input type="text"  name="pertanyaan_gambar" id="pertanyaan_gambar" class="form-control">
				<button data-field="pertanyaan_gambar" data-form="form-edit" class="btn btn-secondary btn-lihat-gambar" type="button"><i class="la la-eye"></i> Lihat</button>
			</div>
		</div>
		<hr>
		{{ Form::bsTextField('Pilihan A','pilihan_a','',false,'md-8') }}
		<div class="mb-3">
			<div class="input-group">
				<button id="btn_pilihan_a" class="btn btn-primary btn-upload-gambar" data-field="pilihan_a_gambar" 
					data-form="form-edit" type="button"><i class="la la-upload"></i> Upload Gambar</button>
				<input type="text"  name="pilihan_a_gambar" id="pilihan_a_gambar" class="form-control">
				<button data-field="pilihan_a_gambar" data-form="form-edit"  class="btn btn-secondary btn-lihat-gambar" type="button"><i class="la la-eye"></i> Lihat</button>
			</div>
		</div>
		<hr>
		{{ Form::bsTextField('Pilihan B','pilihan_b','',false,'md-8') }}
		<div class="mb-3">
			<div class="input-group">
				<button id="btn_pilihan_b" class="btn btn-primary btn-upload-gambar" data-field="pilihan_b_gambar" 
					data-form="form-edit" type="button"><i class="la la-upload"></i> Upload Gambar</button>
				<input type="text"  name="pilihan_b_gambar" id="pilihan_b_gambar" class="form-control">
				<button data-field="pilihan_b_gambar" data-form="form-edit" class="btn btn-secondary btn-lihat-gambar" type="button"><i class="la la-eye"></i> Lihat</button>
			</div>
		</div>
		<hr>
		{{ Form::bsTextField('Pilihan C','pilihan_c','',false,'md-8') }}
		<div class="mb-3">
			<div class="input-group">
				<button id="btn_pilihan_c" class="btn btn-primary btn-upload-gambar" data-field="pilihan_c_gambar" 
					data-form="form-edit" type="button"><i class="la la-upload"></i> Upload Gambar</button>
				<input type="text"  name="pilihan_c_gambar" id="pilihan_c_gambar" class="form-control">
				<button data-field="pilihan_c_gambar" data-form="form-edit"  class="btn btn-secondary btn-lihat-gambar" type="button"><i class="la la-eye"></i> Lihat</button>
			</div>
		</div>
		<hr>
		{{ Form::bsTextField('Pilihan D','pilihan_d','',false,'md-8') }}
		<div class="mb-3">
			<div class="input-group">
				<button id="btn_pilihan_d" class="btn btn-primary btn-upload-gambar" data-field="pilihan_d_gambar" 
					data-form="form-edit" type="button"><i class="la la-upload"></i> Upload Gambar</button>
				<input type="text"  name="pilihan_d_gambar" id="pilihan_d_gambar" class="form-control">
				<button data-field="pilihan_d_gambar" data-form="form-edit" class="btn btn-secondary btn-lihat-gambar" type="button"><i class="la la-eye"></i> Lihat</button>
			</div>
		</div>
		<hr>
		{{ Form::bsTextField('Pilihan E','pilihan_e','',false,'md-8') }}
		<div class="mb-3">
			<div class="input-group">
				<button id="btn_pilihan_e" class="btn btn-primary btn-upload-gambar" data-field="pilihan_e_gambar" 
					data-form="form-edit" type="button"><i class="la la-upload"></i> Upload Gambar</button>
				<input type="text"  name="pilihan_e_gambar" id="pilihan_e_gambar" class="form-control">
				<button data-field="pilihan_e_gambar" data-form="form-edit"  class="btn btn-secondary btn-lihat-gambar" type="button"><i class="la la-eye"></i> Lihat</button>
			</div>
		</div>
		 		 
		<hr>
		{{ Form::bsSelect2('Jawaban','pilihan_jawaban',$list_jawaban,'',true,'md-8')}}
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
		    ajax: "{{url('soal-test-koginitif/dt')}}",
		    "iDisplayLength": 25,
		    columns: [
		    	 {data:'DT_Row_Index' , orderable:false, searchable: false,sClass:""},
		         {data:'paket' , name:"paket" , orderable:true, searchable: false,sClass:""},
		         {data:'bidang' , name:"bidang" , orderable:true, searchable: false,sClass:""},
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



		$("#btn-filter-data").on('click', function(){
				$cari_paket = $("#cari_paket").val();
				$cari_bidang = $("#cari_bidang").val();
				$search="?cari=1";
				$cari = 0;
				if($cari_paket){
					$cari = 1;
					$search += "&paket=" + $cari_paket;
				}
				if($cari_bidang){
					$cari = 1;
					$search += "&bidang=" + $cari_bidang;
				}
				
				if($cari==1){
					$tabel1.ajax.url("{{url($main_path.'/dt')}}" + $search  ).load();
				}else{
					$tabel1.ajax.url("{{url($main_path.'/dt')}}").load();
				}
			});

		$("#btn-filter-reset").on('click', function(){
			 $("#cari_paket").selectize()[0].selectize.setValue('',false);
			 $("#cari_bidang").selectize()[0].selectize.setValue('',false);
			 $tabel1.ajax.url("{{url($main_path.'/dt')}}").load();
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
			$('#form-tambah #paket').selectize()[0].selectize.clear();
			$('#form-tambah #bidang').selectize()[0].selectize.clear();
			$('#form-tambah #id_petunjuk').selectize()[0].selectize.clear();
			$('#form-tambah #pilihan_jawaban').selectize()[0].selectize.clear();
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
				 
				$.get("{{url('soal-test-koginitif/get-data')}}/"+$uuid, function(respon){
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
			$('#form-edit #bidang').selectize()[0].selectize.clear();
			$('#form-edit #id_petunjuk').selectize()[0].selectize.clear();
			$('#form-edit #paket').selectize()[0].selectize.clear();
			$('#form-edit #pilihan_jawaban').selectize()[0].selectize.clear();
			disableButton("#form-edit button[type=submit]")
			$.get("{{url('soal-test-koginitif/get-data')}}/"+$uuid, function(respon){
				if(respon.status){
					$('#form-edit #bidang').selectize()[0].selectize.setValue(respon.data.bidang,false);
					$('#form-edit #id_petunjuk').selectize()[0].selectize.setValue(respon.data.id_petunjuk,false);
					$('#form-edit #paket').selectize()[0].selectize.setValue(respon.data.paket,false);
					$('#form-edit #pilihan_jawaban').selectize()[0].selectize.setValue(respon.data.pilihan_jawaban,false);
					$('#form-edit #uuid').val(respon.data.uuid);
					$('#form-edit #urutan').val(respon.data.urutan);
					$('#form-edit #pertanyaan').val(respon.data.pertanyaan);
					$('#form-edit #pertanyaan_gambar').val(respon.data.pertanyaan_gambar);
					$('#form-edit #pilihan_a').val(respon.data.pilihan_a);
					$('#form-edit #pilihan_a_gambar').val(respon.data.pilihan_a_gambar);
					$('#form-edit #pilihan_b').val(respon.data.pilihan_b);
					$('#form-edit #pilihan_b_gambar').val(respon.data.pilihan_b_gambar);
					$('#form-edit #pilihan_c').val(respon.data.pilihan_c);
					$('#form-edit #pilihan_c_gambar').val(respon.data.pilihan_c_gambar);
					$('#form-edit #pilihan_d').val(respon.data.pilihan_d);
					$('#form-edit #pilihan_d_gambar').val(respon.data.pilihan_d_gambar);
					$('#form-edit #pilihan_e').val(respon.data.pilihan_e);
					$('#form-edit #pilihan_e_gambar').val(respon.data.pilihan_e_gambar);
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