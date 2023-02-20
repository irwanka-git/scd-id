
<?php
$id_quiz = $data_sesi->id_quiz;
$id_user = $data_sesi->id_user;
$skoring = DB::select("select 
                        a.no, 
                        a.akronim,
                        b.b1,b.b2, b.b3, b.b4, b.b5, b.b6, b.b7, b.b8, b.b9,
                        b.total,
                        b.rangking
                        from 
                        ref_sekolah_dinas as a, 
                        ref_skoring_kuliah_dinas as b 
                        where a.no = b.no and b.id_quiz = $id_quiz and b.id_user = $id_user order by b.rangking asc");
?>
<table class="table table-striped table-sm table-bordered table-x">
    <thead>
        <tr class="table-primary">
            <th colspan="12" class="text-center sm">SKORING SEKOLAH KEDINASAN</th>
        </tr>
        <tr class="table-primary">
            <th width="30%" class="text-center sm">NOMOR/SEKOLAH</th>
            <th width="5%" class="text-center sm">I</th>
            <th width="5%" class="text-center sm">II</th>
            <th width="5%" class="text-center sm">III</th>
            <th width="5%" class="text-center sm">IV</th>
            <th width="5%" class="text-center sm">V</th>
            <th width="5%" class="text-center sm">VI</th>
            <th width="5%" class="text-center sm">VII</th>
            <th width="5%" class="text-center sm">VIII</th>
            <th width="5%" class="text-center sm">IX</th>
            <th width="12%" class="text-center sm">Total</th>
            <th width="13%" class="text-center sm">Rangking</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sumb1= 0; $sumb2= 0; $sumb3= 0; $sumb4= 0; $sumb5= 0; $sumb6= 0; $sumb7= 0; $sumb8= 0; $sumb9= 0;
        $sumtotal = 0;
        ?>
        @foreach($skoring as $s)
        <?php
            $sumb1 += $s->b1;  
            $sumb2 += $s->b2;  
            $sumb3 += $s->b3;  
            $sumb4 += $s->b4;  
            $sumb5 += $s->b5;  
            $sumb6 += $s->b6;  
            $sumb7 += $s->b7;  
            $sumb8 += $s->b8;  
            $sumb9 += $s->b9;  
            $sumtotal += $s->total;
        ?>
        <tr>
            <td class="sm">
                 {{$s->no}}. {{$s->akronim}}
            </td>
            <td class="sm text-center">{{$s->b1}}</td>
            <td class="sm text-center">{{$s->b2}}</td>
            <td class="sm text-center">{{$s->b3}}</td>
            <td class="sm text-center">{{$s->b4}}</td>
            <td class="sm text-center">{{$s->b5}}</td>
            <td class="sm text-center">{{$s->b6}}</td>
            <td class="sm text-center">{{$s->b7}}</td>
            <td class="sm text-center">{{$s->b8}}</td>
            <td class="sm text-center">{{$s->b9}}</td>
            <td class="sm text-center">{{$s->total}}</td>
            <td class="sm text-center">{{$s->rangking}}</td>
        <tr>
        @endforeach
        <tr>
            <td class="sm text-right">
                 <b>TOTAL</b>
            </td>
            <td class="sm text-center"><b>{{$sumb1}}</b></td>
            <td class="sm text-center"><b>{{$sumb2}}</b></td>
            <td class="sm text-center"><b>{{$sumb3}}</b></td>
            <td class="sm text-center"><b>{{$sumb4}}</b></td>
            <td class="sm text-center"><b>{{$sumb5}}</b></td>
            <td class="sm text-center"><b>{{$sumb6}}</b></td>
            <td class="sm text-center"><b>{{$sumb7}}</b></td>
            <td class="sm text-center"><b>{{$sumb8}}</b></td>
            <td class="sm text-center"><b>{{$sumb9}}</b></td>
            <td class="sm text-center"><b>{{$sumtotal}}</b></td>
            <td class="sm text-center"></td>
        <tr>
    </tbody>
</table>