<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;

class InterprestasiTipologiJungController   extends Controller
{
     
    //######################### SOAL TEST KOGNITIF #####################################
    function index(){
    	$pagetitle = "Interprestasi Tipologi Jung";
    	$smalltitle = "Pengaturan Soal";
    	return view('manajeman-soal.index-interprestasi-jung', compact('pagetitle','smalltitle'));
    }

    function datatable(Request $r){
        
        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter = " and ( lower(a.kode) like '%$keyword%'    
                			or lower(a.nama) like '%$keyword%'                     
                			or lower(a.keterangan) like '%$keyword%'                     
                            ) ";
            }   
        }

         $sql_union = "select a.* from interprestasi_tipologi_jung as a where a.id_interprestasi > 0 $filter  ";
         $query = DB::table(DB::raw("($sql_union) as x order by urutan"))
                    ->select([
                        'id_interprestasi',
                        'urutan',
                        'kode',
                        'nama',
                        'keterangan',
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
            ->rawColumns(['action'])
            ->make(true);
    }

    // <a href="#"><i class="align-middle" data-feather="edit-2"></i></a>
    //                                             <a href="#"><i class="align-middle" data-feather="trash"></i></a>


    function view_soal($uuid){
        $soal = DB::table('interprestasi_tipologi_jung')->where('uuid', $uuid)->first();
        if($soal){
            return view('manajeman-soal.view-interprestasi-jung',compact('soal'));
        }
        return '<center><b>Maaf, Soal Tidak Ditemukan</b></center>';
    }

    function get_data($uuid){
    	$data = DB::table('interprestasi_tipologi_jung')->where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
            	'informasi'=>'Informasi : Kode '.$data->kode);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }

    function submit_insert(Request $r){
    	if($this->ucc()){
	    	loadHelper('format');
	    	$uuid = $this->genUUID();
	    	$deskripsi =trim($r->deskripsi,"\t");
	    	$deskripsi =trim($deskripsi,"\n");
            //return $pernyataan;
	    	$record = array(
                "urutan"=>(int)$r->urutan, 
                "kode"=>strtoupper(trim($r->kode)), 
                "nama"=>trim($r->nama), 
                "keterangan"=>trim($r->keterangan),
                "deskripsi"=>$deskripsi,
	    		"uuid"=>$uuid);

	    	DB::table('interprestasi_tipologi_jung')->insert($record);
	    	$respon = array('status'=>true,'message'=>'Interprestasi Berhasil Ditambahkan!');
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
	    	$deskripsi =trim($r->deskripsi);
	    	$deskripsi =str_replace("\n", '', $deskripsi);
	    	$deskripsi =str_replace("  ", '', $deskripsi);
	    	$deskripsi =str_replace(PHP_EOL, '', $deskripsi);
	    	$deskripsi = str_replace(array("\r", "\n"), '', $deskripsi);


            //return $pernyataan;
	    	$record = array(
               "urutan"=>(int)$r->urutan, 
                "kode"=>strtoupper(trim($r->kode)), 
                "nama"=>trim($r->nama), 
                "keterangan"=>trim($r->keterangan),
                "deskripsi"=>$deskripsi,
            );

	    	DB::table('interprestasi_tipologi_jung')->where('uuid', $uuid)->update($record);
	    	$respon = array('status'=>true,'message'=>'Interprestasi Berhasil Disimpan!');
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
            DB::table('interprestasi_tipologi_jung')->where('uuid', $uuid)->delete();
            $respon = array('status'=>true,'message'=>'Data Berhasil Dihapus!');          
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
    //#######################################################################################

}
