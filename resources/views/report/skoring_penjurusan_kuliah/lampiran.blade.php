@extends('report.layout')
@section('content')
<?php loadHelper('skoring,function');?>
<?php 
//JURUSAN	DESKRIPSI	MATA KULIAH	PELUANG KARIES	TERSEDIA DI
$biro = DB::table('users')->where('id', $data_sesi->id_user_biro)->first();
?>
<img class="kop" src="{{'/kop/'.$biro->kop_biro}}" height="80px">
<br>
<br>
<center>
	<b>DESKRIPSI JURUSAN KULIAH ILMU SAINS DAN TEKONOLOGI</b>
</center>
<table class="table table-striped table-sm table-bordered table-x">
	<thead>
		<tr class="table-primary">
			<th width="16%" class="text-center sm">JURUSAN</th>
			<th width="21%" class="text-center sm">DESKRIPSI</th>
			<th width="21%" class="text-center sm">MATA KULIAH</th>
			<th width="21%" class="text-center sm">PELUANG KARIER</th>
			<th width="21%" class="text-center sm">TERSEDIA DI</th>
		</tr>
	</thead>
	<tbody>
		<?php
        $list = DB::select("SELECT *  FROM soal_minat_kuliah_eksakta");
        $data_jawaban = $data_sesi->jawaban_skoring;
        $obj = json_decode($data_jawaban);
        $jawaban_minat = $obj->SKALA_PMK_MINAT_ILMU_ALAM;
        ?>

        @foreach ($jawaban_minat as $key => $value)
        	@foreach ($list as $l)
            @if($l->urutan ==(int)$value->jawaban)
		        <tr>
		            <td align="center" class="sm">
		                <b>{{$l->jurusan}}</b>
		            </td>
		            <td class="sm jf">{{$l->deskripsi_jurusan}}</td>
		            <td class="sm  jf">{{$l->matakuliah}}</td>
		            <td class="sm  jf">{{$l->peluang_karier}}</td>
		            <td class="sm  jf">{{$l->tersedia_di}}</td>
		        <tr>
        		@endif
          @endforeach
        @endforeach
	</tbody>
</table>


<p class="new-page"></p>
<img class="kop" src="{{'/kop/'.$biro->kop_biro}}" height="80px">
<br>
<br>
<center>
	<b>DESKRIPSI JURUSAN KULIAH ILMU SOSIAL HUMANIORA</b>
</center>
<table class="table table-striped table-sm table-bordered table-x">
	<thead>
		<tr class="table-primary">
			<th width="16%" class="text-center sm">JURUSAN</th>
			<th width="21%" class="text-center sm">DESKRIPSI</th>
			<th width="21%" class="text-center sm">MATA KULIAH</th>
			<th width="21%" class="text-center sm">PELUANG KARIER</th>
			<th width="21%" class="text-center sm">TERSEDIA DI</th>
		</tr>
	</thead>
	<tbody>
		<?php
        $list = DB::select("SELECT *  FROM soal_minat_kuliah_sosial");
        $data_jawaban = $data_sesi->jawaban_skoring;
        $obj = json_decode($data_jawaban);
        $jawaban_minat = $obj->SKALA_PMK_MINAT_ILMU_SOSIAL;
        ?>

        @foreach ($jawaban_minat as $key => $value)
        	@foreach ($list as $l)
            @if($l->urutan ==(int)$value->jawaban)
		        <tr>
		            <td align="center" class="sm">
		                <b>{{$l->jurusan}}</b>
		            </td>
		            <td class="sm jf">{{$l->deskripsi_jurusan}}</td>
		            <td class="sm jf">{{$l->matakuliah}}</td>
		            <td class="sm jf">{{$l->peluang_karier}}</td>
		            <td class="sm jf">{{$l->tersedia_di}}</td>
		        <tr>
        		@endif
          @endforeach
        @endforeach
	</tbody>
</table>
@endsection
 
