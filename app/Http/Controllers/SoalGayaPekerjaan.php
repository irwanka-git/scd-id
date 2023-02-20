<?php

namespace App\Http\Controllers;

 
use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;

class SoalGayaPekerjaan extends Controller
{
    //
    function index(){
        $pagetitle = "Soal Skala Gaya Pekerjaan";
        $smalltitle = "Pengaturan Soal";
        return view('manajeman-soal.index-gaya-pekerjaan', compact('pagetitle','smalltitle'));
    }

    function datatable(Request $r){

        $ref_komponen = DB::table('ref_komponen_gaya_pekerjaan')->orderby('no')->get();

        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter = " and ( lower(a.nomor) like '%$keyword%'  
                            or  lower(a.deskripsi) like '%$keyword%'                       
                            ) ";
            }   
        }
         $array_select = ['nomor','deskripsi','uuid',];
         foreach($ref_komponen as $k){
            array_push($array_select, 'komponen_'.$k->no);
         }

         $sql_union = "select a.* from soal_gaya_pekerjaan as a where a.nomor > 0 $filter  ";
         $query = DB::table(DB::raw("($sql_union) as x "))
                    ->select($array_select);

         return Datatables::of($query)
            ->addColumn('action', function ($query) {
                    $edit = ""; $delete = "";
                    $action = '';
                    if($this->ucu()){
                        $edit = '<button data-bs-toggle="modal" data-uuid="'.$query->uuid.'" data-bs-target="#modal-edit" class="btn btn-outline-secondary btn-outline btn-sm" type="button"><i class="las la-pen"></i></button>';
                    }
                    if($this->ucd()){
                        $delete = '<button  data-uuid="'.$query->uuid.'" class="btn btn-outline-secondary btn-sm btn-konfirm-delete" type="button"><i class="las la-trash"></i></button>';
                    }
                    $action =  $action." ".$edit." ".$delete;
                    if ($action==""){$action='<a href="#" class="act"><i class="la la-lock"></i></a>'; }
                        return $action;
            })
            ->addIndexColumn()
            ->editColumn('deskripsi', function ($q){
                return $q->deskripsi;
            })
            ->rawColumns(['action','deskripsi'])
            ->make(true);
    }

    function submit_insert(Request $r){

        if($this->ucc()){
            loadHelper('format');
            $uuid = $this->genUUID();
            //return $pernyataan;
            $record = array(
                "nomor"=>trim($r->nomor), 
                "deskripsi"=>trim($r->deskripsi), 
                "komponen_a"=>trim($r->komponen_a), 
                "komponen_b"=>trim($r->komponen_b), 
                "komponen_c"=>trim($r->komponen_c), 
                "komponen_d"=>trim($r->komponen_d), 
                "komponen_e"=>trim($r->komponen_e), 
                "komponen_f"=>trim($r->komponen_f), 
                "komponen_g"=>trim($r->komponen_g), 
                "komponen_h"=>trim($r->komponen_h), 
                "komponen_i"=>trim($r->komponen_i), 
                "komponen_j"=>trim($r->komponen_j), 
                "komponen_k"=>trim($r->komponen_k), 
                "komponen_l"=>trim($r->komponen_l), 
                "uuid"=>$uuid);

            DB::table('soal_gaya_pekerjaan')->insert($record);
            $respon = array('status'=>true,'message'=>'Soal Berhasil Ditambahkan!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }

    }

    function view_soal($uuid){
        $soal = DB::table('ref_komponen_gaya_pekerjaan')->where('uuid', $uuid)->first();
        if($soal){
            return view('manajeman-soal.view-soal-kecerdasan-majemuk',compact('soal'));
        }
        return '<center><b>Maaf, Soal Tidak Ditemukan</b></center>';
    }

    function get_data($uuid){
        $data = DB::table('soal_gaya_pekerjaan')->where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
                'informasi'=>'Nomor : '.$data->nomor);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }

    function submit_update(Request $r){
        if($this->ucu()){
            loadHelper('format');
            $uuid = $r->uuid;
            $indikator = str_replace("<p>", "", $r->indikator);
            $indikator = str_replace("</p>", "", $indikator);
            //return $pernyataan;
            $record = array(
                "nomor"=>trim($r->nomor), 
                "deskripsi"=>trim($r->deskripsi), 
                "komponen_a"=>trim($r->komponen_a), 
                "komponen_b"=>trim($r->komponen_b), 
                "komponen_c"=>trim($r->komponen_c), 
                "komponen_d"=>trim($r->komponen_d), 
                "komponen_e"=>trim($r->komponen_e), 
                "komponen_f"=>trim($r->komponen_f), 
                "komponen_g"=>trim($r->komponen_g), 
                "komponen_h"=>trim($r->komponen_h), 
                "komponen_i"=>trim($r->komponen_i), 
                "komponen_j"=>trim($r->komponen_j), 
                "komponen_k"=>trim($r->komponen_k), 
                "komponen_l"=>trim($r->komponen_l), 
            );

            DB::table('soal_gaya_pekerjaan')->where('uuid', $uuid)->update($record);
            $respon = array('status'=>true,'message'=>'Soal Berhasil Disimpan!', 
                '_token'=>csrf_token());
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }

    function submit_delete(Request $r){
        if($this->ucd()){
            loadHelper('format');
            $uuid = $r->uuid;
            DB::table('soal_gaya_pekerjaan')->where('uuid', $uuid)->delete();
            $respon = array('status'=>true,'message'=>'Soal Berhasil Dihapus!');          
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }


}
