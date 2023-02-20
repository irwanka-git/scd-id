<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;

class MasterTemplateTesController extends Controller
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
    	$pagetitle = "Master Template Tes";
    	$smalltitle = "Data Master Template Tes";
    	return view('master-template.index', compact('pagetitle','smalltitle'));
    }

    function datatable_template(Request $r){
        
        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=2){
                $keyword = strtolower($keyword);
                $filter = " and (  lower(a.nama_sesi) like '%$keyword%' ) ";
            }   
        }

         $sql_union = "select a.id_quiz_template, 
            a.nama_sesi, a.skoring_tabel, a.gambar,
            a.uuid, a.jenis,
            count(b.id_sesi_master) as jumlah_sesi 
            from quiz_sesi_template as a 
            left join quiz_sesi_detil_template as b 
                on a.id_quiz_template = b.id_quiz_template
                where a.id_quiz_template > 0 
            $filter  group by a.id_quiz_template, 
            a.nama_sesi, a.skoring_tabel, 
            a.uuid ";
        //return $sql_union;
         $query = DB::table(DB::raw("($sql_union) as x order by jenis desc, id_quiz_template asc"))
                    ->select([
                        'id_quiz_template',
                        'nama_sesi',
                        'skoring_tabel',
                        'gambar',
                        'jumlah_sesi',
                        'jenis',
                        'uuid',
                    ]);

         return Datatables::of($query)
            ->addColumn('action', function ($query) {
                $edit = ""; $delete = "";
                $action = '';
                if($this->ucu()){

                $edit = '<button data-bs-tip="tooltip" data-bs-placement="top" data-bs-original-title="Ubah Jenis Tes"  data-bs-toggle="modal" data-uuid="'.$query->uuid.'" data-bs-target="#modal-edit" class="btn btn-light btn-sm" type="button"><ion-icon name="create-outline" btn></ion-icon></button>';
                }
                if($this->ucd()){
                    $delete = '<button data-bs-tip="tooltip" data-bs-placement="top" data-bs-original-title="Hapus Jenis Tes"   data-uuid="'.$query->uuid.'" class="btn btn-light btn-sm btn-konfirm-delete" type="button"><ion-icon name="trash-outline" btn></ion-icon></button>';
                }
                $action =  $action." ".$edit." ".$delete;
                if ($action==""){return '<a href="#" class="act"><i class="la la-lock"></i></a>'; }
                return '<div class="btn-group" role="group">'.$action.'</button>';
            })
            ->editColumn('gambar', function ($q) {
                if($q->gambar){

                  return '<button data-image="'.$q->gambar.'" class="btn btn-outline-secondary btn-outline btn-sm btn-view-image" type="button">'.$q->gambar.'</button>';
                }
            })
            ->editColumn('nama_sesi', function($q){
                return "<a href='".url('template-tes/detil/'.$q->uuid)."'>". $q->nama_sesi ."</a>";
            })
            ->addIndexColumn()
            ->rawColumns(['action','nama_sesi','gambar'])
            ->make(true);
    }

    function get_data($uuid){
    	$data = DB::table('quiz_sesi_template')->where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
            	'informasi'=>'Master Template : '.$data->nama_sesi);
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
                "nama_sesi"=>trim($r->nama_sesi),
                "gambar"=>trim($r->gambar),
                "jenis"=>trim($r->jenis),
                "skoring_tabel"=>trim($r->skoring_tabel),
	    		"uuid"=>$uuid,
            );

	    	DB::table('quiz_sesi_template')->insert($record);
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
                 "nama_sesi"=>trim($r->nama_sesi),
                  "gambar"=>trim($r->gambar),
                  "jenis"=>trim($r->jenis),
                "skoring_tabel"=>trim($r->skoring_tabel)
            );

	    	DB::table('quiz_sesi_template')->where('uuid', $uuid)->update($record);
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
            DB::table('quiz_sesi_template')->where('uuid', $uuid)->delete();
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
        $pagetitle = "Master Template Tes";
        $smalltitle = "Data Master Template Tes";
        $template = DB::table('quiz_sesi_template')->where('uuid', $uuid)->first();
        return view('master-template.index-detil', compact('pagetitle','smalltitle','template'));
    }

    function datatable_detil($uuid){
        $template = DB::table('quiz_sesi_template')->where('uuid', $uuid)->first();
        $id_quiz_template = $template->id_quiz_template;
        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=2){
                $keyword = strtolower($keyword);
                $filter = " and (  lower(a.nama_sesi) like '%$keyword%' ) ";
            }   
        }

         $sql_union = "select 
                            a.id_quiz_sesi_template,
                            a.urutan,
                            a.durasi,
                            a.kunci_waktu,
                            b.nama_sesi_ujian, a.uuid
                        FROM
                            quiz_sesi_detil_template AS a,
                            quiz_sesi_master AS b 
                        WHERE
                            a.id_sesi_master = b.id_sesi_master 
                            AND a.id_quiz_template =  $id_quiz_template $filter ";
        //return $sql_union;
         $query = DB::table(DB::raw("($sql_union) as x order by urutan"))
                    ->select([
                        'id_quiz_sesi_template',
                        'urutan',
                        'nama_sesi_ujian',
                        'kunci_waktu',
                        'durasi',
                        'uuid',
                    ]);

         return Datatables::of($query)
            ->addColumn('action', function ($query) {
                    $edit = ""; $delete = "";
                    $action = '';
                    if($this->ucu()){

                    $edit = '<button data-bs-tip="tooltip" data-bs-placement="top" data-bs-original-title="Ubah Data Detil"  data-bs-toggle="modal" data-uuid="'.$query->uuid.'" data-bs-target="#modal-edit" class="btn btn-light btn-sm" type="button"><ion-icon name="create-outline" btn></ion-icon></button>';
                    }
                    if($this->ucd()){
                        $delete = '<button data-bs-tip="tooltip" data-bs-placement="top" data-bs-original-title="Hapus Data Detil"   data-uuid="'.$query->uuid.'" class="btn btn-light btn-sm btn-konfirm-delete" type="button"><ion-icon name="trash-outline" btn></ion-icon></button>';
                    }
                    $action =  $action." ".$edit." ".$delete;
                    if ($action==""){return '<a href="#" class="act"><i class="la la-lock"></i></a>'; }
                    return '<div class="btn-group" role="group">'.$action.'</button>';
            })
            ->editColumn('kunci_waktu', function($q){
                if($q->kunci_waktu==0){
                    return "Fleksibel";
                }
                return "Kaku/Flat";
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }

    function get_data_detil($uuid){
       $data = DB::table('quiz_sesi_detil_template')->where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
                'informasi'=>'Sesi Urutan : '.$data->urutan);
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
                "id_quiz_template"=>$r->id_quiz_template,                                        
                "urutan"=>(int)($r->urutan),
                "durasi"=>(int)($r->durasi),
                "kunci_waktu"=>(int)($r->kunci_waktu),
                "id_sesi_master"=>(int)($r->id_sesi_master),
                "uuid"=>$uuid,
            );

            DB::table('quiz_sesi_detil_template')->insert($record);
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
                "durasi"=>(int)($r->durasi),
                "kunci_waktu"=>(int)($r->kunci_waktu),
                "id_sesi_master"=>(int)($r->id_sesi_master),
            );

            DB::table('quiz_sesi_detil_template')->where('uuid', $r->uuid)->update($record);
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
            DB::table('quiz_sesi_detil_template')->where('uuid', $r->uuid)->delete();
            $respon = array('status'=>true,'message'=>'Data Berhasil Dihapus!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }

}
