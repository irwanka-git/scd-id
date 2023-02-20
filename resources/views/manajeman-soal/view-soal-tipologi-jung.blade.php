<b>Nomor: {{$soal->urutan}}</b>
<hr>
<div class="row">
	<div class="col-md-12">
		Pernyataan:<br>
	 {!! $soal->pernyataan !!}
	 <hr>
	 Pilihan A: {{$soal->pilihan_a}}<br>
	 Pilihan B: {{$soal->pilihan_b}}
	 <hr>
	 Kolom: <b>{{$soal->kolom}}</b> 
	</div>
</div>