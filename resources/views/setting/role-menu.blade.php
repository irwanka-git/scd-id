<?php
$main_path = Request::segment(1);
loadHelper('akses'); 
$list_menu = DB::select("select a.id_menu as value, concat(b.nama_menu,' => ', a.nama_menu) as text 
from menu as a, menu as b where a.id_menu_induk = b.id_menu and a.id_menu_induk> 0");
?>
@extends('layout')
@section("pagetitle")
	 
@endsection

@section('content')

		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h5 class="card-title">Menu Role:  {{$role->nama_role}}</h5>
						<h6 class="card-subtitle text-muted">Fitur Ini Digunakan Untuk Manajemen Menu dan Hak Ases  [{{$role->nama_role}}] </h6>
					</div>
					<div class="card-body">
						<a href="{{url('setting-role')}}" class="btn btn-secondary"><i class="la la-arrow-left"></i> Kembali</a>
						@if(ucc())
				   		{{Html::btnModal('<i class="la la-plus-circle"></i> Tambah Menu','modal-tambah','primary')}}
				   		<hr>
				   		@endif
						<table id="datatable" class="table table-striped table-hover table-sm" style="width:100%">
							<thead>
								<tr>
									<th width="5%">#</th>
									<th>Nama Menu</th>
									<th width="10%">Create</th>
									<th width="10%">Update</th>
									<th width="10%">Delete</th>
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
{{ Form::bsOpen('form-tambah',url($main_path."/insert-menu")) }}
	{{Html::mOpenLG('modal-tambah','Tambah Menu')}}
		{{ Form::bsSelect2('Menu','id_menu',$list_menu,'',true,'md-8')}}
		{{ Form::bsCheckSwitch('User Can Create','ucc','1',true,'md-8') }}
		{{ Form::bsCheckSwitch('User Can Update','ucu','1',true,'md-8') }}
		{{ Form::bsCheckSwitch('User Can Delete','ucd','1',true,'md-8') }}
		{{ Form::bsHidden('id_role',$role->id_role) }}
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif

@if(ucu())
<!-- MODAL FORM EDIT -->
{{ Form::bsOpen('form-edit',url($main_path."/update-menu")) }}
	{{Html::mOpenLG('modal-edit','Edit Menu')}}
		{{ Form::bsReadOnly('Akses Menu','nama_menu','',true,'md-8') }}
		{{ Form::bsCheckSwitch('User Can Create','ucc','1',true,'md-8') }}
		{{ Form::bsCheckSwitch('User Can Update','ucu','1',true,'md-8') }}
		{{ Form::bsCheckSwitch('User Can Delete','ucd','1',true,'md-8') }}
		{{ Form::bsHidden('uuid','') }}
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif

@if(ucd())
 <!-- FORM DELETE -->
{{ Form::bsOpen('form-delete',url($main_path."/delete-menu")) }}
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
		    ajax: "{{url('setting-role/dt-menu/'.$role->id_role)}}",
		    "iDisplayLength": 25,
		    columns: [
		    	 {data:'DT_Row_Index' , orderable:false, searchable: false,sClass:""},
		         {data:'nama_menu' , name:"nama_menu" , orderable:false, searchable: false,sClass:""},
		         {data:'ucc' , name:"ucc" , orderable:false, searchable: false,sClass:"text-center"},
		         {data:'ucu' , name:"ucu" , orderable:false, searchable: false,sClass:"text-center"},
		         {data:'ucd' , name:"ucd" , orderable:false, searchable: false,sClass:"text-center"},
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
		$validator_form_tambah = $("#form-tambah").validate();
		$("#modal-tambah").on('show.bs.modal', function(e){
			$validator_form_tambah.resetForm();
			$("#form-tambah").clearForm();
			$('#form-tambah #id_menu').selectize()[0].selectize.clear();
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
			$.get("{{url('setting-role/get-menu')}}/"+$uuid, function(respon){
				if(respon.status){
					$('#form-edit #nama_menu').val(respon.data.nama_menu)
					$('#form-edit #uuid').val(respon.data.uuid);
					$('#form-edit #ucc').prop('checked',false);
					if(respon.data.ucc==1){
						$('#form-edit #ucc').prop('checked',true);
					}
					$('#form-edit #ucu').prop('checked',false);
					if(respon.data.ucu==1){
						$('#form-edit #ucu').prop('checked',true);
					}
					$('#form-edit #ucd').prop('checked',false);
					if(respon.data.ucd==1){
						$('#form-edit #ucd').prop('checked',true);
					}
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
				 
				$.get("{{url('setting-role/get-menu')}}/"+$uuid, function(respon){
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