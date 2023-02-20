<style type="text/css">
	th.sm{
		font-size: 0.9em!important;
	}

	table.table-x  thead tr  th{
		vertical-align: middle !important;
	}

	table.table-x  tr  td{
		line-height: 1.2em !important;

	}
	table.table-x  tr td p{
		line-height: 1.35em !important;
		margin-top: 0.85rem !important;
	}
</style>
<center>
	<img src="{{url('gambar/'.$data_sesi->avatar)}}" width="48" height="48" class="rounded-circle"><br>
	<b>{{$data_sesi->nama_pengguna}}</b><br>
	<small>{{$data_sesi->organisasi}}</small>

</center>

<div class="row justify-content-center mt-3 mb-2">
	<div class="col-auto">
		<nav class="nav btn-group">
			<a href="#saran" class="btn btn-outline-primary active" data-bs-toggle="tab">INPUT SARAN / REKOM</a>
			<a href="#aspek-kognitif" class="btn btn-outline-primary" data-bs-toggle="tab">TKD</a>
			<a href="#peminatan-sma" class="btn btn-outline-primary" data-bs-toggle="tab">MINAT</a>
			<a href="#sikap-pelajaran" class="btn btn-outline-primary" data-bs-toggle="tab">SIKAP</a>
			<a href="#tmi" class="btn btn-outline-primary" data-bs-toggle="tab">TMI</a>
			<a href="#mbti" class="btn btn-outline-primary" data-bs-toggle="tab">MBTI</a>
			<a href="#karakter-pribadi" class="btn btn-outline-primary" data-bs-toggle="tab">KARAKTERISTIK PRIBADI</a>
		</nav>
	</div>
</div>
<div class="tab-content">
	<div class="tab-pane py-2 fade show active" id="saran">
		<div class="card">
			<div class="card-body">
				<div>
					@if($data_sesi->status_hasil==1)
						<button id="btn-batal-publish" class="btn btn-warning"><i class="las la-times"></i> Batalkan Publikasi</button>
					@else
						<button type="button" class="btn-save-rekom btn btn-primary"><i class="las la-save"></i> Simpan</button>&nbsp;
						<button type="button" class="btn-save-rekom-publish btn btn-primary"><i class="las la-paper-plane"></i> Simpan dan Publikasikan</button>
					@endif
					<hr>
					<table class="table table-striped table-sm table-bordered  table-x">
						<thead>
							<tr>
								<th class="text-center sm">PEMINATAN</th>
								<th class="text-center sm">SIKAP PELAJARAN</th>
								<th class="text-center sm">MINAT KULIAH</th>
								<th class="text-center sm">REKOMENDASI</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td align="center">{{str_replace("_"," ",$data_skoring->rekom_minat)}}</td>
								<td align="center">{{str_replace("_"," ",$data_skoring->rekom_sikap_pelajaran)}}</td>
								<td align="center">{{str_replace("_"," ",$data_skoring->rekom_tmi)}}</td>
								<td align="center">{{str_replace("_"," ",$data_skoring->rekom_akhir)}}</td>
							</tr>
						</tbody>
					</table>
				</div>
				<hr>
				@if($data_sesi->status_hasil==0)
				{{ Form::bsOpen('form-saran',url("manajemen-sesi/update-saran")) }}
				<div  id="quill_saran"></div>
				<div>
					
						{{Form::bsHidden('publish', 0)}}
						{{Form::bsHidden('uuid', $data_sesi->uuid)}}
						{{Form::bsHidden('saran', $data_sesi->saran)}}
				</div>
				{{ Form::bsClose()}}
				@else
				{{ Form::bsOpen('form-batal',url("manajemen-sesi/batal-publish")) }}
					{{Form::bsHidden('uuid', $data_sesi->uuid)}}
				{{ Form::bsClose()}}
				<div class="reading-ql"> 
				{!! $data_sesi->saran !!}
				</div>
				
				@endif
			</div>
		</div>
	</div>
	<div class="tab-pane py-2 fade " id="aspek-kognitif">
		<div class="card">
			<div class="card-body">
				@include("report.".$data_sesi->skoring_tabel.".kognitif")
			</div>
		</div>
	</div>
	<div class="tab-pane py-2 fade" id="peminatan-sma">
		<div class="card">
			<div class="card-body">
				@include("report.".$data_sesi->skoring_tabel.".minat-man")
			</div>
		</div>
	</div>
	<div class="tab-pane py-2 fade" id="sikap-pelajaran">
		<div class="card">
			<div class="card-body">
				@include("report.".$data_sesi->skoring_tabel.".sikap-pelajaran")
			</div>
		</div>
	</div>
	<div class="tab-pane py-2 fade" id="tmi">
		<div class="card">
			<div class="card-body">
				@include("report.".$data_sesi->skoring_tabel.".tmi")
			</div>
		</div>
	</div>
	<div class="tab-pane py-2 fade" id="mbti">
		<div class="card">
			<div class="card-body">
				@include("report.".$data_sesi->skoring_tabel.".mbti")
			</div>
		</div>
	</div>
	<div class="tab-pane py-2 fade" id="karakter-pribadi">
		<div class="card">
			<div class="card-body">
				@include("report.".$data_sesi->skoring_tabel.".karakter-pribadi")
			</div>
		</div>
	</div>
</div>
<script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script type="text/javascript">
	$(function(){
		@if($data_sesi->status_hasil==0)
			var toolbarOptions = [['bold', 'italic'], ['link'],[{'list': 'bullet'}]];
			var quill_saran = new Quill('#quill_saran', {
			    placeholder: 'Tulis Isi Informasi.....',
			    theme: 'snow',
			    modules: {
				    toolbar: toolbarOptions
				  }
			  });
			 quill_saran.on('text-change', function(delta, oldDelta, source) {
			 		$("#form-saran #saran").val($("#quill_saran .ql-editor").html());
			 });

			 $.get("{{url('manajemen-sesi/get-saran/'.$data_sesi->uuid)}}", function(respon){
				if(respon.status){
					quill_saran.clipboard.dangerouslyPasteHTML(respon.saran);
				}
			 });

			 $(".btn-save-rekom").on('click', function(){
			 	$("#form-saran #publish").val(0);
			 	$('#form-saran').submit();
			 });

			 $(".btn-save-rekom-publish").on('click', function(){
			 	$("#form-saran #publish").val(1);
			 	$('#form-saran').submit();
			 });

			 $('#form-saran').ajaxForm({
				beforeSubmit:function(){disableButton("#form-saran button[type=button]")},
				success:function($respon){
					if ($respon.status==true){
						 successNotify($respon.message);
						 $tabel_peserta.ajax.reload(null, false);
						 $("#modal-input-rekom").modal('hide');
					}else{
						errorNotify($respon.message);
					}
					enableButton("#form-saran button[type=button]")
				},
				error:function(){
					$("#form-saran button[type=button]").button('reset');
					errorNotify('Terjadi Kesalahan!');
				}
			}); 
	      @else

	      	$("#btn-batal-publish").on('click', function(){
			 	$('#form-batal').submit();
			 });
			 
			 $('#form-batal').ajaxForm({
				beforeSubmit:function(){disableButton("#form-batal button[type=submit]")},
				success:function($respon){
					if ($respon.status==true){
						 successNotify($respon.message);
						 $tabel_peserta.ajax.reload(null, false);
						 $("#modal-input-rekom").modal('hide');
					}else{
						errorNotify($respon.message);
					}
					enableButton("#form-batal button[type=submit]")
				},
				error:function(){
					$("#form-batal button[type=submit]").button('reset');
					errorNotify('Terjadi Kesalahan!');
				}
			}); 

		  @endif


	})
</script>