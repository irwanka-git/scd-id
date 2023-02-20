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
			<a href="#peminatan-sma" class="btn btn-outline-primary" data-bs-toggle="tab">MINAT</a>
			<a href="#sikap-pelajaran" class="btn btn-outline-primary" data-bs-toggle="tab">SIKAP</a>
			<a href="#tmi" class="btn btn-outline-primary" data-bs-toggle="tab">TMI</a>
			<a href="#mbti" class="btn btn-outline-primary" data-bs-toggle="tab">MBTI</a>
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
	<div class="tab-pane py-2 fade" id="peminatan-sma">
		<div class="card">
			<div class="card-body">
				@include("report.".$data_sesi->skoring_tabel.".minat-smk")
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
	<div class="tab-pane py-2 fade" id="tmi">
		<div class="card">
			<div class="card-body">
				@include("report.".$data_sesi->skoring_tabel.".tmi")
			</div>
		</div>
	</div>
	<div class="tab-pane py-2 fade" id="mbti">
		<div class="card">
			<div class="card-body">
				@include("report.".$data_sesi->skoring_tabel.".mbti")
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
					<table class="table table-striped table-sm table-bordered  table-x">
						<thead>
							<tr class="table-primary">
								<th class="text-center sm">PEMINATAN</th>
								<th class="text-center sm">SIKAP PELAJARAN</th>
								<th class="text-center sm">MINAT KULIAH</th>
								<th class="text-center sm">REKOMENDASI</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td align="center">{{str_replace("_"," ",$data_skoring->rekom_minat)}}</td>
								<td align="center">{{str_replace("_"," ",$data_skoring->rekom_sikap_pelajaran)}}</td>
								<td align="center">{{str_replace("_"," ",$data_skoring->rekom_tmi)}}</td>
								<td align="center">{{str_replace("_"," ",$data_skoring->rekom_akhir)}}</td>
							</tr>
						</tbody>
					</table>
				</div>
				@include("report.".$data_sesi->skoring_tabel.".saran")
			</div>
		</div>
	</div>
</div>