<?php 
loadHelper('skoring,function');
?>
<table class="table table-striped table-sm table-bordered table-x">
	<thead>
		<tr class="table-primary">
			<th colspan="2" class="text-center sm">REKOMENDASI MINAT SUASANA KERJA</th>
		</tr>
		<tr class="table-primary">
			<th width="10%" class="text-center sm">URUTAN</th>
			<th width="90%" class="text-center sm">DESKRIPSI</th>
		</tr>
	</thead>
	<tbody>
		<?php
        $list = DB::select("SELECT *  FROM soal_minat_kuliah_suasana_kerja");
        $data_jawaban = $data_sesi->jawaban_skoring;
        //echo $data_jawaban;
        $obj = json_decode($data_jawaban);
        $jawaban_minat = $obj->SKALA_PMK_SUASANA_KERJA;
        ?>

        @foreach ($jawaban_minat as $key => $value)
        <tr>
            <td align="center" class="text-center sm">
                 {{$value->urutan}}
            </td>
            <td class="jf sm">
                @foreach ($list as $l)
                    @if(trim($l->nomor)== trim($value->jawaban))
                    <b>{{$l->keterangan}}</b> <small> - {{strip_tags($l->deskripsi)}}</small>
                    @endif
                @endforeach
            </td>
        <tr>
        @endforeach
	</tbody>
</table>