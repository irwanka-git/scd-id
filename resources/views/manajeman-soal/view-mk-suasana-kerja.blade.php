<b>Nomor: {{$soal->nomor}}</b>
<hr>
<div class="row">
	<div class="col-md-12">
		Kegiatan:<br>
	 {!! $soal->kegiatan !!}
	 <hr>
	 Keterangan: <br><b>{!! $soal->keterangan !!}</b>
	 <hr>
	 Gambar:<br>
	 <img src="{{url('gambar/'.$soal->gambar)}}" class="img-fluid rounded-lg">
	</div>
</div>