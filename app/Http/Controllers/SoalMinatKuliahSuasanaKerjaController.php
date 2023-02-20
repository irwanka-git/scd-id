<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;

class SoalMinatKuliahSuasanaKerjaController  extends Controller
{
     
    //######################### SOAL TEST KOGNITIF #####################################
    function index(){
    	$pagetitle = "Pilihan Skala Suasana Kerja";
    	$smalltitle = "Pengaturan Pilihan";
    	return view('manajeman-soal.index-mk-suasana-kerja', compact('pagetitle','smalltitle'));
    }

    function datatable(Request $r){
        
        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter = " and ( lower(a.kegiatan) like '%$keyword%' 
                            or lower(a.keterangan) like '%$keyword%'                           
                            ) ";
            }   
        }

         $sql_union = "select a.* from soal_minat_kuliah_suasana_kerja as a where a.id_kegiatan > 0 $filter  ";
         $query = DB::table(DB::raw("($sql_union) as x order by nomor asc "))
                    ->select([
                        'id_kegiatan',
                        'nomor',
                        'kegiatan',
                        'keterangan',
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
            ->editColumn('kegiatan', function($q){
                return '<small>'.$q->kegiatan.'</small>';
            })
            ->editColumn('deskripsi', function($q){
                return '<small>'.$q->deskripsi.'</small>';
            })
            ->addIndexColumn()
            ->rawColumns(['action','kegiatan','deskripsi'])
            ->make(true);
    }

    // <a href="#"><i class="align-middle" data-feather="edit-2"></i></a>
    //                                             <a href="#"><i class="align-middle" data-feather="trash"></i></a>


    function view_soal($uuid){
        $soal = DB::table('soal_minat_kuliah_suasana_kerja')->where('uuid', $uuid)->first();
        if($soal){
            return view('manajeman-soal.view-mk-suasana-kerja',compact('soal'));
        }
        return '<center><b>Maaf, Soal Tidak Ditemukan</b></center>';
    }

    function get_data($uuid){
    	$data = DB::table('soal_minat_kuliah_suasana_kerja')->where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
            	'informasi'=>'Informasi : Kegiatan '.$data->nomor . ' - '.$data->keterangan);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }

    function submit_insert(Request $r){
    	if($this->ucc()){
	    	loadHelper('format');
	    	$uuid = $this->genUUID();
            $kegiatan = str_replace("<p>", "", $r->kegiatan);
            $kegiatan = str_replace("</p>", "", $kegiatan);
            //return $kegiatan;
	    	$record = array(
                "nomor"=>trim($r->nomor), 
                "kegiatan"=>trim($kegiatan), 
                "keterangan"=>trim($r->keterangan), 
                "deskripsi"=>trim($r->deskripsi), 
                "gambar"=>trim($r->gambar), 
	    		"uuid"=>$uuid);

	    	DB::table('soal_minat_kuliah_suasana_kerja')->insert($record);
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
            $kegiatan = str_replace("<p>", "", $r->kegiatan);
            $kegiatan = str_replace("</p>", "", $kegiatan);
            //return $kegiatan;
	    	$record = array(
               "nomor"=>$r->nomor, 
                "kegiatan"=>trim($kegiatan), 
                "keterangan"=>trim($r->keterangan), 
                "deskripsi"=>trim($r->deskripsi), 
                "gambar"=>trim($r->gambar) 
            );

	    	DB::table('soal_minat_kuliah_suasana_kerja')->where('uuid', $uuid)->update($record);
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
            DB::table('soal_minat_kuliah_suasana_kerja')->where('uuid', $uuid)->delete();
            $respon = array('status'=>true,'message'=>'Data Berhasil Dihapus!');          
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
    //#######################################################################################

}
