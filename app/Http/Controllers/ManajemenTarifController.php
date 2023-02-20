<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;

class ManajemenTarifController extends Controller
{
     
    //######################### QUIZ SESI MASTER TEMPLATE #####################################
    // quiz_sesi_template
    // id_quiz_template int
    // nama_sesi        varchar
    // skoring_tabel    varchar
    // uuid             char

    // quiz_sesi_detil_template
    // id_quiz_sesi_template   int
    // id_quiz_template int
    // id_sesi_master  int
    // urutan  int
    // durasi  int
    // kunci_waktu int

    function index(){
    	$pagetitle = "Tarif Paket Tes";
    	$smalltitle = "Data Master Tarif Paket Tes";
    	return view('master-tarif.index', compact('pagetitle','smalltitle'));
    }

    function datatable(Request $r){
        
        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=2){
                $keyword = strtolower($keyword);
                $filter = " and (  lower(a.nama_tarif) like '%$keyword%' ) ";
            }   
        }

         $sql_union = "select  
         a.uuid, a.id_tarif, a.nama_tarif, a.kode, a.tarif
            from tarif_paket as a
                where a.id_tarif > 0 
            $filter  ";
        //return $sql_union;
         $query = DB::table(DB::raw("($sql_union) as x order by id_tarif asc"))
                    ->select([
                        'id_tarif',
                        'nama_tarif',
                        'kode',
                        'tarif',
                        'uuid',
                    ]);

         return Datatables::of($query)
            ->addColumn('action', function ($query) {
                $edit = ""; $delete = "";
                $action = '';
                if($this->ucu()){

                $edit = '<button data-bs-tip="tooltip" data-bs-placement="top" data-bs-original-title="Ubah"  data-bs-toggle="modal" data-uuid="'.$query->uuid.'" data-bs-target="#modal-edit" class="btn btn-light btn-sm" type="button"><ion-icon name="create-outline" btn></ion-icon></button>';
                }
                if($this->ucd()){
                    $delete = '<button data-bs-tip="tooltip" data-bs-placement="top" data-bs-original-title="Hapus"   data-uuid="'.$query->uuid.'" class="btn btn-light btn-sm btn-konfirm-delete" type="button"><ion-icon name="trash-outline" btn></ion-icon></button>';
                }
                $action =  $action." ".$edit." ".$delete;
                if ($action==""){return '<a href="#" class="act"><i class="la la-lock"></i></a>'; }
                return '<div class="btn-group" role="group">'.$action.'</button>';
            })
            ->editColumn('nama_tarif', function($q){
                return "<a href='".url('tarif-paket/detil/'.$q->uuid)."'>". $q->nama_tarif ."</a>";
            })
            ->editColumn('tarif', function($q){
                return number_format($q->tarif,2,",",".");
            })
            ->addIndexColumn()
            ->rawColumns(['action','nama_tarif'])
            ->make(true);
    }

    function get_data($uuid){
    	$data = DB::table('tarif_paket')->where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
            	'informasi'=>'Tarif : '.$data->nama_tarif);
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
                "nama_tarif"=>trim($r->nama_tarif),
                "tarif"=>(int)trim($r->tarif),
                "kode"=>strtoupper(trim($r->kode)), 
	    		"uuid"=>$uuid,
            );

	    	DB::table('tarif_paket')->insert($record);
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
                "nama_tarif"=>trim($r->nama_tarif),
                "tarif"=>(int)trim($r->tarif),
                "kode"=>strtoupper(trim($r->kode)),  
            );

	    	DB::table('tarif_paket')->where('uuid', $uuid)->update($record);
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
            DB::table('tarif_paket')->where('uuid', $uuid)->delete();
            $respon = array('status'=>true,'message'=>'Data Berhasil Dihapus!');          
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
    //#######################################################################################
    //DETIL

    function index_detil($uuid){
        $pagetitle = "Tarif Tes Rincian";
        $smalltitle = "Data Master Template Tes";
        $tarif = DB::table('tarif_paket')->where('uuid', $uuid)->first();
        return view('master-tarif.index-detil', compact('pagetitle','smalltitle','tarif'));
    }

    function datatable_detil($uuid){
        $tarif = DB::table('tarif_paket')->where('uuid', $uuid)->first();
        $id_tarif = $tarif->id_tarif;
        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=2){
                $keyword = strtolower($keyword);
                $filter = " and (  lower(a.nama_rincian) like '%$keyword%' ) ";
            }   
        }

         $sql_union = "select 
                            a.id_tarif_rinci,
                            a.urutan,
                            a.nama_rincian,
                            a.uuid
                        FROM
                            tarif_paket_rinci as a
                        WHERE
                            a.id_tarif = $id_tarif  $filter ";
        //return $sql_union;
         $query = DB::table(DB::raw("($sql_union) as x order by urutan"))
                    ->select([
                        'id_tarif_rinci',
                        'urutan',
                        'nama_rincian',
                        'uuid',
                    ]);

         return Datatables::of($query)
            ->addColumn('action', function ($query) {
                    $edit = ""; $delete = "";
                    $action = '';
                    if($this->ucu()){

                    $edit = '<button data-bs-tip="tooltip" data-bs-placement="top" data-bs-original-title="Ubah Data"  data-bs-toggle="modal" data-uuid="'.$query->uuid.'" data-bs-target="#modal-edit" class="btn btn-light btn-sm" type="button"><ion-icon name="create-outline" btn></ion-icon></button>';
                    }
                    if($this->ucd()){
                        $delete = '<button data-bs-tip="tooltip" data-bs-placement="top" data-bs-original-title="Hapus Data"   data-uuid="'.$query->uuid.'" class="btn btn-light btn-sm btn-konfirm-delete" type="button"><ion-icon name="trash-outline" btn></ion-icon></button>';
                    }
                    $action =  $action." ".$edit." ".$delete;
                    if ($action==""){return '<a href="#" class="act"><i class="la la-lock"></i></a>'; }
                    return '<div class="btn-group" role="group">'.$action.'</button>';
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }

    function get_data_detil($uuid){
       $data = DB::table('tarif_paket_rinci')->where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
                'informasi'=>'Nama Rincian : '.$data->nama_rincian);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);     
    }

    function submit_insert_detil(Request $r){
        if($this->ucc()){
            loadHelper('format');
            $uuid = $this->genUUID();
            
            $record = array( 
                "id_tarif"=>$r->id_tarif,                                        
                "urutan"=>(int)($r->urutan),
                "nama_rincian"=>trim($r->nama_rincian),
                "uuid"=>$uuid,
            );

            DB::table('tarif_paket_rinci')->insert($record);
            $respon = array('status'=>true,'message'=>'Data Berhasil Ditambahkan!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }

    function submit_update_detil(Request $r){
        if($this->ucu()){
            loadHelper('format');
            //$uuid = $this->genUUID();
            
            $record = array(                                                                       
                "urutan"=>(int)($r->urutan),
                "nama_rincian"=>trim($r->nama_rincian),
            );

            DB::table('tarif_paket_rinci')->where('uuid', $r->uuid)->update($record);
            $respon = array('status'=>true,'message'=>'Data Berhasil Disimpan!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }

    function submit_delete_detil(Request $r){
        if($this->ucu()){
            //loadHelper('format');
            DB::table('tarif_paket_rinci')->where('uuid', $r->uuid)->delete();
            $respon = array('status'=>true,'message'=>'Data Berhasil Dihapus!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }

}
