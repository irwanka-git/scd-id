<?php 
loadHelper('skoring,function');

?>
<?php
    $jumlah_pilihan = 5;
    $list_kecerdasan = DB::select("SELECT *  FROM ref_kecerdasan_majemuk");
    $kecerdasan = [];
    foreach($list_kecerdasan as $s){
        $kecerdasan[$s->no]['nama_kecil'] = $s->nama_kecil;
        $kecerdasan[$s->no]['nama'] = $s->nama_kecerdasan;
    }
?>

<table class="table table-striped table-sm table-bordered table-x">
    <thead>
        <tr class="table-primary">
            <th colspan="2" class="text-center sm">SKALA KECERDASAN MAJEMUK</th>
        </tr>
        <tr class="table-primary">
            <th width="10%" class="text-center sm">URUTAN</th>
            <th width="90%" class="text-center sm">KECERDASAN MAJEMUK</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        for ($i=1;$i<=$jumlah_pilihan;$i++){
            $field_name = 'km_'.$i;
            $nomor = $data_skoring->$field_name;
        ?>
        <tr>
            <td align="center" class="text-center sm">
                 {{$i}}
            </td>
            <td class="jf">
                <b>{{$kecerdasan[$nomor]['nama_kecil']}}</b> <small> - {{$kecerdasan[$nomor]['nama']}}</small>
            </td>
        <tr>
        <?php
        }
        ?>
    </tbody>
</table>
