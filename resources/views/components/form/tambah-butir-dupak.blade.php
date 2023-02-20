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
		<input type="text" name="kuantitas" id="kuantitas" placeholder="Masukan Kuantitas (Jumlah Item) Kegiatan/Butir" value="" 
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
		<textarea class="hint2basic form-control" name="uraian"><ol><li></li></ol></textarea>
	</div>
</div>
@endif
@if($butir->penilaian==2)
<div class="form-group">
	<div class="col-md-3 text-right">
		Angka Kredit
	</div>
	<div class="col-md-4">
		<input type="text" name="satuan" id="angka_kredit" value="" required="required" class="form-control decimal">
	</div>
</div>
<div class="form-group">
	<div class="col-md-3 text-right">
		Keterangan
	</div>
	<div class="col-md-8">
		<textarea class="form-control" name="uraian"></textarea>
	</div>
</div>		
@endif
<p></p>
<div class="form-group">
	<div class="col-md-3 text-right">
		Dokumen (Format Pdf)<br><small>Tautan Google Drive</small>
	</div>
	<div class="col-md-8">
		<input type="text" name="url_dokumen" id="url_dokumen" value="" placeholder="Masukan Link (Tautan) yang dibagikan dari Dokumen Google Drive"  class="input_url_google form-control">
	</div>
</div>
<div class="form-group">
	<div class="col-md-3 text-right">
		Drive ID
	</div>
	<div class="col-md-8">
		<input type="text" readonly="readonly" name="drive_id" id="{{$randomID}}" value="" class="form-control">
	</div>
</div>
<hr>
@if($butir->id_butir==1 || $butir->id_butir==2 || $butir->id_butir==4 )
<div class="alert alert-warning">
	Butir Ini Hanya Boleh Diisi Jika Ada Perbedaan (Perubahan) Jenjang Pendidikan dengan Jenjang Pendidikan PAK Terakhir
</div>
@endif
<hr>
{{Form::bsSubmit('<i class="la la-save"></i> Simpan')}}
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
	})
</script>