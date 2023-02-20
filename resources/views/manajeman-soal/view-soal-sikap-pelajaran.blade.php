<b>Urutan: {{$soal->urutan}}</b>
<hr>
<div class="row">

	<div class="col-md-12">
		Kelompok: {{$soal->kelompok}} <br>
		Kode: {{$soal->kode}} 
		<hr>
		<b>{!! $soal->pelajaran !!}</b> adalah pelajaran yang...
	 <hr>
	 <table width="50%">
	 	<tr>
	 		<td><i>Negatif</i></td>
	 		<td><i>Positif</i></td>
	 	</tr>
	 	<tr>
	 		<td width="30%">1. {{$soal->sikap_negatif1}}</td>
	 		<td width="20%">{{$soal->sikap_positif1}}</td>
	 	</tr>
	 	<tr>
	 		<td>2. {{$soal->sikap_negatif2}}</td>
	 		<td>{{$soal->sikap_positif2}}</td>
	 	</tr>
	 	<tr>
	 		<td>3. {{$soal->sikap_negatif3}}</td>
	 		<td>{{$soal->sikap_positif3}}</td>
	 	</tr>
	 </table>
	</div>
</div>