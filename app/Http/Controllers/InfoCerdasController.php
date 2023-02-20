<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;

class InfoCerdasController extends Controller
{
     
    //######################### PETUNJUK SOAL #####################################
 	// id_info		int
	// judul		varchar
	// isi			text
	// gambar		varchar
	// created_at	datetime
	// uuid			varchar


    function index(){
    	$pagetitle = "Informasi Cerdas";
    	$smalltitle = "Pengaturan Informasi Cerdas";
    	return view('info-cerdas.index', compact('pagetitle','smalltitle'));
    }

    function view_info_publik($uuid){
        return view('info-cerdas.view', compact('uuid'));
    }

    function datatable(Request $r){
        
        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter = " and (lower(a.judul) like '%$keyword%') ";
            }   
        }

         $sql_union = "select a.judul, a.url, a.uuid  , a.id_info, a.created_at
         	from info_cerdas as a where  a.id_info > 0  $filter 
         	order by a.id_info desc  ";

         $query = DB::table(DB::raw("($sql_union) as x"))
                    ->select(['judul','id_info','url','uuid','created_at']);

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
            ->editColumn('created_at', function($query){
            	loadHelper('function');
            	return tgl_indo_lengkap($query->created_at);
            	//return ($query->created_at);
            })
             ->editColumn('judul', function($query){
                //loadHelper('function');
                return $query->judul."<br><small>".$query->url."</small>";
                //return ($query->created_at);
            })
            ->addIndexColumn()
            ->rawColumns(['action','judul'])
            ->make(true);
    }

    // <a href="#"><i class="align-middle" data-feather="edit-2"></i></a>
    //                                             <a href="#"><i class="align-middle" data-feather="trash"></i></a>

    function get_data($uuid){
    	$data = DB::table('info_cerdas')->where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
            	'informasi'=>'Judul : '. $data->judul);
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
	    		"judul"=>trim($r->judul), 
                "url"=>trim($r->url), 
	    		"gambar"=>$r->gambar,
	    		"isi"=>trim($r->isi), 
	    		"created_at"=>date('Y-m-d H:i:s'), 
	    		"uuid"=>$uuid);

	    	DB::table('info_cerdas')->insert($record);
	    	$respon = array('status'=>true,'message'=>'Informasi Cerdas Berhasil Ditambahkan!', '_token'=>csrf_token());
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
	    		"judul"=>trim($r->judul), 
                "url"=>trim($r->url), 
	    		"gambar"=>$r->gambar,
	    		"isi"=>trim($r->isi)
	    		);
	    	DB::table('info_cerdas')->where('uuid', $uuid)->update($record);
	    	$respon = array('status'=>true,'message'=>'Informasi Cerdas Berhasil Disimpan!', 
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
            DB::table('info_cerdas')->where('uuid', $uuid)->delete();
            $respon = array('status'=>true,'message'=>'Data Berhasil Dihapus!');          
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
    //#######################################################################################

}
