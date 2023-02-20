<?php
loadHelper('skoring,function');
?>
<table class="table table-striped table-sm table-bordered table-x">
    <thead>
        <tr class="table-primary">
            <th width="10%" class="text-center sm">URUTAN </th>
            <th width="90%" class="text-center sm">MINAT JURUSAN KULIAH</th>
        </tr>
    </thead>
    <tbody>

        <?php
        $list = DB::select("SELECT *  FROM soal_tmi");
        $data_jawaban = $data_sesi->jawaban_skoring;
        //echo $data_jawaban;
        //echo "TMI";
        $obj = json_decode($data_jawaban);
        $jawaban_tmi = $obj->SKALA_TES_MINAT_INDONESIA;
        ?>

        @foreach ($jawaban_tmi as $key => $value)
        <tr>
            <td align="center" class="text-center sm">
                 {{$value->urutan}}
            </td>
            <td class="jf">
                @foreach ($list as $l)
                    @if($l->urutan ==(int)$value->jawaban)
                    <b>{{$l->minat}}</b> <small> - {{$l->keterangan}}</small>
                    @endif
                @endforeach
            </td>
        <tr>
        @endforeach
    </tbody>
</table>