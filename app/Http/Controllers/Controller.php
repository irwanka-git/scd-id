<?php

namespace App\Http\Controllers;

use Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Uuid;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function genUUID(){
        list($usec, $sec) = explode(" ", microtime());
        $time = ((float)$usec + (float)$sec);
        $time = str_replace(".", "-", $time);
        $panjang = strlen($time);
        $sisa = substr($time, -1*($panjang-5));
        return Uuid::generate(3,rand(10,99).rand(0,9).substr($time, 0,5).'-'.rand(0,9).rand(0,9)."-".$sisa,Uuid::NS_DNS);
    }

    // function allow_user_update(){
    function ucc(){
    	$path = Request::segment(1);
		$session_akses = session('uxa894'.'-'.$path);
		if($session_akses){
			$session_akses = json_decode($session_akses);
			if((int)$session_akses->create>=1) return true;
		}
		return false;
    }

   // function allow_user_delete(){
    function ucu(){
    	
    	$path = Request::segment(1);
		$session_akses = session('uxa894'.'-'.$path);
		
		if($session_akses){
			$session_akses = json_decode($session_akses);
			if((int)$session_akses->update>=1) return true;
		}
		return false;	

    }

    // function allow_user_create(){
    function ucd(){
    	$path = Request::segment(1);
		$session_akses = session('uxa894'.'-'.$path);
		if($session_akses){
			$session_akses = json_decode($session_akses);
			if((int)$session_akses->delete>=1) return true;
		}
		return false;
    }

    
    function base_route($path=""){
        $route = request()->segment(1);
        if($path!=""){
            return url($route."/".$path);
        }
        return url($route);
    }
}
