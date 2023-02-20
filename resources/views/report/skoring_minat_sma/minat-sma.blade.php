<?php 
loadHelper('skoring,function');
$list_klasifikasi= get_list_enum_values('ref_klasifikasi_minat_sma', 'klasifikasi');
//var_dump($list_klasifikasi);
?>
<table class="table table-striped table-sm table-bordered table-x">
	<thead>
		<tr class="table-primary">
			<th width="45%" class="text-center sm">PEMINATAN SMA</th>
			@foreach($list_klasifikasi as $l)
			<th class="text-center sm">{{(strtoupper(skoring_replace_($l->value)))}}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php
		//ref_klasifikasi_minat_sma
		$list = DB::select("SELECT * FROM ref_pilihan_minat_sma");
		//var_dump($list);
		?>
		@foreach($list as $r)
		<tr>
			<td class="jf">
				{{skoring_replace_($r->nama_pilihan)}}<br>
				<small>{{$r->keterangan}}</small>
			</td>
			<?php 
			$field_skoring = $r->field_skoring;
			//echo ($data_skoring->$field_skoring);
			$klasifikasi = get_skor_predikat($data_skoring->$field_skoring,'skor','klasifikasi','ref_klasifikasi_minat_sma');
			?>
			@foreach($list_klasifikasi as $l)
			<td  align="center">@if($klasifikasi==$l->value) <i class="fas fa-check"></i> @endif</td>
			@endforeach
		</tr>
		@endforeach
	</tbody>
</table>