<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;

class SoalMinatKuliahSosialController   extends Controller
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
    	return view('manajeman-soal.index-mk-sosial', compact('pagetitle','smalltitle'));
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

         $sql_union = "select a.* , b.kelompok, c.kelas from soal_minat_kuliah_sosial as a 
                        left join ref_kelompok_minat_kuliah as b on a.id_kelompok = b.id 
                        left join ref_kelas_minat_sma as c on a.id_kelas = c.id 
                        where a.id_soal > 0 $filter  ";
         $query = DB::table(DB::raw("($sql_union) as x "))
                    ->select([
                        'id_soal',
                        'urutan',
                        'kelompok',
                        'kelas',
                        'minat',
                        'deskripsi_minat',
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
            ->editColumn('deskripsi_minat', function ($q){
            	return "<small>".$q->deskripsi_minat."</small>";
            })
            ->editColumn('minat', function ($q){
                $kelompok = $q->kelompok ? $q->kelompok : '***';
                $kelas = $q->kelas ? $q->kelas : '***';
                return "<b>".$q->minat. "</b><br><small>(".$kelompok.")</small><br><small>(".$kelas.")</small>";
            })
            ->rawColumns(['action','deskripsi_minat', 'minat'])
            ->make(true);
    }

    // <a href="#"><i class="align-middle" data-feather="edit-2"></i></a>
    //                                             <a href="#"><i class="align-middle" data-feather="trash"></i></a>


    function view_soal($uuid){
        $soal = DB::table('soal_minat_kuliah_sosial')->where('uuid', $uuid)->first();
        if($soal){
            return view('manajeman-soal.view-soal-mk-sosial',compact('soal'));
        }
        return '<center><b>Maaf, Soal Tidak Ditemukan</b></center>';
    }

    function get_data($uuid){
    	$data = DB::table('soal_minat_kuliah_sosial')->where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
            	'informasi'=>'Minat : '.$data->minat);
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
                "minat"=>strtoupper(trim($r->minat)), 
                "deskripsi_minat"=>trim($r->deskripsi_minat), 
                "jurusan"=>trim($r->jurusan), 
                "deskripsi_jurusan"=>trim($r->deskripsi_jurusan), 
                "matakuliah"=>trim($r->matakuliah), 
                "peluang_karier"=>trim($r->peluang_karier), 
                "tersedia_di"=>trim($r->tersedia_di), 
	    		"uuid"=>$uuid);

	    	DB::table('soal_minat_kuliah_sosial')->insert($record);
	    	$respon = array('status'=>true,'message'=>'Minat Berhasil Ditambahkan!');
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
                "indikator"=>trim($indikator), 
                "minat"=>strtoupper(trim($r->minat)), 
                "deskripsi_minat"=>trim($r->deskripsi_minat), 
                "jurusan"=>trim($r->jurusan), 
                "deskripsi_jurusan"=>trim($r->deskripsi_jurusan), 
                "matakuliah"=>trim($r->matakuliah), 
                "peluang_karier"=>trim($r->peluang_karier), 
                "tersedia_di"=>trim($r->tersedia_di), 
                "id_kelompok"=>(int)trim($r->id_kelompok), 
                "id_kelas"=>(int)trim($r->id_kelas), 
            );

	    	DB::table('soal_minat_kuliah_sosial')->where('uuid', $uuid)->update($record);
	    	$respon = array('status'=>true,'message'=>'Minat Berhasil Disimpan!', 
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
            DB::table('soal_minat_kuliah_sosial')->where('uuid', $uuid)->delete();
            $respon = array('status'=>true,'message'=>'Data Berhasil Dihapus!');          
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
    //#######################################################################################

}
