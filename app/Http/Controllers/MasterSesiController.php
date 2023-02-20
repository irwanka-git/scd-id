<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;

class MasterSesiController extends Controller
{
     
    //######################### QUIZ SESI MASTER #####################################
    // tabel: quiz_sesi_master
	// id_sesi_master		int
	// kategori				char
	// nama_sesi_ujian		char
	// metode_skoring		char
	// mode					char
	// jawaban				int
	// petunjuk_sesi		varchar

    //TABEL TERKAIT: quiz_sesi_master
    function index(){
    	$pagetitle = "Master Sesi Tes";
    	$smalltitle = "Data Master Sesi Tes";
    	return view('master-sesi.index', compact('pagetitle','smalltitle'));
    }

    function datatable(Request $r){
        
        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=2){
                $keyword = strtolower($keyword);
                $filter = " and ( lower(a.kategori) like '%$keyword%'
                				 or lower(a.nama_sesi_ujian) like '%$keyword%'                     
                            ) ";
            }   
        }

         $sql_union = "select a.* from quiz_sesi_master as a where a.id_sesi_master > 0 $filter  ";
         $query = DB::table(DB::raw("($sql_union) as x order by id_sesi_master"))
                    ->select([
                        'id_sesi_master',
                        'kategori',
                        'nama_sesi_ujian',
                        'metode_skoring',
                        'mode',
                        'jawaban',
                        'petunjuk_sesi',
                        'uuid',
                    ]);

         return Datatables::of($query)
            ->addColumn('action', function ($query) {
                    $edit = ""; $delete = "";
                    $action = '';
                    if($this->ucu()){
                        $edit = '<button data-bs-tip="tooltip" data-bs-placement="top" data-bs-original-title="Ubah Master Sesi" data-bs-toggle="modal" data-uuid="'.$query->uuid.'" data-bs-target="#modal-edit"  class="btn btn-light btn-sm" type="button"><ion-icon name="create-outline" btn></ion-icon></button>';
                    }
                    if($this->ucd()){
                        $delete = '<button <button data-bs-tip="tooltip" data-bs-placement="top" data-bs-original-title="Hapus Master Sesi"  data-uuid="'.$query->uuid.'" class="btn btn-light btn-sm btn-konfirm-delete"  
                        type="button"><ion-icon name="trash-outline" btn></ion-icon></button>';
                    }
                    $action =  $action." ".$edit." ".$delete;
                    if ($action==""){$action='<a href="#" class="act"><i class="la la-lock"></i></a>'; }
                        return  '<div class="btn-group" role="group">'.$action.'</button>';
            })
             ->editColumn('metode_skoring', function($q){
                return "<small>".$q->metode_skoring."</small>";
            })
            ->addIndexColumn()
            ->rawColumns(['action','metode_skoring'])
            ->make(true);
    }

    function get_data($uuid){
    	$data = DB::table('quiz_sesi_master')->where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
            	'informasi'=>'Informasi Sesi : Master Sesi Ke '.$data->id_sesi_master);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }

    function submit_insert(Request $r){
        if($this->ucc()){
            loadHelper('format');
	    	$uuid = $this->genUUID();
            // $petunjuk_sesi = str_replace("<p>", "", $r->petunjuk_sesi);
            // $petunjuk_sesi = str_replace("</p>", "", $petunjuk_sesi);          
            // return $petunjuk_sesi;
	    	$record = array(                                              
                "kategori"=>trim($r->kategori),
                "nama_sesi_ujian"=>trim($r->nama_sesi_ujian),
                "soal"=>trim($r->soal),
                "metode_skoring"=>trim($r->metode_skoring),
                "mode"=>trim($r->mode),
                "jawaban"=>(int)$r->jawaban, 
                "petunjuk_sesi"=>trim($r->petunjuk_sesi), 
	    		"uuid"=>$uuid,
            );

	    	DB::table('quiz_sesi_master')->insert($record);
	    	$respon = array('status'=>true,'message'=>'Data Berhasil Ditambahkan!');
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
            // $pernyataan = str_replace("<p>", "", $r->pernyataan);
            // $pernyataan = str_replace("</p>", "", $pernyataan);
            //return $pernyataan;
	    	$record = array(
                "kategori"=>trim($r->kategori),
                "nama_sesi_ujian"=>trim($r->nama_sesi_ujian),
                "soal"=>trim($r->soal),
                "metode_skoring"=>trim($r->metode_skoring),
                "mode"=>trim($r->mode),
                "jawaban"=>(int)$r->jawaban, 
                "petunjuk_sesi"=>trim($r->petunjuk_sesi), 
	    		"uuid"=>$uuid,

            );

	    	DB::table('quiz_sesi_master')->where('uuid', $uuid)->update($record);
	    	$respon = array('status'=>true,'message'=>'Data Berhasil Disimpan!');
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
            DB::table('quiz_sesi_master')->where('uuid', $uuid)->delete();
            $respon = array('status'=>true,'message'=>'Data Berhasil Dihapus!');          
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
    //#######################################################################################

}
