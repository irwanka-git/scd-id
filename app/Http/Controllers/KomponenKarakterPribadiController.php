<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;

class KomponenKarakterPribadiController extends Controller
{
     
    //######################### Komponen SOAL #####################################
    function index(){
    	$pagetitle = "Komponen Karakteristik Pribadi";
    	$smalltitle = "Pengaturan Komponen Karakteristik Pribadi";       
    	return view('manajeman-soal.index-komponen-karakter', compact('pagetitle','smalltitle'));
    }

    function datatable(Request $r){
        
        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter = " and (lower(a.nama_komponen) like '%$keyword%') ";
            }   
        }

         $sql_union = "select a.id_komponen, a.nama_komponen , a.keterangan, a.uuid  
         	from komponen_karakteristik_pribadi as a where  a.id_komponen > 0  $filter 
         	order by a.nama_komponen  ";

         $query = DB::table(DB::raw("($sql_union) as x"))
                    ->select(['id_komponen','nama_komponen','keterangan','uuid']);

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
            ->addIndexColumn()
            ->rawColumns(['action','label'])
            ->make(true);
    }

    // <a href="#"><i class="align-middle" data-feather="edit-2"></i></a>
    //                                             <a href="#"><i class="align-middle" data-feather="trash"></i></a>

    function get_data($uuid){
    	$data = DB::table('komponen_karakteristik_pribadi')->where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
            	'informasi'=>'Komponen : '. $data->nama_komponen);
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
	    		"nama_komponen"=>$r->nama_komponen, 
	    		"keterangan"=>$r->keterangan, 
	    		"uuid"=>$uuid);

	    	DB::table('komponen_karakteristik_pribadi')->insert($record);
	    	$respon = array('status'=>true,'message'=>'Komponen Berhasil Ditambahkan!', '_token'=>csrf_token());
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
	    		"nama_komponen"=>$r->nama_komponen, 
	    		"keterangan"=>$r->keterangan, 
	    	);

	    	DB::table('komponen_karakteristik_pribadi')->where('uuid', $uuid)->update($record);
	    	$respon = array('status'=>true,'message'=>'Komponen Soal Berhasil Disimpan!', 
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
            DB::table('komponen_karakteristik_pribadi')->where('uuid', $uuid)->delete();
            $respon = array('status'=>true,'message'=>'Data Berhasil Dihapus!');          
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
    //#######################################################################################

}
