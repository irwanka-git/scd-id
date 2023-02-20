<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;

class SoalMinatKuliahDinasController   extends Controller
{
     
        // nomor
        // deskripsi
        // pernyataan_a s.d pernyataan_l
        // uuid

    //######################### SOAL TEST KOGNITIF #####################################
    function index(){
    	$pagetitle = "Soal Minat Kuliah Dinas";
    	$smalltitle = "Pengaturan Soal";
    	return view('manajeman-soal.index-mk-dinas', compact('pagetitle','smalltitle'));
    }

    function datatable(Request $r){
        
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

         $sql_union = "select a.* from soal_minat_kuliah_dinas as a where a.nomor <> '' $filter  ";
         $query = DB::table(DB::raw("($sql_union) as x "))
                    ->select([
                        'nomor',
                        'deskripsi',
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
            ->rawColumns(['action','deskripsi'])
            ->make(true);
    }

    // <a href="#"><i class="align-middle" data-feather="edit-2"></i></a>
    //                                             <a href="#"><i class="align-middle" data-feather="trash"></i></a>


    function view_soal($uuid){
        $soal = DB::table('soal_minat_kuliah_dinas')->where('uuid', $uuid)->first();
        if($soal){
            return view('manajeman-soal.view-soal-mk-dinas',compact('soal'));
        }
        return '<center><b>Maaf, Soal Tidak Ditemukan</b></center>';
    }

    function get_data($uuid){
    	$data = DB::table('soal_minat_kuliah_dinas')->where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
            	'informasi'=>'Nomor : '.$data->nomor);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }

    function submit_insert(Request $r){
    	if($this->ucc()){
	    	loadHelper('format');
	    	$uuid = $this->genUUID();
            //return $pernyataan;
	    	$record = array(
                "nomor"=>trim($r->nomor), 
                "deskripsi"=>trim($r->deskripsi), 
                "pernyataan_a"=>trim($r->pernyataan_a), 
                "pernyataan_b"=>trim($r->pernyataan_b), 
                "pernyataan_c"=>trim($r->pernyataan_c), 
                "pernyataan_d"=>trim($r->pernyataan_d), 
                "pernyataan_e"=>trim($r->pernyataan_e), 
                "pernyataan_f"=>trim($r->pernyataan_f), 
                "pernyataan_g"=>trim($r->pernyataan_g), 
                "pernyataan_h"=>trim($r->pernyataan_h), 
                "pernyataan_i"=>trim($r->pernyataan_i), 
                "pernyataan_j"=>trim($r->pernyataan_j), 
                "pernyataan_k"=>trim($r->pernyataan_k), 
                "pernyataan_l"=>trim($r->pernyataan_l), 
	    		"uuid"=>$uuid);

	    	DB::table('soal_minat_kuliah_dinas')->insert($record);
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
            $indikator = str_replace("<p>", "", $r->indikator);
            $indikator = str_replace("</p>", "", $indikator);
            //return $pernyataan;
	    	$record = array(
                "nomor"=>trim($r->nomor), 
                "deskripsi"=>trim($r->deskripsi), 
                "pernyataan_a"=>trim($r->pernyataan_a), 
                "pernyataan_b"=>trim($r->pernyataan_b), 
                "pernyataan_c"=>trim($r->pernyataan_c), 
                "pernyataan_d"=>trim($r->pernyataan_d), 
                "pernyataan_e"=>trim($r->pernyataan_e), 
                "pernyataan_f"=>trim($r->pernyataan_f), 
                "pernyataan_g"=>trim($r->pernyataan_g), 
                "pernyataan_h"=>trim($r->pernyataan_h), 
                "pernyataan_i"=>trim($r->pernyataan_i), 
                "pernyataan_j"=>trim($r->pernyataan_j), 
                "pernyataan_k"=>trim($r->pernyataan_k), 
                "pernyataan_l"=>trim($r->pernyataan_l), 
            );

	    	DB::table('soal_minat_kuliah_dinas')->where('uuid', $uuid)->update($record);
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
            DB::table('soal_minat_kuliah_dinas')->where('uuid', $uuid)->delete();
            $respon = array('status'=>true,'message'=>'Soal Berhasil Dihapus!');          
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
    //#######################################################################################

}
