<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;

class SoalPeminatanMANController extends Controller
{
    function index(){
    	$pagetitle = "Soal Skala Peminatan MAN";
    	$smalltitle = "Pengaturan Soal";
    	return view('manajeman-soal.index-peminatan-man', compact('pagetitle','smalltitle'));
    }

    function datatable(Request $r){
        
        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter = " and ( lower(a.pernyataan) like '%$keyword%' 
                    or lower(a.pilihan_a) like '%$keyword%'
                    or lower(a.pilihan_b) like '%$keyword%'
                    or lower(a.pilihan_c) like '%$keyword%'
                    or lower(a.pilihan_d) like '%$keyword%' 
                    ) ";
            }   
        }

         $sql_union = "select a.* from soal_peminatan_man as a where a.id_soal > 0 $filter  ";
         $query = DB::table(DB::raw("($sql_union) as x "))
                    ->select([
                        'id_soal',
                        'urutan',
                        'pernyataan',
                        'uuid',
                    ]);

         return Datatables::of($query)
            ->addColumn('action', function ($query) {
                    $edit = ""; $delete = "";
                    $action = '<button data-bs-toggle="modal" data-uuid="'.$query->uuid.'" data-bs-target="#modal-lihat-soal" class="btn btn-outline-secondary btn-outline btn-sm" type="button"><i class="las la-eye"></i></button>';
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
            ->rawColumns(['action','pernyataan'])
            ->make(true);
    }

    function view_soal($uuid){
        $soal = DB::table('soal_peminatan_man')->where('uuid', $uuid)->first();
        if($soal){
            return view('manajeman-soal.view-soal-peminatan-man',compact('soal'));
        }
        return '<center><b>Maaf, Soal Tidak Ditemukan</b></center>';
    }

    function get_data($uuid){
    	$data = DB::table('soal_peminatan_man')->where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
            	'informasi'=>'Informasi Soal : Pernyataan Ke '.$data->urutan);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }


    function submit_insert(Request $r){
    	if($this->ucc()){
	    	loadHelper('format');
	    	$uuid = $this->genUUID();
            $pernyataan = str_replace("<p>", "", $r->pernyataan);
            $pernyataan = str_replace("</p>", "", $pernyataan);
            //return $pernyataan;
	    	$record = array(
                "urutan"=>(int)$r->urutan, 
                "pernyataan"=>trim($pernyataan), 
                "pilihan_a"=>trim($r->pilihan_a), 
                "pilihan_b"=>trim($r->pilihan_b), 
                "pilihan_c"=>trim($r->pilihan_c), 
                "pilihan_d"=>trim($r->pilihan_d), 
	    		"uuid"=>$uuid);

	    	DB::table('soal_peminatan_man')->insert($record);
	    	$respon = array('status'=>true,'message'=>'Soal Berhasil Ditambahkan!');
        	return response()->json($respon);
    	}else{
    		$respon = array('status'=>false,'message'=>'Akses Ditolak!');
        	return response()->json($respon);
    	}
    }

    function submit_update(Request $r){
    	if($this->ucu()){
	    	loadHelper('format');
	    	$uuid = $r->uuid;
            $pernyataan = str_replace("<p>", "", $r->pernyataan);
            $pernyataan = str_replace("</p>", "", $pernyataan);
            //return $pernyataan;
	    	$record = array(
               "urutan"=>(int)$r->urutan, 
                "pernyataan"=>trim($pernyataan), 
                "pilihan_a"=>trim($r->pilihan_a), 
                "pilihan_b"=>trim($r->pilihan_b), 
                "pilihan_c"=>trim($r->pilihan_c), 
                "pilihan_d"=>trim($r->pilihan_d), 
            );

	    	DB::table('soal_peminatan_man')->where('uuid', $uuid)->update($record);
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
            DB::table('soal_peminatan_man')->where('uuid', $uuid)->delete();
            $respon = array('status'=>true,'message'=>'Data Berhasil Dihapus!');          
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }

}
