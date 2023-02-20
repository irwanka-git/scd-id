<?php
$main_path = Request::segment(1);
loadHelper('akses'); 
$list_provinces = DB::table('provinces')
					->select('id as value','name as text')->get();

?>
@extends('layout')
@section("pagetitle")
	BERANDALAN
@endsection

@section('content')

		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h5 class="card-title">Lokasi Tes</h5>
						<h6 class="card-subtitle text-muted">Fitur Ini Digunakan Untuk Manajemen Lokasi Tes Pada Sistem </h6>
					</div>
					<div class="card-body">
						@if(ucc())
				   		{{Html::btnModal('<i class="la la-plus-circle"></i> Tambah lokasi','modal-tambah','primary')}}
				   		<hr>
				   		@endif
						<table id="datatable" class="table table-striped table-hover table-sm" style="width:100%">
							<thead>
								<tr>
									<th>#</th>
									<th>Provinsi</th>
									<th>Kabupaten/Kota</th>
									<th>Nama Lokasi</th>
									<th>Actions</th>
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
	{{Html::mOpenLG('modal-tambah','Tambah lokasi')}}
		{{ Form::bsSelect2('Provinsi','kode_provinsi',$list_provinces,'',true,'md-8')}}
		{{ Form::bsSelect2('Kabupaten / Kota','kode_kabupaten',null,'',true,'md-8')}}
		{{ Form::bsTextField('Nama lokasi','nama_lokasi','',true,'md-8') }}
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif

@if(ucu())
<!-- MODAL FORM EDIT -->
{{ Form::bsOpen('form-edit',url($main_path."/update")) }}
	{{Html::mOpenLG('modal-edit','Edit lokasi')}}
		{{ Form::bsSelect2('Provinsi','kode_provinsi',$list_provinces,'',true,'md-8')}}
		{{ Form::bsSelect2('Kabupaten / Kota','kode_kabupaten',null,'',true,'md-8')}}
		{{ Form::bsTextField('Nama lokasi','nama_lokasi','',true,'md-8') }}
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
	$(function(){
		 

		var $tabel1 = $('#datatable').DataTable({
		    processing: true,
		    responsive: true,
		    fixedHeader: true,
		    serverSide: true,
		    ajax: "{{url('lokasi-tes/dt')}}",
		    "iDisplayLength": 25,
		    columns: [
		    	 {data:'DT_Row_Index' , orderable:false, searchable: false,sClass:""},
		         {data:'provinsi' , name:"provinsi" , orderable:false, searchable: false,sClass:""},
		         {data:'kabupaten' , name:"kabupaten" , orderable:false, searchable: false,sClass:""},
		         {data:'nama_lokasi' , name:"nama_lokasi" , orderable:false, searchable: false,sClass:""},
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

		@if(ucc())


		var $select_kab_tambah = $('#form-tambah #kode_kabupaten').selectize({
			valueField: 'id',
			labelField: 'name',
			searchField: 'name',
			options: [],
			create: false
		});

	    var $select_kab_tambah_control = $select_kab_tambah[0].selectize;
	    $select_kab_tambah_control.clear();

		$validator_form_tambah = $("#form-tambah").validate();
		$("#modal-tambah").on('show.bs.modal', function(e){
			$validator_form_tambah.resetForm();
			$("#form-tambah").clearForm();
			$('#form-tambah #kode_provinsi').selectize()[0].selectize.clear();
			$select_kab_tambah_control.clearOptions();
			$select_kab_tambah_control.clear();
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

		$("#form-tambah #kode_provinsi").on('change', function(){
			$val = $("#form-tambah #kode_provinsi").val();
			//alert($val);
			if($val!==""){
				$.get("{{url('lokasi-tes/get-list-kabupaten')}}/"+$val, function(respon){
					$select_kab_tambah_control.clearOptions();
					$select_kab_tambah_control.addOption(respon.data);
					$select_kab_tambah_control.clear();
				}) 
			}
		})

		@endif

		@if(ucu())

		var $select_kab_edit = $('#form-edit #kode_kabupaten').selectize({
			valueField: 'id',
			labelField: 'name',
			searchField: 'name',
			options: [],
			create: false
		});

	    var $select_kab_edit_control = $select_kab_edit[0].selectize;
	    $select_kab_edit_control.clear();
	    var $default_kode_kabupaten = 0;

		$validator_form_edit = $("#form-edit").validate();
		$("#modal-edit").on('show.bs.modal', function(e){
			$uuid  = $(e.relatedTarget).data('uuid');
			$validator_form_edit.resetForm();
			$("#form-edit").clearForm();
			$('#form-edit #kode_provinsi').selectize()[0].selectize.clear();
			$select_kab_edit_control.clearOptions();
			$select_kab_edit_control.clear();
			disableButton("#form-edit button[type=submit]")
			$.get("{{url('lokasi-tes/get-data')}}/"+$uuid, function(respon){
				if(respon.status){
					$('#form-edit #uuid').val(respon.data.uuid);
					$default_kode_kabupaten = respon.data.kode_kabupaten;
					$('#form-edit #kode_provinsi').selectize()[0].selectize.setValue(respon.data.kode_provinsi,false);
					$('#form-edit #nama_lokasi').val(respon.data.nama_lokasi);
					
					enableButton("#form-edit button[type=submit]");
				}else{
					errorNotify(respon.message);
				}
			})
		});

		$("#form-edit #kode_provinsi").on('change', function(){
			$val = $("#form-edit #kode_provinsi").val();
			//alert($val);
			if($val!==""){
				//alert(1);
				$.get("{{url('lokasi-tes/get-list-kabupaten')}}/"+$val, function(respon){
					$select_kab_edit_control.clearOptions();
					$select_kab_edit_control.addOption(respon.data);
					$select_kab_edit_control.clear();
					if($default_kode_kabupaten > 0){
						$select_kab_edit_control.setValue($default_kode_kabupaten,false);
						$default_kode_kabupaten = 0;
					}
				}) 
			}
		})

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
				 
				$.get("{{url('lokasi-tes/get-data')}}/"+$uuid, function(respon){
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