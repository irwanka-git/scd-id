<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;

class SoalSikapPelajaranController   extends Controller
{
     
    //######################### SOAL TEST KOGNITIF #####################################
    function index(){
    	$pagetitle = "Soal Sikap Terhadap Pelajaran";
    	$smalltitle = "Pengaturan Soal";
    	return view('manajeman-soal.index-sikap-pelajaran', compact('pagetitle','smalltitle'));
    }

    function datatable(Request $r){
        
        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter = " and ( lower(a.pelajaran) like '%$keyword%'   
                             or   lower(a.kelompok) like '%$keyword%'                     
                            ) ";
            }   
        }

         $sql_union = "select a.* from soal_sikap_pelajaran as a where a.id_soal > 0 $filter  ";
         $query = DB::table(DB::raw("($sql_union) as x "))
                    ->select([
                        'id_soal',
                        'urutan',
                        'kelompok',
                        'pelajaran',
                        'kode',
                        'field_skoring',
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
            ->editColumn('pelajaran', function($q){
                return $q->pelajaran;
            })
            ->addIndexColumn()
            ->rawColumns(['action','pelajaran'])
            ->make(true);
    }

    // <a href="#"><i class="align-middle" data-feather="edit-2"></i></a>
    //                                             <a href="#"><i class="align-middle" data-feather="trash"></i></a>


    function view_soal($uuid){
        $soal = DB::table('soal_sikap_pelajaran')->where('uuid', $uuid)->first();
        if($soal){
            return view('manajeman-soal.view-soal-sikap-pelajaran',compact('soal'));
        }
        return '<center><b>Maaf, Soal Tidak Ditemukan</b></center>';
    }

    function get_data($uuid){
    	$data = DB::table('soal_sikap_pelajaran')->where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
            	'informasi'=>'Informasi Soal : pelajaran Ke '.$data->urutan);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }

    function submit_insert(Request $r){
    	if($this->ucc()){
	    	loadHelper('format');
	    	$uuid = $this->genUUID();
            $pelajaran = str_replace("<p>", "", $r->pelajaran);
            $pelajaran = str_replace("</p>", "", $pelajaran);
            //return $pelajaran;
	    	$record = array(
                "urutan"=>(int)$r->urutan, 
                "pelajaran"=>trim($pelajaran), 
                "kelompok"=>trim($r->kelompok), 
                "kode"=>strtoupper($r->kode), 
                "field_skoring"=>strtolower($r->field_skoring), 
                "sikap_negatif1"=>trim($r->sikap_negatif1), 
                "sikap_positif1"=>trim($r->sikap_positif1),
                "sikap_negatif2"=>trim($r->sikap_negatif2), 
                "sikap_positif2"=>trim($r->sikap_positif2),
                "sikap_negatif3"=>trim($r->sikap_negatif3), 
                "sikap_positif3"=>trim($r->sikap_positif3), 
	    		"uuid"=>$uuid);

	    	DB::table('soal_sikap_pelajaran')->insert($record);
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
            $pelajaran = str_replace("<p>", "", $r->pelajaran);
            $pelajaran = str_replace("</p>", "", $pelajaran);
            //return $pelajaran;
	    	$record = array(
               "urutan"=> $r->urutan, 
                "pelajaran"=>trim($pelajaran), 
                "kelompok"=>trim($r->kelompok), 
                "kode"=>strtoupper($r->kode), 
                "field_skoring"=>strtolower($r->field_skoring), 
                "sikap_negatif1"=>trim($r->sikap_negatif1), 
                "sikap_positif1"=>trim($r->sikap_positif1),
                "sikap_negatif2"=>trim($r->sikap_negatif2), 
                "sikap_positif2"=>trim($r->sikap_positif2),
                "sikap_negatif3"=>trim($r->sikap_negatif3), 
                "sikap_positif3"=>trim($r->sikap_positif3), 
            );

	    	DB::table('soal_sikap_pelajaran')->where('uuid', $uuid)->update($record);
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
            DB::table('soal_sikap_pelajaran')->where('uuid', $uuid)->delete();
            $respon = array('status'=>true,'message'=>'Data Berhasil Dihapus!');          
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
    //#######################################################################################

}
