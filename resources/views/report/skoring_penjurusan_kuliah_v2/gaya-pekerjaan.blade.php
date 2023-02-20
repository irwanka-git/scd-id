<?php 
loadHelper('skoring,function');
$id_user = $data_skoring->id_user;
$id_quiz = $data_skoring->id_quiz;

$list_klasifikasi= DB::table('ref_klasifikasi_gaya_kerja')->select('akronim')->get();
$gaya_pekerjaan = DB::select("SELECT A
    .ID,
    A.rangking,
    C.nama_komponen,
    C.kode,
    C.cetak_komponen,
    A.skor,
    A.klasifikasi 
FROM
    ref_skoring_gaya_pekerjaan AS A,
    ref_komponen_gaya_pekerjaan AS C 
WHERE
    C.kode = A.kode 
    AND A.id_quiz = $id_quiz 
    AND A.id_user = $id_user 
ORDER BY
    A.rangking");

?>
<table class="table table-striped table-sm table-bordered table-x">
    <thead>
        <tr class="table-primary">
            <th colspan="10" class="text-center sm">POLA GAYA PEKERJAAN</th>
        </tr>
        <tr class="table-primary">
            <th width="10%" rowspan="2" class="text-center sm">RANGKING</th>
            <th width="35%" rowspan="2" class="text-center sm">GAYA PEKERJAAN</th>
            <th width="10%" rowspan="2" class="text-center sm">SKOR</th>
            <th colspan="7" class="text-center sm">KUALIFIKASI</th>
        </tr>
        <tr>
            <th width="5%" class="text-center sm">SR</th>
            <th width="5%" class="text-center sm">RD</th>
            <th width="5%" class="text-center sm">AR</th>
            <th width="5%" class="text-center sm">SD</th>
            <th width="5%" class="text-center sm">AT</th>
            <th width="5%" class="text-center sm">TG</th>
            <th width="5%" class="text-center sm">ST</th>
        </tr>
    </thead>
    <tbody>
        @foreach($gaya_pekerjaan as $r)
         <tr>
             <td align="center" class="text-center sm">@if($r->rangking<=3) <b> @endif 
                {{$r->rangking}} 
                @if($r->rangking<=3) </b> @endif</td>
             <td class="sm">@if($r->rangking<=3) <b> @endif 
                {!! $r->cetak_komponen !!} 
                @if($r->rangking<=3) </b> @endif</td>
             <td align="center" class="text-center sm">@if($r->rangking<=3) <b> @endif 
                {{$r->skor}} 
                @if($r->rangking<=3) </b> @endif</td>
             <td align="center" class="text-center sm">
                 @if($r->klasifikasi =="SR") <i class="fas fa-check"></i> @endif 
             </td>
             <td align="center" class="text-center sm">
                 @if($r->klasifikasi =="RD") <i class="fas fa-check"></i> @endif 
             </td>
             <td align="center" class="text-center sm">
                 @if($r->klasifikasi =="AR") <i class="fas fa-check"></i> @endif 
             </td>
             <td align="center" class="text-center sm">
                 @if($r->klasifikasi =="SD") <i class="fas fa-check"></i> @endif 
             </td>
             <td align="center" class="text-center sm">
                 @if($r->klasifikasi =="AT") <i class="fas fa-check"></i> @endif 
             </td>
             <td align="center" class="text-center sm">
                 @if($r->klasifikasi =="TG") <i class="fas fa-check"></i> @endif 
             </td>
             <td align="center" class="text-center sm">
                 @if($r->klasifikasi =="ST") <i class="fas fa-check"></i> @endif 
             </td>
         </tr>
        @endforeach
        <tr>
            <td colspan="10" class="jf">
                <p style="text-align: justify !important;"><b>KETERANGAN</b><br>
                Adalah urutan dari 12 Pola Gaya Kerja, 3 gaya pertama adalah yang dominan, penjelasan lebih lanjut dapat dilihat pada bagian deskripsi. Semakin tinggi skor gaya pekerjaan, semakin tinggi pula urutan pola gaya pekerjaan (paling dominan).
                </p>
            </td>
        </tr>
    </tbody>
</table>
