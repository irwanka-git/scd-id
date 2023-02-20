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
			<a href="#aspek-kognitif" class="btn btn-outline-primary active" data-bs-toggle="tab">TKD</a>
			<a href="#sikap-pelajaran" class="btn btn-outline-primary" data-bs-toggle="tab">SIKAP PELAJARAN</a>
			<a href="#jurusan-sains" class="btn btn-outline-primary" data-bs-toggle="tab">ILMU SAINS</a>
			<a href="#jurusan-sosial" class="btn btn-outline-primary" data-bs-toggle="tab">ILMU SOSIAL</a>
			<a href="#jurusan-agama" class="btn btn-outline-primary" data-bs-toggle="tab">ILMU AGAMA</a>
			<a href="#jurusan-kedinasan" class="btn btn-outline-primary" data-bs-toggle="tab">KEDINASAN</a>
			<a href="#gaya-pekerjaan" class="btn btn-outline-primary" data-bs-toggle="tab">GAYA PEKERJAAN</a>
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

	<div class="tab-pane py-2 fade" id="sikap-pelajaran">
		<div class="card">
			<div class="card-body">
				@include("report.".$data_sesi->skoring_tabel.".sikap-pelajaran")
			</div>
		</div>
	</div>

	<div class="tab-pane py-2 fade" id="jurusan-sains">
		<div class="card">
			<div class="card-body">
				@include("report.".$data_sesi->skoring_tabel.".minat-sains")
			</div>
		</div>
	</div>

	<div class="tab-pane py-2 fade" id="jurusan-sosial">
		<div class="card">
			<div class="card-body">
				@include("report.".$data_sesi->skoring_tabel.".minat-sosial")
			</div>
		</div>
	</div>

	<div class="tab-pane py-2 fade" id="jurusan-agama">
		<div class="card">
			<div class="card-body">
				@include("report.".$data_sesi->skoring_tabel.".minat-agama")
			</div>
		</div>
	</div>

	<div class="tab-pane py-2 fade" id="jurusan-kedinasan">
		<div class="card">
			<div class="card-body">
				@include("report.".$data_sesi->skoring_tabel.".minat-kedinasan")
				<br>
				@include("report.".$data_sesi->skoring_tabel.".skoring-minat-kedinasan")
			</div>
		</div>
	</div>

	<div class="tab-pane py-2 fade" id="gaya-pekerjaan">
		<div class="card">
			<div class="card-body">
				@include("report.".$data_sesi->skoring_tabel.".gaya-pekerjaan")
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