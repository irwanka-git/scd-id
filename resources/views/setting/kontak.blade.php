<?php
$main_path = Request::segment(1);
loadHelper('akses'); 
$list_group_menu = DB::table('menu')
					->select('id_menu as value','nama_menu as text')
					->where('id_menu_induk','0')->orderby('urutan','asc')->get();

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
						<h5 class="card-title">Pengaturan Kontak</h5>
						<h6 class="card-subtitle text-muted">Fitur Ini Digunakan Untuk Informasi Kontak Pada Sistem </h6>
					</div>
					<div class="card-body">
						@if(ucc())
				   		{{Html::btnModal('<i class="la la-plus-circle"></i> Tambah Menu','modal-tambah','primary')}}
				   		<hr>
				   		@endif
						<table id="datatable" class="table table-striped table-hover table-sm" style="width:100%">
							<thead>
								<tr>
									<th>#</th>
									<th>Telepon</th>
									<th>Email</th>
									<th>Website</th>
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
 
@if(ucu())
<!-- MODAL FORM EDIT -->
{{ Form::bsOpen('form-edit',url($main_path."/update")) }}
	{{Html::mOpenLG('modal-edit','Edit Kontak')}}
		{{ Form::bsTextField('Telepon','telepon','',true,'md-8') }}
		{{ Form::bsTextField('Email','email','',true,'md-8') }}
		{{ Form::bsTextField('Website','website','',true,'md-8') }}
		{{ Form::bsHidden('uuid','') }}
	{{Html::mCloseSubmitLG('Simpan')}}
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
		    ajax: "{{url('info-kontak/dt')}}",
		    "iDisplayLength": 25,
		    columns: [
		    	 {data:'DT_Row_Index' , orderable:false, searchable: false,sClass:""},
		         {data:'telepon' , name:"telepon" , orderable:true, searchable: false,sClass:""},
		         {data:'email' , name:"email" , orderable:false, searchable: false,sClass:""},
		         {data:'website' , name:"website" , orderable:false, searchable: false,sClass:""},
		         {data:'action' , orderable:false, searchable: false,sClass:"text-center"},
		        ],
		        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
		        $(nRow).addClass( aData["rowClass"] );
		        return nRow;
		    },
		    "drawCallback": function( settings ) {
		        //initKonfirmDelete();
		    }
		});

		$tabel1.on( 'responsive-display', function ( e, datatable, columns ) {
		    //initKonfirmDelete();;
		});
 

		@if(ucu())
		$validator_form_edit = $("#form-edit").validate();
		$("#modal-edit").on('show.bs.modal', function(e){
			$uuid  = $(e.relatedTarget).data('uuid');
			$validator_form_edit.resetForm();
			$("#form-edit").clearForm();
			disableButton("#form-edit button[type=submit]")
			$.get("{{url('info-kontak/get-data')}}/"+$uuid, function(respon){
				if(respon.status){
					$('#form-edit #uuid').val(respon.data.uuid);
					$('#form-edit #telepon').val(respon.data.telepon);
					$('#form-edit #website').val(respon.data.website);
					$('#form-edit #email').val(respon.data.email);
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