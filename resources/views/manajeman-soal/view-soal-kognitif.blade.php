
<b>{{str_replace("_"," ",$soal->bidang) }}</b>  <b> ({{$soal->paket}})</b> 
<hr>
<b>Pertanyaan Ke {{$soal->urutan}}</b>
@if($soal->isi_petunjuk!="")
<p><i>{{$soal->isi_petunjuk}}</i></p>
@endif
<div class="row">
	<div class="col-md-6">
	@if($soal->pertanyaan !="")
	<p>
		{{$soal->pertanyaan}}
		@if($soal->pertanyaan_gambar!="")
			<br><img src="{{url('gambar/'.$soal->pertanyaan_gambar)}}" class="img-fluid rounded-lg">
		@endif
	</p>
	@endif

	@if($soal->pertanyaan =="")
	<p>
		@if($soal->pertanyaan_gambar!="")
			<img src="{{url('gambar/'.$soal->pertanyaan_gambar)}}" class="img-fluid rounded-lg">
		@endif
	</p>
	@endif
	</div>

	<div class="col-md-6">
			Pilihan Jawaban:
		<ul>
			@if($soal->pilihan_a !="")
			<li>
				A. {{$soal->pilihan_a}}
				@if($soal->pilihan_a_gambar!="")
					<br><img src="{{url('gambar/'.$soal->pilihan_a_gambar)}}" class="img-fluid rounded-lg">
				@endif
			</li>
			@endif
			@if($soal->pilihan_a =="")
			<li>
				@if($soal->pilihan_a_gambar!="")
					A. <img src="{{url('gambar/'.$soal->pilihan_a_gambar)}}" class="img-fluid rounded-lg">
				@endif
			</li>
			@endif

			@if($soal->pilihan_b !="")
			<li>
				B. {{$soal->pilihan_b}}
				@if($soal->pilihan_b_gambar!="")
					<br><img src="{{url('gambar/'.$soal->pilihan_b_gambar)}}" class="img-fluid rounded-lg">
				@endif
			</li>
			@endif
			@if($soal->pilihan_b =="")
			<li>
				@if($soal->pilihan_b_gambar!="")
					B. <img src="{{url('gambar/'.$soal->pilihan_b_gambar)}}" class="img-fluid rounded-lg">
				@endif
			</li>
			@endif

			@if($soal->pilihan_c !="")
			<li>
				C. {{$soal->pilihan_c}}
				@if($soal->pilihan_c_gambar!="")
					<br><img src="{{url('gambar/'.$soal->pilihan_c_gambar)}}" class="img-fluid rounded-lg">
				@endif
			</li>
			@endif
			@if($soal->pilihan_c =="")
			<li>
				@if($soal->pilihan_c_gambar!="")
					C. <img src="{{url('gambar/'.$soal->pilihan_c_gambar)}}" class="img-fluid rounded-lg">
				@endif
			</li>
			@endif


			@if($soal->pilihan_d !="")
			<li>
				D. {{$soal->pilihan_d}}
				@if($soal->pilihan_d_gambar!="")
					<br><img src="{{url('gambar/'.$soal->pilihan_d_gambar)}}" class="img-fluid rounded-lg">
				@endif
			</li>
			@endif
			@if($soal->pilihan_d =="")
			<li>
				@if($soal->pilihan_d_gambar!="")
					D. <img src="{{url('gambar/'.$soal->pilihan_d_gambar)}}" class="img-fluid rounded-lg">
				@endif
			</li>
			@endif


			@if($soal->pilihan_e !="")
			<li>
				E. {{$soal->pilihan_e}}
				@if($soal->pilihan_e_gambar!="")
					<br><img src="{{url('gambar/'.$soal->pilihan_e_gambar)}}" class="img-fluid rounded-lg">
				@endif
			</li>
			@endif
			@if($soal->pilihan_e =="")
			<li>
				@if($soal->pilihan_e_gambar!="")
					E. <img src="{{url('gambar/'.$soal->pilihan_e_gambar)}}" class="img-fluid rounded-lg">
				@endif
			</li>
			@endif

		</ul>
	</div>
</div>