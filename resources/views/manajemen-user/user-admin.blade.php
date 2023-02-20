<?php
$main_path = Request::segment(1);

loadHelper('akses'); 
?>
@extends('layout')
@section("pagetitle")
	 User Admininistrator
@endsection

@section('content')

		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h5 class="card-title">Pengaturan User Administarator</h5>
						<h6 class="card-subtitle text-muted">Fitur Ini Digunakan Untuk Manajemen User Administrator Pada Sistem </h6>
					</div>
					<div class="card-body">
						@if(ucc())
				   		{{Html::btnModal('<i class="la la-plus-circle"></i> Tambah Admin','modal-tambah','primary')}}
				   		<hr>
				   		@endif
						<table id="datatable" class="table table-striped table-hover table-sm" style="width:100%">
							<thead>
								<tr>
									<th width="5%">#</th>
									<th width="15%">Nama Pengguna</th>								
									<th width="15%">Username</th>
									<th width="15%">Email</th>								
									<th width="15%">Telp</th>								
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
{{ Form::bsOpen('form-tambah',url($main_path."/insert")) }}
	{{Html::mOpenLG('modal-tambah','Tambah Admin')}}
		{{ Form::bsTextField('Nama Admin','nama_pengguna','',true,'md-8') }}
		{{ Form::bsTextField('Username','username','',true,'md-8') }}
		{{ Form::bsPassword('Password','password','',true,'md-8') }}
		{{ Form::bsTextField('Email','email','',true,'md-8') }}
		{{ Form::bsTextField('telp','telp','',true,'md-8') }}
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif

@if(ucu())
<!-- MODAL FORM EDIT -->
{{ Form::bsOpen('form-edit',url($main_path."/update")) }}
	{{Html::mOpenLG('modal-edit','Edit Admin')}}
		{{ Form::bsTextField('Nama Admin','nama_pengguna','',true,'md-8') }}
		{{ Form::bsTextField('Username','username','',true,'md-8') }}		
		{{ Form::bsTextField('Email','email','',true,'md-8') }}
		{{ Form::bsTextField('Telp','telp','',true,'md-8') }}
		{{ Form::bsHidden('uuid','') }}
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}

{{ Form::bsOpen('form-password',url($main_path."/update_password")) }}
	{{Html::mOpenLG('modal-password','Ubah Password Admin')}}
		{{ Form::bsReadOnly('Nama Admin','nama_pengguna','',true,'md-8') }}
		{{ Form::bsReadOnly('Username','username','',true,'md-8') }}	
		{{ Form::bsPassword('Password','password','',true,'md-8') }}
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
		    ajax: "{{url('manajemen-user-admin/dt')}}",
		    "iDisplayLength": 25,
		    columns: [
		    	 {data:'DT_Row_Index' , orderable:false, searchable: false,sClass:""},
		         {data:'nama_pengguna' , name:"nama_pengguna" , orderable:false, searchable: false,sClass:""},
		         {data:'username' , name:"username" , orderable:true, searchable: false,sClass:""},
		         {data:'email' , name:"email" , orderable:false, searchable: false,sClass:""},
		         {data:'telp' , name:"telp" , orderable:false, searchable: false,sClass:""},
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

		@if(ucc())
		$validator_form_tambah = $("#form-tambah").validate();
		$("#modal-tambah").on('show.bs.modal', function(e){
			$validator_form_tambah.resetForm();
			$("#form-tambah").clearForm();
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

		@if(ucu())
		$validator_form_edit = $("#form-edit").validate();
		$("#modal-edit").on('show.bs.modal', function(e){
			$uuid  = $(e.relatedTarget).data('uuid');
			$validator_form_edit.resetForm();
			$("#form-edit").clearForm();
			disableButton("#form-edit button[type=submit]")
			$.get("{{url('manajemen-user-admin/get-data')}}/"+$uuid, function(respon){
				if(respon.status){
					$('#form-edit #uuid').val(respon.data.uuid);
					$('#form-edit #nama_pengguna').val(respon.data.nama_pengguna);
					$('#form-edit #username').val(respon.data.username);
					$('#form-edit #email').val(respon.data.email);
					$('#form-edit #telp').val(respon.data.telp);
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
		//password
		@if(ucu())
		$validator_form_password = $("#form-password").validate();
		$("#modal-password").on('show.bs.modal', function(e){
			$uuid  = $(e.relatedTarget).data('uuid');
			$validator_form_password.resetForm();
			$("#form-password").clearForm();
			disableButton("#form-password button[type=submit]")
			$.get("{{url('manajemen-user-admin/get-data')}}/"+$uuid, function(respon){
				if(respon.status){
					$('#form-password #uuid').val(respon.data.uuid);
					$('#form-password #nama_pengguna').val(respon.data.nama_pengguna);
					$('#form-password #username').val(respon.data.username);				
					enableButton("#form-password button[type=submit]");
				}else{
					errorNotify(respon.message);
				}
			})
		});

		$('#form-password').ajaxForm({
			beforeSubmit:function(){disableButton("#form-password button[type=submit]")},
			success:function($respon){
				if ($respon.status==true){
					 $("#modal-password").modal('hide'); 
					 successNotify($respon.message);
					 $tabel1.ajax.reload(null, true);
				}else{
					errorNotify($respon.message);
				}
				enableButton("#form-password button[type=submit]")
			},
			error:function(){
				$("#form-password button[type=submit]").button('reset');
				$("#modal-password").modal('hide'); 
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
				 
				$.get("{{url('manajemen-user-admin/get-data')}}/"+$uuid, function(respon){
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