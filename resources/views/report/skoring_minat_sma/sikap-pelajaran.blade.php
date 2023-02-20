<?php 
loadHelper('skoring,function');
?>
<table class="table table-striped table-sm table-bordered table-x">
	<?php
	$list_klasifikasi= get_list_enum_values('ref_skala_sikap_pelajaran', 'klasifikasi');
	?>
	<thead>
		<tr class="table-primary">
			<th colspan="2" class="text-center  sm">SIKAP TERHADAP PELAJARAN</th>
			@foreach($list_klasifikasi as $l)
			<th class="text-center  sm">{{strtoupper($l->value)}}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php 
		$list = DB::table('soal_sikap_pelajaran')->orderby('urutan','asc')->get();
		$item_kelompok['ILMU_UMUM'] = 4;
		$item_kelompok['ILMU_ALAM'] = 3;
		$item_kelompok['ILMU_SOSIAL'] = 3;
		$kelompok = '';
		?>
		@foreach($list as $r)
		<tr>
			<?php 
			if($r->kelompok!=$kelompok){
				$kelompok = $r->kelompok;
			?>
				<td rowspan="{{$item_kelompok[$kelompok]}}" align="center" width="20%">{{skoring_replace_($kelompok)}}</td>
			<?php 
			}
			?>
			<td width="30%">{{$r->pelajaran}}</td>
			<?php 
			$field_skoring = $r->field_skoring;
			$klasifikasi = get_skor_predikat($data_skoring->$field_skoring,'skor','klasifikasi','ref_skala_sikap_pelajaran');
			?>
			@foreach($list_klasifikasi as $l)
			<td  align="center">@if($klasifikasi==$l->value) <i class="fas fa-check"></i> @endif</td>
			@endforeach
		</tr>
		@endforeach
	</tbody>
</table>