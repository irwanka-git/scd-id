<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use DB;

//use Illuminate\Http\Request;
class AuthApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
    	$bearerToken = false;
    	$header = $request->header('Authorization', '');

	    if (str_starts_with($header, 'Bearer ')) {
	            $bearerToken = substr($header, 7);
	    }

        if ($bearerToken==false) {
        	$request->session()->forget('uuid_api_user');
            $respon = array('status'=>false,'message'=>'Akses Tidak Valid', 'auth'=>false);
        	return response()->json($respon);
        }else{
        	try {
			    $id = Crypt::decrypt($bearerToken);
			    $user = DB::table('users')->where('id', $id)->first();
			    if ($user){
			    	//Session::put('uuid_api_user', ));
			    	$request->session()->put('user_api', Crypt::encrypt($user->id));
			    	return $next($request);
			    }else{
			    	$respon = array('status'=>false,'message'=>'Akses Tidak Valid', 'auth'=>false);
			    	$request->session()->forget('user_api');
        			return response()->json($respon);
			    }
			} catch (DecryptException $e) {
			    //
			    $respon = array('status'=>false,'message'=>'Akses Tidak Valid','auth'=>false);
			    $request->session()->forget('user_api');
        		return response()->json($respon);
			}
        }
    }
 

}
