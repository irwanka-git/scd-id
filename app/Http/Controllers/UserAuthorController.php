<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;
use Hash;

class UserAuthorController extends Controller
{
     private $id_role=4;

      function index(){
    	$pagetitle = "Pengaturan User";
    	$smalltitle = "Pengaturan User dan Akses User";
    	return view('manajemen-user.user-author', compact('pagetitle','smalltitle'));
    }
    
  
    function get_data($uuid){       
    	$user = DB::table('users')->where('uuid', $uuid)->first();
       
        if($user){
            $respon = array('status'=>true,'data'=>$user, 
            	'informasi'=>'Nama user: '. $user->nama_pengguna);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }
    
   
    function datatable(){
        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter = " and (lower(a.username) like '%$keyword%' or lower(a.nama_pengguna) like '%$keyword%') ";
            }   
        }
        $id_role=$this->id_role;
        $sql_union = "select a.username, a.nama_pengguna, a.email, a.telp, a.uuid from users as a, user_role as b 
        where a.id = b.id_user and b.id_role = $id_role
        $filter order by a.username asc";

        $query = DB::table(DB::raw("($sql_union) as x order by username "))
                ->select(['username', 'nama_pengguna','email', 'telp', 'uuid']);

        return Datatables::of($query)
        ->addColumn('action', function ($query) {
                $edit = ""; $delete = "";
                if($this->ucu()){

                    $edit = '<button data-bs-toggle="modal" data-uuid="'.$query->uuid.'" data-bs-target="#modal-password" class="btn btn-outline-secondary btn-outline btn-sm" type="button">
                    <i class="las la-key"></i></button>';
                    
                    $edit .= ' <button data-bs-toggle="modal" data-uuid="'.$query->uuid.'" data-bs-target="#modal-edit" class="btn btn-outline-secondary btn-outline btn-sm" type="button">
                    <i class="las la-pen"></i></button>';
                }
                if($this->ucd()){
                    $delete = '<button  data-uuid="'.$query->uuid.'" class="btn btn-outline-secondary btn-sm btn-konfirm-delete" type="button">
                    <i class="las la-trash"></i></button>';
                }
                $action =  $edit." ".$delete;
                if ($action==""){$action='<a href="#" class="act"><i class="la la-lock"></i></a>'; }
                    return $action;
        })
        
        ->addIndexColumn()
        ->rawColumns(['action','menu'])
        ->make(true);
    }


    function submit_insert(Request $r){
        if($this->ucc()){
            loadHelper('format');
            $uuid = $this->genUUID();
            $record_user = array(
                "username"=>trim($r->username), 
                "uuid"=>$uuid,
                "nama_pengguna"=>trim($r->nama_pengguna),
                "password"=>Hash::make(trim($r->password)),
                "email"=>trim($r->email),
                "avatar"=>'user.png',
                "telp"=>trim($r->telp)
                );

            $insert = DB::table('users')->insert($record_user);
            if($insert){
                $user_last = DB::table('users')->where('uuid',$uuid)->first();
                $id_user = $user_last->id;
                $record_role = array(
                    'id_user'=>$id_user,
                    'id_role'=>$this->id_role,
                    'uuid'=>$uuid
                );
                $insert = DB::table('user_role')->insert($record_role);
            }
            $respon = array('status'=>true,'message'=>'Data Berhasil Ditambahkan!', '_token'=>csrf_token());
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }


    function submit_update(Request $r){
        if($this->ucu()){
            $uuid = trim($r->uuid);
            $password = Hash::make(trim($r->password));
            $record = array(
                "username"=>trim($r->username), 
                "nama_pengguna"=>trim($r->nama_pengguna),              
                "email"=>trim($r->email),
                "telp"=>trim($r->telp)
                );
            if(strlen(trim($r->password))>4){
                $record = array(
                    "username"=>trim($r->username), 
                    "nama_pengguna"=>trim($r->nama_pengguna),
                    "password"=>$password,
                    "email"=>trim($r->email),
                    "telp"=>trim($r->telp)
                    );
            }
            DB::table('users')->where('uuid', $uuid)->update($record);
            $respon = array('status'=>true,'message'=>'Data Berhasil Diperbaruhi!', '_token'=>csrf_token());
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }

    function submit_update_password (Request $r){
        if($this->ucu()){
            $uuid = trim($r->uuid);
            $password = Hash::make(trim($r->password));
          
            if(strlen(trim($r->password))>4){
                $record = array(
                    "password"=>$password,
                    );
            }
            DB::table('users')->where('uuid', $uuid)->update($record);
            $respon = array('status'=>true,'message'=>'Password Berhasil Dirubah!', '_token'=>csrf_token());
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }

    

    function submit_delete(Request $r){
        if($this->ucd()){
            loadHelper('format');
            DB::table('users')->where('uuid', trim($r->uuid))->delete();
            DB::table('user_role')->where('uuid', trim($r->uuid))->delete();
            $respon = array('status'=>true,'message'=>'Data Berhasil Dihapus!', 
            '_token'=>csrf_token());       
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
    
}