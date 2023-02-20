<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;

class SettingRoleController extends Controller
{
     
    //######################### SETTING Role #####################################
    function index(){
    	$pagetitle = "Pengaturan Role";
    	$smalltitle = "Pengaturan Role dan Akses Menu";
    	return view('setting.role', compact('pagetitle','smalltitle'));
    }

    function datatable_role(Request $r){
        
        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter = " and (lower(a.nama_role) like '%$keyword%' ) ";
            }   
        }

         $sql_union = "select a.nama_role, a.uuid, count(b.id_menu) as jumlah_menu 
                        from role as a left join  role_menu as b on 
                         a.id_role = b.id_role where a.id_role > 0 $filter
                        group by a.nama_role, a.uuid, a.id_role  order by a.id_role asc ";

         $query = DB::table(DB::raw("($sql_union) as x"))
                    ->select(['nama_role','uuid','jumlah_menu']);

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
             ->addColumn('menu', function ($query) {
                   return '<a href="'.url('setting-role/menu/'.$query->uuid).'" class="btn btn-outline-secondary btn-sm">'.$query->jumlah_menu.' Menu</a>';
            })
            ->addIndexColumn()
            ->rawColumns(['action','menu'])
            ->make(true);
    }

    // <a href="#"><i class="align-middle" data-feather="edit-2"></i></a>
    //                                             <a href="#"><i class="align-middle" data-feather="trash"></i></a>

    function get_data_role($uuid){
    	$data = DB::table('role')->where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
            	'informasi'=>'Nama Role: '. $data->nama_role);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }

    function submit_insert_role(Request $r){
    	if($this->ucc()){
	    	loadHelper('format');
	    	$uuid = $this->genUUID();

	    	$record = array(
	    		"nama_role"=>trim($r->nama_role), 
	    		"uuid"=>$uuid);

	    	DB::table('role')->insert($record);
	    	$respon = array('status'=>true,'message'=>'Role Berhasil Ditambahkan!', '_token'=>csrf_token());
        	return response()->json($respon);
    	}else{
    		$respon = array('status'=>false,'message'=>'Akses Ditolak!');
        	return response()->json($respon);
    	}
    }

    function submit_update_role(Request $r){
    	if($this->ucu()){
	    	loadHelper('format');
	    	$uuid = $r->uuid;

	    	$record = array(
	    		"nama_role"=>trim($r->nama_role), 
	    	);

	    	DB::table('role')->where('uuid', $uuid)->update($record);
	    	$respon = array('status'=>true,'message'=>'Data Role Berhasil Disimpan!', 
	    		'_token'=>csrf_token());
        	return response()->json($respon);
    	}else{
    		$respon = array('status'=>false,'message'=>'Akses Ditolak!');
        	return response()->json($respon);
    	}
    }

    function submit_delete_role(Request $r){
        if($this->ucd()){
            loadHelper('format');
            $uuid = $r->uuid;
            $role = DB::table('role')->where("uuid", $uuid)->first();
            $respon = array('status'=>true,'message'=>'Data Role Berhasil Dihapus!');
            //hapus menu pada role
            DB::table('role_menu')->where('id_role', $role->id_role)->delete();
            //hapus role
            DB::table('role')->where('id_role', $role->id_role)->delete();          
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }


    //## MENU PADA ROLE $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$//
     function menu_role($uuid){
        $role = DB::table('role')->where("uuid", $uuid)->first();
        $pagetitle = "Pengaturan Menu Role";
        $smalltitle = "Pengaturan Role dan Akses Menu";
        return view('setting.role-menu', compact('pagetitle','smalltitle','role'));
    }

    function datatable_menu($id_role){
        $filter = "";
        $role = DB::table('role')->where('id_role', $id_role)->first();
        $id_role_x = $role->id_role;
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter = " and (lower(b.nama_menu) like '%$keyword%' ) ";
            }   
        }

         $sql_union = "select   concat(c.nama_menu, ' => ', b.nama_menu) as nama_menu , a.uuid , a.ucc, a.ucu, a.ucd 
                    from role_menu as a, menu as b , menu as c 
                    where a.id_menu = b.id_menu
                    and b.id_menu_induk = c.id_menu  $filter
                    and a.id_role = $id_role_x order by c.urutan, b.urutan";

         $query = DB::table(DB::raw("($sql_union) as x"))
                    ->select(['nama_menu','uuid','ucc','ucd','ucu']);

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
            ->editColumn('ucc', function($q){
                if ($q->ucc==0) { return '<span class="badge bg-danger">No</span>';}
                if ($q->ucc==1) { return '<span class="badge bg-success">Yes</span>';}
            })
            ->editColumn('ucu', function($q){
                if ($q->ucu==0) { return '<span class="badge bg-danger">No</span>';}
                if ($q->ucu==1) { return '<span class="badge bg-success">Yes</span>';}
            })
            ->editColumn('ucd', function($q){
                if ($q->ucd==0) { return '<span class="badge bg-danger">No</span>';}
                if ($q->ucd==1) { return '<span class="badge bg-success">Yes</span>';}
            })
            ->addIndexColumn()
            ->rawColumns(['action','menu','ucc','ucu','ucd'])
            ->make(true);
    }

    function get_data_menu($uuid){
        $data = DB::table('role_menu')->where('uuid', $uuid)->first();
        if($data){
            $menu = DB::table('menu')->where('id_menu', $data->id_menu)->first();
            $data->nama_menu = $menu->nama_menu;
            $respon = array('status'=>true,'data'=>$data, 
                'informasi'=>'Akses Menu: '. $menu->nama_menu);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }

    function submit_insert_menu(Request $r){
        if($this->ucc()){
            loadHelper('format');
            $uuid = $this->genUUID();

            $record = array(
                "id_role"=>(int)($r->id_role), 
                "id_menu"=>(int)($r->id_menu), 
                "ucc"=>(int)($r->ucc), 
                "ucd"=>(int)($r->ucd), 
                "ucu"=>(int)($r->ucu), 
                "uuid"=>$uuid);

            DB::table('role_menu')->insert($record);
            $respon = array('status'=>true,'message'=>'Hak Akses Menu Berhasil Ditambahkan!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }

    function submit_update_menu(Request $r){
        if($this->ucu()){
            loadHelper('format');
            $uuid = $r->uuid;
            $record = array(
                "ucc"=>(int)($r->ucc), 
                "ucd"=>(int)($r->ucd), 
                "ucu"=>(int)($r->ucu));

            DB::table('role_menu')->where('uuid',$uuid)->update($record);
            $respon = array('status'=>true,'message'=>'Hak Akses Menu Berhasil Disimpan!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }

    function submit_delete_menu(Request $r){
        if($this->ucd()){
            loadHelper('format');
            $uuid = $r->uuid;
            $respon = array('status'=>true,'message'=>'Akses Menu Berhasil Dihapus!');
            //hapus menu pada role
            DB::table('role_menu')->where('uuid', $uuid)->delete();    
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
    //#######################################################################################

}
