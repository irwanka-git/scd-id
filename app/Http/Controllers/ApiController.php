<?php

namespace App\Http\Controllers;

use Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Uuid;
use Illuminate\Support\Facades\Crypt;
use App\User;
use Session;

class ApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

     
    public function get_user(){
        if(Session::has('user_api')){
            $token  =  Session::get('user_api');
            $user_id = Crypt::decrypt($token);
            $user = User::find($user_id);
            $token = Crypt::encrypt($user_id);
            $temp = array(
                        'username'=>$user->username, 
                        'uuid'=>$user->uuid,
                        'nama_pengguna'=>$user->nama_pengguna,
                        'avatar'=>url('/gambar/'.$user->avatar),
                        'id'=>$user_id,
                        'device_id'=>$user->device_id,
                        'token'=>$token
                    );
            if($user){
                return json_decode(json_encode($temp));
            }
        }
        return false;
    }

    public function gen_token_bearer(){
        if(Session::has('user_api')){
            $token  =  Session::get('user_api');
            $user_id = Crypt::decrypt($token);
            $user = User::find($user_id);
            if($user){
                return Crypt::encrypt($user->id);
            }
        }
    }

    public function respon_json($json){
        //$json=json_encode(json_encode($json));
        Session::flush();
        \Config::set('session.driver', 'array');
        \Config::set('cookie.driver', 'array');
        return response()->json($json)
                ->header('Content-Type', 'application/json')
                ->header('Access-Control-Allow-Origin', '*');
    }

    function genUUID(){
        $time = time();
        //$random= rand(1000,2000);
        $alpa = ['','a','b','c','d','e','f','0','1','2','3','4','5','6','7','8','9'];
        $random = $alpa[rand(1,count($alpa)-1)];
        $random .= $alpa[rand(1,count($alpa)-1)];
        $random .= $alpa[rand(1,count($alpa)-1)];
        $random .= $alpa[rand(1,count($alpa)-1)];
        $random .= $alpa[rand(1,count($alpa)-1)];
        $random .= $alpa[rand(1,count($alpa)-1)];
        $random .= '-';
        $random .= $alpa[rand(1,count($alpa)-1)];
        $random .= $alpa[rand(1,count($alpa)-1)];
        $random .= $alpa[rand(1,count($alpa)-1)];
        $random .= $alpa[rand(1,count($alpa)-1)];
        $random .= $alpa[rand(1,count($alpa)-1)];
        $random .= '-';
        $random .= $alpa[rand(1,count($alpa)-1)];
        $random .= $alpa[rand(1,count($alpa)-1)];
        $random .= $alpa[rand(1,count($alpa)-1)];
        $random .= $alpa[rand(1,count($alpa)-1)];
        $random .= $alpa[rand(1,count($alpa)-1)];
        return $time.'-'.$random;
    }

}
