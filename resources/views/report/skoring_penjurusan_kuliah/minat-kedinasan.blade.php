<?php 
loadHelper('skoring,function');

?>
<?php
    $jumlah_pilihan = 3;
    $list_sekolah = DB::select("SELECT *  FROM ref_sekolah_dinas");
    $sekolah = [];
    foreach($list_sekolah as $s){
        $sekolah[$s->no]['akronim'] = $s->akronim;
        $sekolah[$s->no]['nama'] = $s->nama_sekolah_dinas;
    }
?>

<table class="table table-striped table-sm table-bordered table-x">
    <thead>
        <tr class="table-primary">
            <th colspan="2" class="text-center sm">MINAT JURUSAN SEKOLAH KEDINASAN</th>
        </tr>
        <tr class="table-primary">
            <th width="10%" class="text-center sm">URUTAN</th>
            <th width="90%" class="text-center sm">SEKOLAH KEDINASAN</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        for ($i=1;$i<=$jumlah_pilihan;$i++){
            $field_name = 'minat_dinas'.$i;
            $nomor = $data_skoring->$field_name;
        ?>
        <tr>
            <td align="center" class="text-center sm">
                 {{$i}}
            </td>
            <td class="jf">
                <b>{{$sekolah[$nomor]['akronim']}}</b> <small> - {{$sekolah[$nomor]['nama']}}</small>
            </td>
        <tr>
        <?php
        }
        ?>
    </tbody>
</table>
