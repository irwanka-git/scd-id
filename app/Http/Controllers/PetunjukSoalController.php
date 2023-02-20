<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;

class PetunjukSoalController extends Controller
{
     
    //######################### PETUNJUK SOAL #####################################
    function index(){
    	$pagetitle = "Petunjuk Soal";
    	$smalltitle = "Pengaturan Petunjuk Soal";
    	return view('manajeman-soal.petunjuk', compact('pagetitle','smalltitle'));
    }

    function datatable(Request $r){
        
        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter = " and (lower(a.isi_petunjuk) like '%$keyword%') ";
            }   
        }

         $sql_union = "select a.isi_petunjuk , a.id_petunjuk, a.uuid  
         	from petunjuk_soal as a where  a.id_petunjuk > 0  $filter 
         	order by a.id_petunjuk  ";

         $query = DB::table(DB::raw("($sql_union) as x"))
                    ->select(['isi_petunjuk','id_petunjuk','uuid']);

         return Datatables::of($query)
            ->addColumn('action', function ($query) {
                    $edit = ""; $delete = "";
                    if($this->ucu()){
                        $edit = '<button data-bs-toggle="modal" data-uuid="'.$query->uuid.'" data-bs-target="#modal-edit" class="btn btn-outline-secondary btn-outline btn-sm" type="button"><i class="las la-pen"></i></button>';
                    }
                    if($this->ucd()){
                        $delete = '<button  data-uuid="'.$query->uuid.'" class="btn btn-outline-secondary btn-sm btn-konfirm-delete" type="button"><i class="las la-trash"></i></button>';
                    }
                    $action =  $edit." ".$delete;
                    if ($action==""){$action='<a href="#" class="act"><i class="la la-lock"></i></a>'; }
                        return $action;
            })
            ->editColumn('isi_petunjuk', function($q){
                return substr($q->isi_petunjuk, 0, 120);
            })
            ->addIndexColumn()
            ->rawColumns(['action','label'])
            ->make(true);
    }

    // <a href="#"><i class="align-middle" data-feather="edit-2"></i></a>
    //                                             <a href="#"><i class="align-middle" data-feather="trash"></i></a>

    function get_data($uuid){
    	$data = DB::table('petunjuk_soal')->where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
            	'informasi'=>'Petunjuk : '. $data->isi_petunjuk);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }

    function submit_insert(Request $r){
    	if($this->ucc()){
	    	loadHelper('format');
	    	$uuid = $this->genUUID();

	    	$record = array(
	    		"isi_petunjuk"=>$r->isi_petunjuk, 
	    		"uuid"=>$uuid);

	    	DB::table('petunjuk_soal')->insert($record);
	    	$respon = array('status'=>true,'message'=>'Petunjuk Soal Berhasil Ditambahkan!', '_token'=>csrf_token());
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

	    	$record = array(
	    		"isi_petunjuk"=>$r->isi_petunjuk, 
	    	);

	    	DB::table('petunjuk_soal')->where('uuid', $uuid)->update($record);
	    	$respon = array('status'=>true,'message'=>'Petunjuk Soal Berhasil Disimpan!', 
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
            DB::table('petunjuk_soal')->where('uuid', $uuid)->delete();
            $respon = array('status'=>true,'message'=>'Data Berhasil Dihapus!');          
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
    //#######################################################################################

}
