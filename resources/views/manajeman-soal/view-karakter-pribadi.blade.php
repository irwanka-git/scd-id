<b>Pertanyaan Ke {{$soal->urutan}}</b>
<div class="row">
	<div class="col-md-12">
	 {!! $soal->pernyataan !!}
	 <hr>
	 Nama Komponen: {{ $soal->nama_komponen }}
	 <hr>
	 Pilihan Jawaban:
	<ul>
		<li>{{$soal->pilihan_1}}</li>
		<li>{{$soal->pilihan_2}}</li>
		<li>{{$soal->pilihan_3}}</li>
		<li>{{$soal->pilihan_4}}</li>
	</ul>
	</div>
</div>