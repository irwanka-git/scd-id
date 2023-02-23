<?php 
loadHelper('skoring,function');
$list_klasifikasi= get_list_enum_values('ref_klasifikasi_minat_man', 'klasifikasi');
?>
<table class="table table-striped table-sm table-bordered table-x">
	<thead>
		<tr class="table-primary">
			<th width="10%" class="text-center sm">URUTUN<br>MINAT</th>
			<th width="25%" class="text-center sm">PEMINATAN SMK</th>
			<th width="65%" class="text-center sm">DEKRIPSI</th>
		</tr>
	</thead>
	<tbody>
		<?php
		//ref_klasifikasi_minat_sma
		$arr_minat = [];
		$list = DB::select("SELECT * FROM soal_peminatan_smk");
		$jumlah_pilihan = DB::table('quiz_sesi_mapping_smk')->where('id_quiz', $data_sesi->id_quiz)->count();
		foreach ($list as $l){
			$arr_minat[$l->nomor]['keterangan'] = $l->keterangan;
			$arr_minat[$l->nomor]['deskripsi'] = $l->deskripsi;
		}
		?>
		<?php 
		for ($n=1;$n<=$jumlah_pilihan;$n++){
			$val_minat = 'minat_'.$n;
		?>
			<tr>
				<td align="center">{{$n}}</td>
				<td align="center" class="tc">{{$arr_minat[$data_skoring->$val_minat]['keterangan']}}</td>
				<td class="tf"><small>{!! strip_tags($arr_minat[$data_skoring->$val_minat]['deskripsi'], '<b><strong>') !!}</small></td>
			</tr>
		<?php 
		}
		?>
	</tbody>
</table>