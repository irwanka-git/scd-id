<b>Pertanyaan Ke {{$soal->urutan}}</b>
<div class="row">
	<div class="col-md-12">
	 {!! $soal->pernyataan !!}
	 <hr>
	 Pilihan Jawaban:
	<ul>
		<li>A. {{$soal->pilihan_a}}</li>
		<li>B. {{$soal->pilihan_b}}</li>
		<li>C. {{$soal->pilihan_c}}</li>
		<li>D. {{$soal->pilihan_d}}</li>
	</ul>
	</div>
</div>