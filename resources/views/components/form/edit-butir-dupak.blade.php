{{Form::bsHidden('uuid_butir', $butir->uuid)}}
<?php 
list($usec, $sec) = explode(" ", microtime());
$time = ((float)$usec + (float)$sec);
$time = str_replace(".", "-", $time);
$randomID = $time;
?>
{{Form::bsHidden('id_butir', $butir->id_butir)}}

<div class="form-group">
	<div class="col-md-3 text-right">
		Kode Butir
	</div>
	<div class="col-md-8">
		<input type="text" readonly value="{{$butir->kd_butir}}" class="form-control">
	</div>
</div>
<div class="form-group">
	<div class="col-md-3 text-right">
		Nama Butir
	</div>
	<div class="col-md-8">
		<textarea class="form-control" readonly>{{$butir->nm_butir}}</textarea>
	</div>
</div>
<hr>
<div class="form-group">
	<div class="col-md-3 text-right">
		Satuan Hasil
	</div>
	<div class="col-md-8">
		<input type="text" readonly name="satuan_hasil" id="satuan_hasil" value="{{$butir->satuan_hasil}}" required="required" class="form-control">
	</div>
</div>
@if($butir->penilaian==1 && $butir->multi==0)
<div class="form-group">
	<div class="col-md-3 text-right">
		Angka Kredit
	</div>
	<div class="col-md-4">
		<input type="text" readonly name="satuan" id="angka_kredit" value="{{$butir->angka_kredit}}" required="required" class="form-control">
		{{Form::bsHidden('kuantitas', 1)}}
	</div>
</div>
@endif
@if($butir->penilaian==1 && $butir->multi==1)
<div class="form-group">
	<div class="col-md-3 text-right">
		Kuantitas
	</div>
	<div class="col-md-8">
		<input type="text" name="kuantitas" value="{{$butir_dupak->kuantitas}}" id="kuantitas" placeholder="Masukan Kuantitas (Jumlah Item) Kegiatan/Butir" value="" 
		required="required" class="form-control numerik">
	</div>
</div>		
<div class="form-group">
	<div class="col-md-3 text-right">
		Satuan Angka Kredit
	</div>
	<div class="col-md-4">
		<input type="text" readonly name="satuan" id="angka_kredit" value="{{$butir->angka_kredit}}" required="required" class="form-control">
	</div>
</div>	
<div class="form-group">
	<div class="col-md-3 text-right">
		Uraian <small>Berdasarkan Kuantitas (Jumlah Item)</small>
	</div>
	<div class="col-md-8">
		<textarea class="hint2basic form-control" name="uraian">{{$butir_dupak->uraian}}</textarea>
	</div>
</div>
@endif
@if($butir->penilaian==2)
<div class="form-group">
	<div class="col-md-3 text-right">
		Angka Kredit
	</div>
	<div class="col-md-4">
		<input type="text" name="satuan" id="angka_kredit" value="{{$butir_dupak->satuan}}" required="required" class="form-control decimal">
	</div>
</div>
<div class="form-group">
	<div class="col-md-3 text-right">
		Keterangan
	</div>
	<div class="col-md-8">
		<textarea class="form-control" name="uraian">{{$butir_dupak->uraian}}</textarea>
	</div>
</div>		
@endif
<p></p>
<div class="form-group">
	<div class="col-md-3 text-right">
		Dokumen (Format Pdf)<br><small>Tautan Google Drive</small>
	</div>
	<div class="col-md-8">
		<input type="text" name="url_dokumen" id="url_dokumen" value="{{$butir_dupak->url_dokumen}}" placeholder="Masukan Link (Tautan) yang dibagikan dari Dokumen Google Drive"  class="input_url_google form-control">
	</div>
</div>
<div class="form-group">
	<div class="col-md-3 text-right">
		Drive ID
	</div>
	<div class="col-md-8">
		<input type="text" readonly="readonly" name="drive_id" id="{{$randomID}}" value="{{$butir_dupak->drive_id}}" class="form-control">
	</div>
</div>
{{Form::bsHidden('hapus_butir',0)}}
<hr>
<div class="form-group">
	<div class="col-md-3 text-right">
		
	</div>
	<div class="col-md-8">
		<button type="submit" class="btn btn-primary btn-sm"><i class="la la-save"></i> Simpan</button>
		<button type="button" id="btn-reset-butir" class="pull-right btn btn-warning btn-sm"><i class="la la-times"></i> Hapus Butir</button>
	</div>
</div>
<script type="text/javascript">
	$(function(){
		 
		 $(".hint2basic").summernote({
			  height: 130,
			  toolbar: false
		 });

 		

		 $('.input_url_google').on("change", function(){
		 		$url = $(this).val();
		 		$.get("{{url('parse_tautan_google')}}?url="+$url, function(respon){
		 			$("#{{$randomID}}").val(respon);
		 		})
		 })

		 $("#btn-reset-butir").on('click', function(){
			     bootbox.confirm({
				        message: "Anda Yakin Ingin Menghapus Angka Kredit Butir Ini?",
				        buttons: {
				            confirm: {
				                label: 'Ya, Hapus',
				                className: 'btn-success'
				            },
				            cancel: {
				                label: 'Batalkan',
				                className: 'btn-danger'
				            }
				        },
				        callback: function (result) {
				           if(result){
				           		$('#form-butir #hapus_butir').val(1000);
				           		$('#form-butir').submit();
				           }
				        }
				    });
		 })
	})
</script>