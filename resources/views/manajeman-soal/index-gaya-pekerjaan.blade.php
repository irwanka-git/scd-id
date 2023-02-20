<?php
$main_path = Request::segment(1);
loadHelper('akses,function'); 
$ref_komponen = DB::table('ref_komponen_gaya_pekerjaan')->orderby('no')->get();
$list_status_komponen = array(
							['value'=>'U1', 'text'=>'Utama (1)'],
							['value'=>'U2', 'text'=>'Utama (2)'],
							['value'=>'U3', 'text'=>'Utama (3)'],
							['value'=>'U4', 'text'=>'Utama (4)'],
							['value'=>'U5', 'text'=>'Utama (5)'],

							['value'=>'T1', 'text'=>'Tambahan (1)'],
							['value'=>'T2', 'text'=>'Tambahan (2)'],
							['value'=>'T3', 'text'=>'Tambahan (3)'],
							['value'=>'T4', 'text'=>'Tambahan (4)'],
							['value'=>'T5', 'text'=>'Tambahan (5)'],

							['value'=>'C1', 'text'=>'Cadangan (1)'],
							['value'=>'C2', 'text'=>'Cadangan (2)'],
							['value'=>'C3', 'text'=>'Cadangan (3)'],
							['value'=>'C4', 'text'=>'Cadangan (4)'],
							['value'=>'C5', 'text'=>'Cadangan (5)'],
					);
$list_status_komponen = json_decode(json_encode($list_status_komponen));
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
						<h5 class="card-title">Soal Gaya Pekerjaan</h5>
						<h6 class="card-subtitle text-muted">Fitur Ini Digunakan Untuk Input Data Soal Gaya Pekerjaan </h6>
					</div>
					<div class="card-body">
						@if(ucc())
				   		{{Html::btnModal('<i class="la la-plus-circle"></i> Tambah Soal','modal-tambah','primary')}}
				   		<hr>
				   		@endif
				   		<?php $urut=1;?>
				   		Keterangan: <br>
				   		<small>
				   		@foreach($ref_komponen as $rk)
				   		 
						({{$rk->kode}}) = {{$rk->nama_komponen}}&nbsp;&nbsp;
						<?php 
						$urut++;
						?>
						@endforeach
						<br>U = Utama, T = Tambahan dan C = Cadangan
						</small>
				   		<hr>
						<table id="datatable" class="table table-striped table-bordered table-hover table-sm" style="width:100%">
							<thead>
								<tr>
									<th width="1%">No.</th>
									<th>Deskripsi</th>
									@foreach($ref_komponen as $rk)
									<th width="3%">{!! $rk->kode !!}</th>
									@endforeach
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
	{{Html::mOpenLG('modal-tambah','Tambah Soal')}}
		{{ Form::bsTextField('Nomor','nomor','',true,'md-8') }}
		{{ Form::bsTextArea('Deskripsi','deskripsi','',true,'md-8') }}
		@foreach($ref_komponen as $rk)
		{{ Form::bsSelect2(strtoupper($rk->no).'. '.$rk->nama_komponen.' ('.$rk->kode.')','komponen_'.$rk->no,$list_status_komponen,'',false,'md-8')}}
		@endforeach
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif


@if(ucu())
 <!-- MODAL FORM TAMBAH -->
{{ Form::bsOpen('form-edit',url($main_path."/update")) }}
	{{Html::mOpenLG('modal-edit','Ubah Soal')}}
		{{ Form::bsTextField('Nomor','nomor','',true,'md-8') }}
		{{ Form::bsTextArea('Deskripsi','deskripsi','',true,'md-8') }}
		@foreach($ref_komponen as $rk)
		{{ Form::bsSelect2(strtoupper($rk->no).'. '.$rk->nama_komponen.' ('.$rk->kode.')','komponen_'.$rk->no,$list_status_komponen,'',false,'md-8')}}
		@endforeach
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

 
 <style type="text/css">
	td.utama{
		background-color: #3f80ea !important;
		color: white !important;
		box-shadow: none !important;
	}

	td.tambahan{
		background-color: #fd7e14 !important;
		color: white !important;
		box-shadow: none !important;
	}

	td.cadangan{
		background-color: #adb5bd !important;
		color: white !important;
		box-shadow: none !important;
	}

</style>


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
			         {data:'deskripsi' , name:"deskripsi" , orderable:false, searchable: true,sClass:""},
			         @foreach($ref_komponen as $rk)
					 {data:'komponen_{{$rk->no}}' , name:"komponen_{{$rk->no}}" , orderable:true, searchable: true,sClass:"text-center"},
					 @endforeach
			         {data:'action' , orderable:false, searchable: false,sClass:"text-center"},
			        ],
			        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
			        $(nRow).addClass( aData["rowClass"] );
			        <?php $kolom=2;?>
			        @foreach($ref_komponen as $rk)
		        	if(aData['komponen_{{$rk->no}}']!=null){
		        		switch(aData['komponen_{{$rk->no}}'].substring(0, 1)) {
						  case "U":
						    $('td:eq({{$kolom}})', nRow).addClass('utama');
						    break;
						  case "T":
						    $('td:eq({{$kolom}})', nRow).addClass('tambahan');
						    break;
						  case "C":
						    $('td:eq({{$kolom}})', nRow).addClass('cadangan');
						    break;
						  default:
						}
		        	}else{
		        		$('td:eq({{$kolom}})', nRow).addClass('empty');
		        	}
					<?php $kolom++;?>
					@endforeach
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
				@foreach($ref_komponen as $rk)
				$('#form-tambah #komponen_{{$rk->no}}').selectize()[0].selectize.clear();
				@endforeach
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
			@foreach($ref_komponen as $rk)
			$('#form-edit #komponen_{{$rk->no}}').selectize()[0].selectize.clear();
			@endforeach
			$("#form-edit").clearForm();
			disableButton("#form-edit button[type=submit]");
			$.get("{{url($main_path.'/get-data')}}/"+$uuid, function(respon){
				if(respon.status){
					$('#form-edit #nomor').val(respon.data.nomor);
					$('#form-edit #deskripsi').val(respon.data.deskripsi);
					@foreach($ref_komponen as $rk)
					$('#form-edit #komponen_{{$rk->no}}').selectize()[0].selectize.setValue(respon.data.komponen_{{$rk->no}},false);
					@endforeach
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