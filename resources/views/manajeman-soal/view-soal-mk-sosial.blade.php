<b>Nomor: {{$soal->urutan}}</b>
<br>Minat: <b>{{$soal->minat}}</b> | Jurusan: <b>{{$soal->jurusan}}</b>
<hr>
<div class="row">
	<div class="col-md-12">
		Indikator:<br>
	 {!! $soal->indikator !!}
	 <hr>
	 Deskripsi Minat: <br><b>{{$soal->deskripsi_minat}}</b>
	 
	 <br>Deskripsi Jurusan: <br><b>{{$soal->deskripsi_jurusan}}</b>
	 <br>Matakuliah: <br><b>{{$soal->matakuliah}}</b>
	 <br>Peluang Karier: <br><b>{{$soal->peluang_karier}}</b>
	 <br>Tersedia Di: <br><b>{{$soal->tersedia_di}}</b>
	  
</div>