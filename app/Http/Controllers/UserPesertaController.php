<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;
use Hash;
use Auth;

class UserPesertaController extends Controller
{
     private $id_role=7;

      function index(){
    	$pagetitle = "Pengaturan User";
    	$smalltitle = "Pengaturan User dan Akses User";
    	return view('manajemen-user.user-peserta', compact('pagetitle','smalltitle'));
    }
    
  
    function get_data($uuid){       
    	$user = DB::table('users')->where('uuid', $uuid)->first();
       
        if($user){
            $respon = array('status'=>true,'data'=>$user, 
            	'informasi'=>'Nama Pengguna: '. $user->nama_pengguna);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }
    
   
    function datatable(){
        $filter = "";
        loadHelper('akses');
        if(isAdminBiro()){
            $filter = " and a.create_by = ". Auth::user()->id;
        }

        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter = " and (lower(a.username) like '%$keyword%' or lower(a.nama_pengguna) like '%$keyword%') ";
            }   
        }
        $id_role=$this->id_role;
        $sql_union = "select a.device_id, a.username, a.nama_pengguna,a.avatar, a.organisasi,a.unit_organisasi,
        a.email, a.telp, a.uuid from users as a, user_role as b 
        where a.id = b.id_user and b.id_role = $id_role
        $filter order by a.username asc";

        $query = DB::table(DB::raw("($sql_union) as x order by username "))
                ->select(['username','device_id', 'avatar', 'organisasi','unit_organisasi','nama_pengguna','email', 'telp', 'uuid']);

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
                    
                    $delete = ' <button data-uuid="'.$query->uuid.'"  class="btn btn-outline-secondary btn-outline btn-sm btn-konfirm-revoke" type="button">
                    <i class="las la-user-slash"></i></button>';
                    $delete .= ' <button  data-uuid="'.$query->uuid.'" class="btn btn-outline-secondary btn-sm btn-konfirm-delete" type="button">
                    <i class="las la-trash"></i></button>';
                }
                $action =  $edit." ".$delete;
                if ($action==""){$action='<a href="#" class="act"><i class="la la-lock"></i></a>'; }
                    return $action;
        })
        ->addColumn('user', function ($q) {
            $device_status = "";
            if($q->device_id!=""){
                $device_status='&nbsp;<span class="badge bg-success pull-right"> Login </span>';
            }
            return '<div class="d-flex align-items-start">
                      <img src="'.url("gambar/".$q->avatar).'" width="36" height="36" class="rounded-circle me-2">
                      <div class="info-user">
                      <small>'.$q->username. $device_status.'</small>
                      <br><a class="btn-view-user" data-id="'.$q->uuid.'">'.$q->nama_pengguna.'</a>
                      </div>
                  </div>';
      })
      ->addColumn('departement', function ($q) {
            return '<div class="info-user">
                      <small>'.$q->unit_organisasi.'</small>
                      <br>'.$q->organisasi.'
                      </div>';
      })
        ->addIndexColumn()
        ->rawColumns(['action','menu','user','departement'])
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
                "jenis_kelamin"=>trim($r->jenis_kelamin),
                "organisasi"=>trim($r->organisasi),
                "unit_organisasi"=>trim($r->unit_organisasi),
                "email"=>trim($r->email),
                "telp"=>trim($r->telp),
                "avatar"=>'user.png',
                "create_by"=>Auth::user()->id
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
                "jenis_kelamin"=>trim($r->jenis_kelamin),
                "organisasi"=>trim($r->organisasi),
                "unit_organisasi"=>trim($r->unit_organisasi),
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
            $id_user = DB::table('users')->select('id')->where('uuid', trim($r->uuid))->first()->id;
            DB::table('users')->where('uuid', trim($r->uuid))->delete();
            DB::table('user_role')->where('uuid', trim($r->uuid))->delete();
            DB::table('quiz_sesi_user')->where('id_user', trim($id_user))->delete();
            DB::table('skoring_minat_man')->where('id_user', trim($id_user))->delete();
            DB::table('skoring_minat_smk')->where('id_user', trim($id_user))->delete();
            DB::table('skoring_minat_sma')->where('id_user', trim($id_user))->delete();
            DB::table('skoring_penjurusan_kuliah')->where('id_user', trim($id_user))->delete();
            
            $respon = array('status'=>true,'message'=>'Data Berhasil Dihapus!', 
            '_token'=>csrf_token());       
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
    
    function submit_revoke(Request $r){
        if($this->ucd()){
            loadHelper('format');
            $id_user = DB::table('users')->select('id')->where('uuid', trim($r->uuid))->first()->id;
            DB::table('users')->where('id', $id_user)->update(['device_id'=>""]);
            $respon = array('status'=>true,'message'=>'Status Login Perangkat Berhasil Dihapus!', 
            '_token'=>csrf_token());       
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
    
}