 <?php
$main_path = Request::segment(1);
loadHelper('akses'); 
?>
@extends('layout')
@section("pagetitle")
	 
@endsection

@section('content')

		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h5 class="card-title">Informasi Cerdas</h5>
						<h6 class="card-subtitle text-muted">Fitur Ini Digunakan Untuk Kelola Informasi Cerdas</h6>
					</div>
					<div class="card-body">
						@if(ucc())
				   		{{Html::btnModal('<i class="la la-plus-circle"></i> Tambah Informasi','modal-tambah','primary')}}
				   		<hr>
				   		@endif
						<table id="datatable" class="table table-striped table-hover table-sm" style="width:100%">
							<thead>
								<tr>
									<th width="5%">#</th>
									<th>Judul Informasi</th>
									<th width="25%">Created At</th>
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
	{{Html::mOpenLG('modal-tambah','Tambah Informasi Cerdas')}}
		{{ Form::bsTextField('Judul','judul','',true,'md-8') }}
		{{ Form::bsTextField('URL','url','',true,'md-8') }}
		<div class="mb-3">
			<div class="input-group">
				<button id="btn_gambar" class="btn btn-primary btn-upload-gambar" data-field="gambar" 
					data-form="form-tambah" type="button"><i class="la la-upload"></i> Upload Gambar</button>
				<input type="text" required="required"  
					name="gambar" id="gambar" class="form-control">
				<button data-field="gambar" data-form="form-tambah" class="btn btn-secondary btn-lihat-gambar" type="button"><i class="la la-eye"></i> Lihat</button>
			</div>
		</div>
		<div class="mb-3">
			<label class="form-label">Isi Informasi  <star>*</star> </label>
			<textarea name="isi_tambah" id="isi_tambah"></textarea>
			<textarea style="display: none;" name="isi" id="isi"  required="required"></textarea>
		</div>

	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif

@if(ucu())
<!-- MODAL FORM EDIT -->
{{ Form::bsOpen('form-edit',url($main_path."/update")) }}
	{{Html::mOpenLG('modal-edit','Edit Petunjuk')}}
		{{ Form::bsTextField('Judul','judul','',true,'md-8') }}
		{{ Form::bsTextField('URL','url','',true,'md-8') }}
		<div class="mb-3">
			<div class="input-group">
				<button id="btn_gambar" class="btn btn-primary btn-upload-gambar" data-field="gambar" 
					data-form="form-edit" type="button"><i class="la la-upload"></i> Upload Gambar</button>
				<input type="text" required="required"  
					name="gambar" id="gambar" class="form-control">
				<button data-field="gambar" data-form="form-edit" class="btn btn-secondary btn-lihat-gambar" type="button"><i class="la la-eye"></i> Lihat</button>
			</div>
		</div>
		<div class="mb-3">
			<label class="form-label">Isi Informasi  <star>*</star> </label>
			<!-- <textarea style="display: none;" name="isi" id="isi"  required="required"></textarea> -->
			<textarea name="isi_edit" id="isi_edit"></textarea>
			<textarea style="display: none;" name="isi" id="isi"  required="required"></textarea>
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



{{ Form::bsOpen('form-upload-gambar',url($main_path."/upload-gambar")) }}
	 <input type="file" style="display: none;" id="upload-gambar" name="image" accept="image/*">
{{ Form::bsClose()}}

@endsection

@section("js")
<script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script src="https://cdn.ckeditor.com/4.19.0/standard/ckeditor.js"></script>

<script type="text/javascript">
	var toolbarOptions = [['bold', 'italic'], ['link'],[{'list': 'bullet'}]];
	var $tabel1 = $('#datatable').DataTable({
		    processing: true,
		    responsive: true,
		    fixedHeader: true,
		    serverSide: true,
		    ajax: "{{url('info-cerdas/dt')}}",
		    "iDisplayLength": 25,
		    columns: [
		    	 {data:'DT_Row_Index' , orderable:false, searchable: false,sClass:""},
		         {data:'judul' , name:"judul" , orderable:false, searchable: false,sClass:""},
		         {data:'created_at' , name:"created_at" , 
		         	orderable:false, searchable: false,sClass:""},
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

		// var quill_isi_tambah = new Quill('#isi_tambah', {
		// 	    placeholder: 'Tulis Isi Informasi.....',
		// 	    theme: 'snow',
		// 	    modules: {
		// 		    toolbar: toolbarOptions
		// 		  }
		// 	  });
		//  quill_isi_tambah.on('text-change', function(delta, oldDelta, source) {
		//  		$("#form-tambah #isi").val($("#isi_tambah .ql-editor").html());
		//  });

		var editor_tambah = CKEDITOR.replace('isi_tambah');
		editor_tambah.on( 'change', function( evt ) {
		    // getData() returns CKEditor's HTML content.
		    // var data_isi = CKEDITOR.instances['isi_edit'].getData();
			$('#form-tambah #isi').val(evt.editor.getData());
		    //console.log(evt.editor.getData());
		});

		$validator_form_tambah = $("#form-tambah").validate();
		$("#modal-tambah").on('show.bs.modal', function(e){
			$validator_form_tambah.resetForm();
			$("#form-tambah").clearForm();
			$("#isi_tambah .ql-editor").html('');
			$("#form-tambah #isi").val('');
			CKEDITOR.instances['isi_tambah'].setData('')
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

		// var quill_isi_edit = new Quill('#isi_edit', {
		//     placeholder: 'Tulis Isi Informasi.....',
		//     theme: 'snow',
		//     modules: {
		// 	    toolbar: toolbarOptions
		// 	  }
		//   });
		// 	 quill_isi_edit.on('text-change', function(delta, oldDelta, source) {
		// 	 		$("#form-edit #isi").val($("#isi_edit .ql-editor").html());
		// 	 });

		var editor_edit = CKEDITOR.replace('isi_edit');
		editor_edit.on( 'change', function( evt ) {
		    // getData() returns CKEditor's HTML content.
		    // var data_isi = CKEDITOR.instances['isi_edit'].getData();
			$('#form-edit #isi').val(evt.editor.getData());
		    //console.log(evt.editor.getData());
		});

		$validator_form_edit = $("#form-edit").validate();
		$("#modal-edit").on('show.bs.modal', function(e){
			$uuid  = $(e.relatedTarget).data('uuid');
			$validator_form_edit.resetForm();
			$("#form-edit").clearForm();
			disableButton("#form-edit button[type=submit]");
			//$("#isi_edit .ql-editor").html('');
			
			$.get("{{url('info-cerdas/get-data')}}/"+$uuid, function(respon){
				if(respon.status){
					//$('#form-edit #isi_edit').val(respon.data.isi);
					CKEDITOR.instances['isi_edit'].setData(respon.data.isi)
					//quill_isi_edit.clipboard.dangerouslyPasteHTML(respon.data.isi);
					$('#form-edit #judul').val(respon.data.judul);
					$('#form-edit #url').val(respon.data.url);
					$('#form-edit #isi').val(respon.data.isi);
					$('#form-edit #gambar').val(respon.data.gambar);
					$('#form-edit #uuid').val(respon.data.uuid);
					enableButton("#form-edit button[type=submit]");
				}else{
					errorNotify(respon.message);
				}
			})
		});

		$('#form-edit').ajaxForm({
			beforeSubmit:function(){
				
				disableButton("#form-edit button[type=submit]")
			},
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
				 
				$.get("{{url('info-cerdas/get-data')}}/"+$uuid, function(respon){
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

</script>

@endsection