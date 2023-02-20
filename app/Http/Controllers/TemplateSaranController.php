<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;

class TemplateSaranController extends Controller
{
     
    //######################### PETUNJUK SOAL #####################################
 	// id_info		int
	// judul		varchar
	// isi			text
	// gambar		varchar
	// created_at	datetime
	// uuid			varchar


    function index(){
    	$pagetitle = "Template Saran";
    	$smalltitle = "Pengaturan Template Saran";
    	return view('template-saran.index', compact('pagetitle','smalltitle'));
    }

    function datatable(Request $r){
        
        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter = " and (lower(a.nama_template_saran) like '%$keyword%') ";
            }   
        }

         $sql_union = "select a.id_template_saran, a.uuid  , a.nama_template_saran, a.skoring_tabel, a.isi
         	from quiz_template_saran as a where  a.id_template_saran> 0  $filter 
         	order by a.id_template_saran desc  ";

         $query = DB::table(DB::raw("($sql_union) as x"))
                    ->select(['nama_template_saran','skoring_tabel','uuid','isi']);

         return Datatables::of($query)
            ->addColumn('action', function ($query) {
                    $edit = ""; $delete = "";
                    if($this->ucu()){
                        $edit = '<button data-bs-tip="tooltip" data-bs-placement="top" data-bs-original-title="Ubah Template Saran" data-bs-toggle="modal" data-uuid="'.$query->uuid.'" data-bs-target="#modal-edit"  class="btn btn-light btn-sm" type="button"><ion-icon name="create-outline" btn></ion-icon></button>';
                    }
                    if($this->ucd()){
                        $delete = '<button <button data-bs-tip="tooltip" data-bs-placement="top" data-bs-original-title="Hapus Template Saran"  data-uuid="'.$query->uuid.'" class="btn btn-light btn-sm btn-konfirm-delete"  
                        type="button"><ion-icon name="trash-outline" btn></ion-icon></button>';
                    }
                    $action =  $edit." ".$delete;
                    if ($action==""){$action='<a href="#" class="act"><i class="la la-lock"></i></a>'; }
                        return  '<div class="btn-group" role="group">'.$action.'</button>';
            })            
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }

    function get_data($uuid){
    	$data = DB::table('quiz_template_saran')->where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
            	'informasi'=>'Nama Template : '. $data->nama_template_saran);
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
	    		"nama_template_saran"=>trim($r->nama_template_saran), 
	    		"skoring_tabel"=>trim($r->skoring_tabel), 
	    		"isi"=>trim($r->isi), 
                "salam_pembuka"=>trim($r->salam_pembuka), 
	    		"uuid"=>$uuid);

	    	DB::table('quiz_template_saran')->insert($record);
	    	$respon = array('status'=>true,'message'=>'Template Saran Berhasil Ditambahkan!', '_token'=>csrf_token());
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
	    		"nama_template_saran"=>trim($r->nama_template_saran), 
	    		"skoring_tabel"=>trim($r->skoring_tabel), 
	    		"isi"=>trim($r->isi),
                "salam_pembuka"=>trim($r->salam_pembuka), 
            );
	    	DB::table('quiz_template_saran')->where('uuid', $uuid)->update($record);
	    	$respon = array('status'=>true,'message'=>'Template Saran Berhasil Disimpan!', 
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
            DB::table('quiz_template_saran')->where('uuid', $uuid)->delete();
            $respon = array('status'=>true,'message'=>'Data Berhasil Dihapus!');          
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
    //#######################################################################################

}
