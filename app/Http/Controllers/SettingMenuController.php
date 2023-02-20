<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;

class SettingMenuController extends Controller
{
     
    //######################### SETTING MENU #####################################
    function index(){
    	$pagetitle = "Pengaturan Menu";
    	$smalltitle = "Pengaturan Menu Aplikasi";
    	return view('setting.menu', compact('pagetitle','smalltitle'));
    }

    function datatable(Request $r){
        
        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter = " and (lower(a.nama_menu) like '%$keyword%' or lower(b.nama_menu) like '%$keyword%') ";
            }   
        }

         $sql_union = "select a.nama_menu , a.url, a.uuid, a.urutan, b.nama_menu as group_menu from menu as a, menu as b where a.id_menu_induk = b.id_menu and a.id_menu_induk > 0  $filter order by a.id_menu_induk  ";

         $query = DB::table(DB::raw("($sql_union) as x"))
                    ->select(['group_menu','nama_menu', 'url','uuid','urutan']);

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
            ->addIndexColumn()
            ->rawColumns(['action','label'])
            ->make(true);
    }

    // <a href="#"><i class="align-middle" data-feather="edit-2"></i></a>
    //                                             <a href="#"><i class="align-middle" data-feather="trash"></i></a>

    function get_data($uuid){
    	$menu = DB::table('menu')->where('uuid', $uuid)->first();
        if($menu){
            $respon = array('status'=>true,'data'=>$menu, 
            	'informasi'=>'Nama Menu: '. $menu->nama_menu);
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
	    		"id_menu_induk"=>$r->id_menu_induk, 
	    		"nama_menu"=>trim($r->nama_menu), 
	    		"url"=>trim($r->url),
	    		"urutan"=>$r->urutan, 
	    		"uuid"=>$uuid);

	    	DB::table('menu')->insert($record);
	    	$respon = array('status'=>true,'message'=>'Menu Berhasil Ditambahkan!', '_token'=>csrf_token());
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
	    		"id_menu_induk"=>$r->id_menu_induk, 
	    		"nama_menu"=>trim($r->nama_menu), 
	    		"url"=>trim($r->url),
	    		"urutan"=>$r->urutan
	    	);

	    	DB::table('menu')->where('uuid', $uuid)->update($record);
	    	$respon = array('status'=>true,'message'=>'Data Menu Berhasil Disimpan!', 
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
            $menu = DB::table('menu')->where("uuid", $uuid)->first();
            
            $exist_role = DB::table('role_menu')->where('id_menu', $menu->id_menu)->count();
            if($exist_role==0){
                DB::table('menu')->where('uuid', $uuid)->delete();
                $respon = array('status'=>true,'message'=>'Data Menu Berhasil Dihapus!', 
                '_token'=>csrf_token());
            }else{
                $respon = array('status'=>false,'message'=>'Data Menu Tidak Dihapus!', 
                '_token'=>csrf_token());
            }            
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
    //#######################################################################################

}
