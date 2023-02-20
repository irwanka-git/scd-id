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
						<h5 class="card-title">Template Saran</h5>
						<h6 class="card-subtitle text-muted">Fitur Ini Digunakan Untuk Kelola Template Saran </h6>
					</div>
					<div class="card-body">
						@if(ucc())
				   		{{Html::btnModal('<i class="la la-plus-circle"></i> Tambah Template','modal-tambah','primary')}}
				   		<hr>
				   		@endif
						<table id="datatable" class="table table-striped table-hover table-sm" style="width:100%">
							<thead>
								<tr>
									<th width="5%">#</th>
									<th width="40%">Nama Template</th>
									<th width="45%">Skoring Tabel</th>
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
	{{Html::mOpenLG('modal-tambah','Tambah Template Saran')}}
		{{ Form::bsTextField('Nama Template','nama_template_saran','',true,'md-8') }}		
		{{ Form::bsTextField('Skoring Tabel','skoring_tabel','',true,'md-8') }}		
		<div class="mb-3">
			<label class="form-label">Isi Template  <star>*</star> </label>
			<div  id="isi_tambah"></div>
			<textarea style="display: none;" name="isi" id="isi"  required="required"></textarea>
		</div>
		<div class="mb-3">
			<label class="form-label">Salam Pembuka <star>*</star> </label>
			<div  id="salam_pembuka_tambah"></div>
			<textarea style="display: none;" name="salam_pembuka" id="salam_pembuka"  required="required"></textarea>
		</div>
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif

@if(ucu())
<!-- MODAL FORM EDIT -->
{{ Form::bsOpen('form-edit',url($main_path."/update")) }}
	{{Html::mOpenLG('modal-edit','Edit Petunjuk')}}
		{{ Form::bsTextField('Nama Template','nama_template_saran','',true,'md-8') }}		
		{{ Form::bsTextField('Skoring Tabel','skoring_tabel','',true,'md-8') }}		
		<div class="mb-3">
			<label class="form-label">Isi Template  <star>*</star> </label>
			<div  id="isi_edit"></div>
			<textarea style="display: none;" name="isi" id="isi"  required="required"></textarea>
		</div>
		<div class="mb-3">
			<label class="form-label">Salam Pembuka  <star>*</star> </label>
			<div  id="salam_pembuka_edit"></div>
			<textarea style="display: none;" name="salam_pembuka" id="salam_pembuka"  required="required"></textarea>
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

@endsection

@section("js")
<script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script type="text/javascript">
	var toolbarOptions = [['bold', 'italic'], ['link'],[{'list': 'bullet'}]];
	var $tabel1 = $('#datatable').DataTable({
		    processing: true,
		    responsive: true,
		    fixedHeader: true,
		    serverSide: true,
		    ajax: "{{url('template-saran-rekom/dt')}}",
		    "iDisplayLength": 25,
		    columns: [
		    	 {data:'DT_Row_Index' , orderable:false, searchable: false,sClass:""},
		         {data:'nama_template_saran' , name:"nama_template_saran" , orderable:false, searchable: false,sClass:""},		        
		         {data:'skoring_tabel' , name:"skoring_tabel" , orderable:false, searchable: false,sClass:""},		
		         {data:'action' , orderable:false, searchable: false,sClass:"text-center"},
		        ],
		        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
		        $(nRow).addClass( aData["rowClass"] );
		        return nRow;
		    },
		    "drawCallback": function( settings ) {
				initTooltips();
		        initKonfirmDelete();
		    }
		});

		$tabel1.on( 'responsive-display', function ( e, datatable, columns ) {
		    initKonfirmDelete();;
		});


		@if(ucc())

		var quill_isi_tambah = new Quill('#isi_tambah', {
			    placeholder: 'Tulis Isi Template.....',
			    theme: 'snow',
			    modules: {
				    toolbar: toolbarOptions
				  }
			  });

		 quill_isi_tambah.on('text-change', function(delta, oldDelta, source) {
		 		$("#form-tambah #isi").val($("#isi_tambah .ql-editor").html());
		 });

		 var quill_salam_pembuka_tambah = new Quill('#salam_pembuka_tambah', {
			    placeholder: 'Tulis Isi Pembuka.....',
			    theme: 'snow',
			    modules: {
				    toolbar: toolbarOptions
				  }
			  });
		
		 quill_salam_pembuka_tambah.on('text-change', function(delta, oldDelta, source) {
		 		$("#form-tambah #salam_pembuka").val($("#salam_pembuka_tambah .ql-editor").html());
		 });

		$validator_form_tambah = $("#form-tambah").validate();
		$("#modal-tambah").on('show.bs.modal', function(e){
			$validator_form_tambah.resetForm();
			$("#form-tambah").clearForm();
			$("#isi_tambah .ql-editor").html('');
			$("#form-tambah #isi").val('');
			$("#salam_pembuka_tambah .ql-editor").html('');
			$("#form-tambah #salam_pembuka").val('');
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

		var quill_isi_edit = new Quill('#isi_edit', {
		    placeholder: 'Tulis Isi Template.....',
		    theme: 'snow',
		    modules: {
			    toolbar: toolbarOptions
			  }
		  });

		var quill_salam_pembuka_edit = new Quill('#salam_pembuka_edit', {
		    placeholder: 'Tulis Isi Salam Pembuka.....',
		    theme: 'snow',
		    modules: {
			    toolbar: toolbarOptions
			  }
		  });
			 quill_isi_edit.on('text-change', function(delta, oldDelta, source) {
			 		$("#form-edit #isi").val($("#isi_edit .ql-editor").html());
			 });
			 quill_salam_pembuka_edit.on('text-change', function(delta, oldDelta, source) {
			 		$("#form-edit #salam_pembuka").val($("#salam_pembuka_edit .ql-editor").html());
			 });
		$validator_form_edit = $("#form-edit").validate();
		$("#modal-edit").on('show.bs.modal', function(e){
			$uuid  = $(e.relatedTarget).data('uuid');
			$validator_form_edit.resetForm();
			$("#form-edit").clearForm();
			disableButton("#form-edit button[type=submit]");
			$("#isi_edit .ql-editor").html('');
			$("#salam_pembuka_edit .ql-editor").html('');
			$.get("{{url('template-saran-rekom/get-data')}}/"+$uuid, function(respon){
				if(respon.status){
					quill_isi_edit.clipboard.dangerouslyPasteHTML(respon.data.isi);
					quill_salam_pembuka_edit.clipboard.dangerouslyPasteHTML(respon.data.salam_pembuka);
					$('#form-edit #nama_template_saran').val(respon.data.nama_template_saran);
					$('#form-edit #skoring_tabel').val(respon.data.skoring_tabel);
					$('#form-edit #isi').val(respon.data.isi);
					$('#form-edit #salam_pembuka').val(respon.data.salam_pembuka);
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
				 
				$.get("{{url('template-saran-rekom/get-data')}}/"+$uuid, function(respon){
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