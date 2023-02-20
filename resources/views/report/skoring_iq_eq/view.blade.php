<style type="text/css">
	th.sm{
		font-size: 0.9em!important;
	}

	table.table-x  thead tr  th{
		vertical-align: middle !important;
	}

	table.table-x  tr  td{
		line-height: 1.2em !important;

	}
	table.table-x  tr td p{
		line-height: 1.35em !important;
		margin-top: 0.85rem !important;
	}
</style>
<center>
	<img src="{{url('gambar/'.$data_sesi->avatar)}}" width="48" height="48" class="rounded-circle"><br>
	<b>{{$data_sesi->nama_pengguna}}</b><br>
	<small>{{$data_sesi->organisasi}}</small>

</center>

<div class="row justify-content-center mt-3 mb-2">
	<div class="col-auto">
		<nav class="nav btn-group">
			<a href="#aspek-kognitif" class="btn btn-outline-primary active" data-bs-toggle="tab">TKD (IQ)</a>
			<a href="#karakter-pribadi1" class="btn btn-outline-primary" data-bs-toggle="tab">ASPEK SIKAP KERJA (EQ)</a>
			<a href="#karakter-pribadi2" class="btn btn-outline-primary" data-bs-toggle="tab">ASPEK KEPRIBADIAN(EQ)</a>
			<a href="#saran" class="btn btn-outline-primary" data-bs-toggle="tab">SARAN / REKOM</a>
		</nav>
	</div>
</div>

<div class="tab-content">
	<div class="tab-pane py-2 fade show active" id="aspek-kognitif">
		<div class="card">
			<div class="card-body">
				@include("report.".$data_sesi->skoring_tabel.".kognitif")
			</div>
		</div>
	</div>
	<div class="tab-pane py-2 fade" id="karakter-pribadi1">
		<div class="card">
			<div class="card-body">
				@include("report.".$data_sesi->skoring_tabel.".eq-sikap-kerja")
			</div>
		</div>
	</div>

	<div class="tab-pane py-2 fade" id="karakter-pribadi2">
		<div class="card">
			<div class="card-body">
				@include("report.".$data_sesi->skoring_tabel.".eq-kepribadian")
			</div>
		</div>
	</div>
	<div class="tab-pane py-2 fade" id="saran">
		<div class="card">
			<div class="card-body">
				@include("report.".$data_sesi->skoring_tabel.".saran")
			</div>
		</div>
	</div>
</div>