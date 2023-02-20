@extends('report.layout')
@section('content')
<?php loadHelper('skoring,function'); 
	$cek_nomor_seri = env('APP_URL').'/vcf/'.$nomor_seri;
?>
<style type="text/css">
span.qrcode {
  position: absolute;
  right: 10px;
  top:85px;
} 
</style>
<style type="text/css">
span.rahasia {
  position: absolute;
  left: 15px;
  top:100px;
  width: 120px;
  padding-top:5px;
  padding-bottom:5px;
  font-size: 14px;
  font-weight: bold;
  text-align: center;
  border: 1px solid #454545;
  background: #dedede;
} 
</style>
<?php 
$biro = DB::table('users')->where('id', $data_sesi->id_user_biro)->first();
$lokasi = DB::table('lokasi')->where('id_lokasi', $quiz->id_lokasi)->first();
?>
<img class="kop" src="{{'/kop/'.$biro->kop_biro}}" height="80px">
	<span class="rahasia">
		 R A H A S I A
	</span>
	<span class="qrcode">
		{!! DNS2D::getBarcodeSVG($cek_nomor_seri, 'QRCODE',3,3); !!}
	</span>
	<br>
	<br>
	<br>
	<center>
		<b style="font-size: 18px;">P S I K O G R A M</b>
	</center>
	<br>
	<table width="100%">
		<tr>
			<td width="15%">ID</td>
			<td>:</td>
			<td width="35%">{{$user->username}}</td>
			<td width="15%">SLTP Asal</td>
			<td>:</td>
			<td width="35%">{{$user->organisasi}}</td>
		</tr>
		<tr>
			<td>Nama</td>
			<td>:</td>
			<td>{{$user->nama_pengguna}}</td>
			<td>Lokasi Tes</td>
			<td>:</td>
			<td>{{$lokasi->nama_lokasi}}</td>
		</tr>
		<tr>
			<td>Jenis Kelamin</td>
			<td>:</td>
			<td>{{$user->jenis_kelamin=='P'?'Perempunan':'Laki-Laki'}}</td>
			<td>Tanggal Tes</td>
			<td>:</td>
			<td>{{tgl_indo_lengkap(substr($data_sesi->submit_at,0,10))}}</td>
		</tr>
		<tr>
			<td>Kelas</td>
			<td>:</td>
			<td>{{$user->unit_organisasi}}</td>
			<td>Tujuan</td>
			<td>:</td>
			<td>Peminatan SMK</td>
		</tr>
	</table>
 	 	 <br> 
 

	@include("report.".$data_sesi->skoring_tabel.".kognitif")
	<br>
	@include("report.".$data_sesi->skoring_tabel.".minat-smk")
	<br>
	@include("report.".$data_sesi->skoring_tabel.".sikap-pelajaran")
	<p class="new-page"></p>
	@include("report.".$data_sesi->skoring_tabel.".tmi")
	<br>
	@include("report.".$data_sesi->skoring_tabel.".mbti")
	<br>
	<p class="new-page"></p>
	@include("report.".$data_sesi->skoring_tabel.".karakter-pribadi")
	<br>
	<p class="new-page"></p>
	@include("report.".$data_sesi->skoring_tabel.".saran")
	<br>
	@if($data_sesi->ttd_asesor)
	<?php 
	$cek_ttd = DB::table('gambar')->where('filename', $data_sesi->ttd_asesor)->first();
	$ttd_gambar =  '<img width="125px" src="data:image/'.$cek_ttd->type.';base64,'.$cek_ttd->image_base64. '">';
	?>
	<center>
		{{$data_sesi->kota}}, {{tgl_indo(substr($data_sesi->skoring_at,0,10))}}<br>
		Asesor,
			<br>{!! $ttd_gambar !!}<br>
		 {{$data_sesi->nama_asesor}}<br>
		 <small>SIPP: {{$data_sesi->nomor_sipp}}</small>
	</center>
  @else
  <center>
		{{$data_sesi->kota}}, {{tgl_indo(substr($data_sesi->skoring_at,0,10))}}<br>
		Asesor,<br><br><br><br>
		 {{$data_sesi->nama_asesor}}<br>
		 <small>SIPP: {{$data_sesi->nomor_sipp}}</small>
	</center>
	@endif
@endsection