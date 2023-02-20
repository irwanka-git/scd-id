<?php
loadHelper('skoring,function');
?>
<table class="table table-striped table-sm table-bordered table-x">
	<thead>
		<tr class="table-primary">
			<th width="100%" class="sm">TIPOLOGI KEPRIBADIAN</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			$list = DB::table('interprestasi_tipologi_jung')->where('kode',$data_skoring->tipojung_kode)->first();
		?>
            @if($list)
			<tr>

				<td>
					<strong>{{ $list->urutan }} - {{ $list->kode }} - {{ $list->nama }} -  <i>{{ $list->keterangan }}</i></strong>
	                <br>
	                <p style="text-align: justify">{{ $list->deskripsi }}</p>
	        
	                <b style="float: right">Informasi lebih lanjut bisa dilihat di internet<i> <a href="https://www.google.com/search?q={{ $list->keterangan }}" target="_blank">"{{ $list->keterangan }}"</a></i></b>
				</td>
			</tr>
			@else
			<tr>
				<td>
					{{$data_skoring->tipojung_kode }} Tidak Ditemukan
				</td>
			</tr>
			@endif
	</tbody>
</table>