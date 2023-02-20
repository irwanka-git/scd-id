<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;

class SoalMinatKuliahAgamaController   extends Controller
{
     
        // id_soal
        // urutan
        // indikator
        // kelompok
        // minat
        // deskripsi_minat
        // jurusan
        // deskripsi_jurusan
        // matakuliah
        // peluang_karier
        // tersedia_di
        // uuid

    //######################### SOAL TEST KOGNITIF #####################################
    function index(){
    	$pagetitle = "Soal Minat Kuliah Sosial";
    	$smalltitle = "Pengaturan Soal";
    	return view('manajeman-soal.index-mk-agama', compact('pagetitle','smalltitle'));
    }

    function datatable(Request $r){
        
        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter = " and ( lower(a.minat) like '%$keyword%'  
                			or  lower(a.jurusan) like '%$keyword%'                       
                            ) ";
            }   
        }

         $sql_union = "select a.* from soal_minat_kuliah_agama as a where a.id_soal > 0 $filter  ";
         $query = DB::table(DB::raw("($sql_union) as x "))
                    ->select([
                        'id_soal',
                        'urutan',
                        'jurusan',
                        'indikator',
                        'uuid',
                    ]);

         return Datatables::of($query)
            ->addColumn('action', function ($query) {
                    $edit = ""; $delete = "";
                    $action = '';
                    // '<button data-bs-toggle="modal" data-uuid="'.$query->uuid.'" data-bs-target="#modal-lihat-soal" class="btn btn-outline-secondary btn-outline btn-sm" type="button"><i class="las la-eye"></i></button>';
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
            ->editColumn('indikator', function ($q){
            	return "<small>".$q->indikator."</small>";
            })
            ->rawColumns(['action','indikator'])
            ->make(true);
    }

    // <a href="#"><i class="align-middle" data-feather="edit-2"></i></a>
    //                                             <a href="#"><i class="align-middle" data-feather="trash"></i></a>


    function view_soal($uuid){
        $soal = DB::table('soal_minat_kuliah_agama')->where('uuid', $uuid)->first();
        if($soal){
            return view('manajeman-soal.view-soal-mk-agama',compact('soal'));
        }
        return '<center><b>Maaf, Soal Tidak Ditemukan</b></center>';
    }

    function get_data($uuid){
    	$data = DB::table('soal_minat_kuliah_agama')->where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
            	'informasi'=>'Jurusan : '.$data->jurusan);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }

    function submit_insert(Request $r){
    	if($this->ucc()){
	    	loadHelper('format');
	    	$uuid = $this->genUUID();
            $indikator = str_replace("<p>", "", $r->indikator);
            $indikator = str_replace("</p>", "", $indikator);
            //return $pernyataan;
	    	$record = array(
                "urutan"=>(int)$r->urutan, 
                "indikator"=>trim($indikator), 
                "jurusan"=>trim($r->jurusan), 
	    		"uuid"=>$uuid);

	    	DB::table('soal_minat_kuliah_agama')->insert($record);
	    	$respon = array('status'=>true,'message'=>'Jurusan Berhasil Ditambahkan!');
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
                "urutan"=>(int)$r->urutan, 
                "jurusan"=>trim($r->jurusan),
                "indikator"=>trim($indikator), 
            );

	    	DB::table('soal_minat_kuliah_agama')->where('uuid', $uuid)->update($record);
	    	$respon = array('status'=>true,'message'=>'Jurusan Berhasil Disimpan!', 
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
            DB::table('soal_minat_kuliah_agama')->where('uuid', $uuid)->delete();
            $respon = array('status'=>true,'message'=>'Data Berhasil Dihapus!');          
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
    //#######################################################################################

}
