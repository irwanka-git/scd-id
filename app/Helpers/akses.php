<?php
//user_can_update
function ucu(){
	$path = Request::segment(1);
	$session_akses = session('uxa894'.'-'.$path);
	
	if($session_akses){
		$session_akses = json_decode($session_akses);
		if((int)$session_akses->update>=1) return true;
	}
	return false;	
}
function isMobileDevice() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}
//user_can_create
function ucc(){
	$path = Request::segment(1);
	$session_akses = session('uxa894'.'-'.$path);
	if($session_akses){
		$session_akses = json_decode($session_akses);
		if((int)$session_akses->create>=1) return true;
	}
	return false;
}

//user_can_delete
function ucd(){
	$path = Request::segment(1);
	$session_akses = session('uxa894'.'-'.$path);
	if($session_akses){
		$session_akses = json_decode($session_akses);
		if((int)$session_akses->delete>=1) return true;
	}
	return false;
}
 


function isAdminSistem(){
	$id_role = 1;
	return DB::table('user_role')->where('id_user', Auth::user()->id)
			->whereIn('id_role', array(1,3))->count() >=1 ;
}

function isAdminBiro(){
	return DB::table('user_role')->where('id_user', Auth::user()->id)
			->whereIn('id_role', array(5))->count() >=1 ;
}
 

function recomPassword($password){
	$panjang_minimal = 6;
	$number = preg_match('@[0-9]@', $password);
	$uppercase = preg_match('@[A-Z]@', $password);
	$lowercase = preg_match('@[a-z]@', $password);
	$specialChars = preg_match('@[^\w]@', $password);
	if(strlen($password) < $panjang_minimal || !$number || !$uppercase || !$lowercase || !$specialChars) {
	 	return false;
	}else {
	 	return true;
	}
}
 
?>