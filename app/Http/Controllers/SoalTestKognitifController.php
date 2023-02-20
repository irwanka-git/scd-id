<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;

class SoalTestKognitifController extends Controller
{
     
    //######################### SOAL TEST KOGNITIF #####################################
    function index(){
    	$pagetitle = "Soal Test Kognitif";
    	$smalltitle = "Pengaturan Soal";
    	return view('manajeman-soal.index-kognitif', compact('pagetitle','smalltitle'));
    }



    function datatable(Request $r){
        
        $filter = "";

        $cari =(int) request('cari');

        if($cari==1){
            $paket = request('paket');
            $bidang = request('bidang');

            if($paket!=""){
                $filter.= "and a.paket = '$paket' ";
            }
            if($bidang!=""){
                $filter.= "and a.bidang = '$bidang' ";
            }
        }

        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter .= " and ( lower(a.pertanyaan) like '%$keyword%' 
                    or lower(a.bidang) like '%$keyword%' ) ";
            }   
        }

         $sql_union = "select a.* from soal_kognitif as a where a.id_soal > 0 $filter  ";
         $query = DB::table(DB::raw("($sql_union) as x order by bidang, urutan asc"))
                    ->select([
                        'id_soal',
                        'paket',
                        'bidang',
                        'urutan',
                        'pertanyaan',
                        'pertanyaan_gambar',
                        'pilihan_jawaban',
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
            ->editColumn('pertanyaan', function ($q){
                    $pertanyaan = "(".$q->urutan.") Pertanyaan" ;
                    if ($q->pertanyaan!=""){
                        $pertanyaan =   "(".$q->urutan.")  ".$q->pertanyaan;
                    }
                    if ($q->pertanyaan_gambar!="" && $q->pertanyaan==""){
                        $pertanyaan = "(".$q->urutan.") <i>Pertanyaan Gambar</i>";
                    }
                    return $pertanyaan;
            })
            ->editColumn('bidang', function($q){
                return str_replace("_"," ",$q->bidang);
            })
            ->addIndexColumn()
            ->rawColumns(['action','pertanyaan'])
            ->make(true);
    }

    // <a href="#"><i class="align-middle" data-feather="edit-2"></i></a>
    //                                             <a href="#"><i class="align-middle" data-feather="trash"></i></a>


    function view_soal($uuid){
        $soal = DB::select("SELECT a.*, b.isi_petunjuk FROM 
                soal_kognitif as a LEFT JOIN petunjuk_soal as b on  a.id_petunjuk = b.id_petunjuk
                where a.uuid = '$uuid'");
        if(count($soal)==1){
            $soal = $soal[0];
            return view('manajeman-soal.view-soal-kognitif',compact('soal'));
        }
        return '<center><b>Maaf, Soal Tidak Ditemukan</b></center>';
    }

    function get_data($uuid){
    	$data = DB::table('soal_kognitif')->where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
            	'informasi'=>'Informasi Soal : '.$data->paket.' / '. $data->bidang. ' / '. 'Pertanyaan Ke '.$data->urutan);
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
	    		"paket"=>$r->paket, 
                "bidang"=>$r->bidang, 
                "urutan"=>(int)$r->urutan, 
                "id_petunjuk"=>(int)$r->id_petunjuk, 
                "pertanyaan"=>trim($r->pertanyaan), 
                "pertanyaan_gambar"=>trim($r->pertanyaan_gambar), 
                "pilihan_a"=>trim($r->pilihan_a), 
                "pilihan_a_gambar"=>trim($r->pilihan_a_gambar), 
                "pilihan_b"=>trim($r->pilihan_b), 
                "pilihan_b_gambar"=>trim($r->pilihan_b_gambar), 
                "pilihan_c"=>trim($r->pilihan_c), 
                "pilihan_c_gambar"=>trim($r->pilihan_c_gambar), 
                "pilihan_d"=>trim($r->pilihan_d), 
                "pilihan_d_gambar"=>trim($r->pilihan_d_gambar), 
                "pilihan_e"=>trim($r->pilihan_e), 
                "pilihan_e_gambar"=>trim($r->pilihan_e_gambar), 
                "pilihan_jawaban"=>trim($r->pilihan_jawaban), 
	    		"uuid"=>$uuid);

	    	DB::table('soal_kognitif')->insert($record);
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

	    	$record = array(
                "paket"=>$r->paket, 
                "bidang"=>$r->bidang, 
                "urutan"=>(int)$r->urutan, 
                "id_petunjuk"=>(int)$r->id_petunjuk, 
                "pertanyaan"=>trim($r->pertanyaan), 
                "pertanyaan_gambar"=>trim($r->pertanyaan_gambar), 
                "pilihan_a"=>trim($r->pilihan_a), 
                "pilihan_a_gambar"=>trim($r->pilihan_a_gambar), 
                "pilihan_b"=>trim($r->pilihan_b), 
                "pilihan_b_gambar"=>trim($r->pilihan_b_gambar), 
                "pilihan_c"=>trim($r->pilihan_c), 
                "pilihan_c_gambar"=>trim($r->pilihan_c_gambar), 
                "pilihan_d"=>trim($r->pilihan_d), 
                "pilihan_d_gambar"=>trim($r->pilihan_d_gambar), 
                "pilihan_e"=>trim($r->pilihan_e), 
                "pilihan_e_gambar"=>trim($r->pilihan_e_gambar), 
                "pilihan_jawaban"=>trim($r->pilihan_jawaban)
            );

	    	DB::table('soal_kognitif')->where('uuid', $uuid)->update($record);
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
            DB::table('soal_kognitif')->where('uuid', $uuid)->delete();
            $respon = array('status'=>true,'message'=>'Data Berhasil Dihapus!');          
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
    //#######################################################################################

}
