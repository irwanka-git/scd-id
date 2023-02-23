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
			<a href="#peminatan-smk" class="btn btn-outline-primary" data-bs-toggle="tab">MINAT SMK</a> 
			<a href="#kecerdasan-majemuk" class="btn btn-outline-primary" data-bs-toggle="tab">KECERDASAN MAJEMUK</a>
			<a href="#gaya-pekerjaan" class="btn btn-outline-primary" data-bs-toggle="tab">POLA GAYA PEKERJAAN</a>
			<a href="#minat-alam" class="btn btn-outline-primary" data-bs-toggle="tab">MINAT ILMU ALAM</a>
			<a href="#minat-sosial" class="btn btn-outline-primary" data-bs-toggle="tab">MINAT ILMU SOSIAL</a>
			<a href="#karakter-pribadi" class="btn btn-outline-primary" data-bs-toggle="tab">KARAKTERISTIK PRIBADI</a>
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
	<div class="tab-pane py-2 fade" id="peminatan-smk">
		<div class="card">
			<div class="card-body">
				@include("report.".$data_sesi->skoring_tabel.".minat-smk")
			</div>
		</div>
	</div>
	 
	<div class="tab-pane py-2 fade" id="kecerdasan-majemuk">
		<div class="card">
			<div class="card-body">
				 @include("report.".$data_sesi->skoring_tabel.".kecerdasan-majemuk")
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
	<div class="tab-pane py-2 fade" id="minat-alam">
		<div class="card">
			<div class="card-body">
				  @include("report.".$data_sesi->skoring_tabel.".minat-sains")
			</div>
		</div>
	</div>
	<div class="tab-pane py-2 fade" id="minat-sosial">
		<div class="card">
			<div class="card-body">
				  @include("report.".$data_sesi->skoring_tabel.".minat-sosial")
			</div>
		</div>
	</div>
	<div class="tab-pane py-2 fade" id="karakter-pribadi">
		<div class="card">
			<div class="card-body">
				 @include("report.".$data_sesi->skoring_tabel.".karakter-pribadi")
			</div>
		</div>
	</div>
	<div class="tab-pane py-2 fade" id="saran">
		<div class="card">
			<div class="card-body">
				<div>
					 
				</div>
				@include("report.".$data_sesi->skoring_tabel.".saran")
			</div>
		</div>
	</div>
</div>