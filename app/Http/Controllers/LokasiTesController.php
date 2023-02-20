<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;

class LokasiTesController extends Controller
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
    	$pagetitle = "Lokasi Tes";
    	$smalltitle = "Data Lokasi Sesi Tes";
    	return view('manajemen-sesi.lokasi', compact('pagetitle','smalltitle'));
    }

    function datatable(Request $r){
        
        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=2){
                $keyword = strtolower($keyword);
                $filter = " and (  lower(a.nama_lokasi) like '%$keyword%'                     
                            ) ";
            }   
        }

         $sql_union = "select a.*, b.name as provinsi, c.name as kabupaten from lokasi as a , provinces as b, regencies as c 
         			where a.id_lokasi > 0 and a.kode_provinsi = b.id and a.kode_kabupaten = c.id
         			$filter  ";
         $query = DB::table(DB::raw("($sql_union) as x order by kode_kabupaten asc"))
                    ->select([
                        'id_lokasi',
                        'provinsi',
                        'kabupaten',
                        'nama_lokasi',
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
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }

    function get_list_kabupaten($kode_provinces){
    	$data =DB::table('regencies')->where('province_id', $kode_provinces)->get();
    	$respon = array('status'=>true,'data'=>$data);
        return response()->json($respon);
    }

    function get_data($uuid){
    	$data = DB::table('lokasi')->where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
            	'informasi'=>'Lokasi: '.$data->nama_lokasi);
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
                "kode_provinsi"=>trim($r->kode_provinsi),
                "kode_kabupaten"=>trim($r->kode_kabupaten),
                "nama_lokasi"=>trim($r->nama_lokasi),
	    		"uuid"=>$uuid,
            );

	    	DB::table('lokasi')->insert($record);
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
                "kode_provinsi"=>trim($r->kode_provinsi),
                "kode_kabupaten"=>trim($r->kode_kabupaten),
                "nama_lokasi"=>trim($r->nama_lokasi),
	    		"uuid"=>$uuid,
            );

	    	DB::table('lokasi')->where('uuid', $uuid)->update($record);
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
            $lokasi = DB::table('lokasi')->where('uuid', $uuid)->first();
            $ada = DB::table('quiz_sesi')->where('id_lokasi', $lokasi->id_lokasi)->count();
            if(!$ada){
                DB::table('lokasi')->where('uuid', $uuid)->delete();
                $respon = array('status'=>true,'message'=>'Data Berhasil Dihapus!');          
                return response()->json($respon);
            }
            $respon = array('status'=>false,'message'=>'Data lokasi masih digunakan');
            return response()->json($respon);
            
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
    //#######################################################################################

}
