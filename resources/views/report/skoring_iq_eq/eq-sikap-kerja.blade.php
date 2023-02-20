<?php 
loadHelper('skoring,function');
$list_klasifikasi= get_list_enum_values('ref_karakter_pribadi', 'klasifikasi'); 
$singkat_nama = array(
	"Sangat Rendah"=>"SR",
	"Rendah"=>"R",
	"Sedang"=>"S",
	"Tinggi"=>"T",
	"Sangat Tinggi"=>"ST",
	);
//dd($list_klasifikasi);
?>
<table class="table table-striped table-sm table-bordered table-x">
	<thead>
		<tr>
			<th colspan="7" class="text-center">EQ - ASPEK SIKAP KERJA</th>
		</tr>
		<tr class="table-primary">
			<th  width="5%"  class="text-center sm">NO.</th>
			<th width="70%"  class="text-center sm">KOMPONEN</th>
			@foreach($list_klasifikasi as $l)
			<th width="5%" class="text-center sm">{{strtoupper($l->value)}}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php
		$no=1;
		$komponen = DB::table('ref_komponen_eq')->where('aspek','ASPEK SIKAP KERJA')->orderby('urutan','asc')->get();
		?>
		@foreach($komponen as $k)
		<tr>
			<td align="center" class="text-center">
				<span>{{$k->urutan}}</span>
			</td>
			<td class="jf" width="25%">
				{{$k->nama_komponen}}<br>
				<small>{{$k->keterangan}}</small>
			</td>
			<?php 
			$field_skoring = $k->field_skoring;
			$klasifikasi = get_skor_predikat($data_skoring->$field_skoring,'skor','klasifikasi','ref_karakter_pribadi');
			?>
			@foreach($list_klasifikasi as $l)
			<td  align="center">@if($klasifikasi== $l->value) <i class="fas fa-check"></i> @endif</td>
			@endforeach
		</tr>
		@endforeach
		 
	</tbody>
</table>