<?php 
loadHelper('skoring,function');
?>
<table class="table table-striped table-sm table-bordered table-x">
	<thead>
		<tr class="table-primary">
			<th colspan="2" class="text-center sm">MINAT JURUSAN UIN/IAIN (ILMU AGAMA)</th>
		</tr>
		<tr class="table-primary">
			<th width="10%" class="text-center sm">URUTAN</th>
			<th width="90%" class="text-center sm">JURUSAN</th>
		</tr>
	</thead>
	<tbody>
		<?php
        $list = DB::select("SELECT *  FROM soal_minat_kuliah_agama");
        $data_jawaban = $data_sesi->jawaban_skoring;
        $obj = json_decode($data_jawaban);
        $jawaban_minat = $obj->SKALA_PMK_ILMU_AGAMA;
        ?>

        @foreach ($jawaban_minat as $key => $value)
        <tr>
            <td align="center" class="text-center sm">
                 {{$value->urutan}}
            </td>
            <td class="jf">
                @foreach ($list as $l)
                    @if($l->urutan ==(int)$value->jawaban)
                    <b>{{$l->jurusan}}</b>
                    @endif
                @endforeach
            </td>
        <tr>
        @endforeach
	</tbody>
</table>