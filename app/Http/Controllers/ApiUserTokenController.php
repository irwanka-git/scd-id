<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;
use Auth;
use App\User;
use Hash;
use Image;

class ApiUserController  extends ApiController
{
	function get_current_user(){
		$user = $this->get_user();
		$respon = array('status'=>true,'data'=>$user);
		return $this->respon_json($respon);
	}


	function get_info_user(){
		$user = $this->get_user();
		$respon = array('status'=>true,'data'=>$user);
		return $this->respon_json($respon);
	}

	function submit_change_password(Request $r){
		$user = User::find($this->get_user()->id);
		$current = $r->current;
		$new = $r->new;
		$confirm = $r->confirm;
		if (Hash::check($current, $user->password)){
            if ($confirm == $new){
                if (strlen($confirm)>=5){ 
                    $id = $user->id;
                    $record= array(
                        'password'=>Bcrypt($confirm),
                        'updated_at'=>date("Y-m-d H:i:s")
                    );
                     DB::table('users')->where('id', $user->id)->update($record);
                     $respon = array('status'=>true,'message'=>'Password Berhasil Diubah!');
					 return $this->respon_json($respon);
                }else{
                     $respon = array('status'=>false,'message'=>'Password Minimal 5 Karakter');
					 return $this->respon_json($respon);
                }
            }else{
                $respon = array('status'=>false,'message'=>'Password Baru dan Konfirmasi Tidak Sama');
				return $this->respon_json($respon);
            }
        }else{
            $respon = array('status'=>false,'message'=>'Password Lama Tidak Sesuai');
				return $this->respon_json($respon);
        }

        $respon = array('status'=>false,'message'=>'Password Lama Tidak Sesuai');
		return $this->respon_json($respon);
		
	}

	function gen_new_token_bearer(){
		$respon = array('status'=>true,'token'=>$this->gen_token_bearer());
        return $this->respon_json($respon);
	}

	function submit_login(Request $r){
		$key = $r->key;
		if($key!='8080276-902836083-803860386-03760347'){
			$respon = array('status'=>false,'message'=>'Akses Tidak Valid');
        	return $this->respon_json($respon);	
		}
		$username = $r->username;
		$password = $r->password;
		if(Auth::attempt(['username' => $username, 'password' => $password])){
			$user = Auth::user();
			$token =  Crypt::encrypt($user->id);
			$user_data = ['username'=>$user->username, 'nama_pengguna'=>$user->nama_pengguna,'uuid'=>$user->uuid];
			$respon = array('status'=>true,'message'=>'Login Berhasil', 'token'=>$token, 'data'=>$user_data);
        	return $this->respon_json($respon);	
		}else{
			$respon = array('status'=>false,'message'=>'Username dan Password Tidak Valid!');
        	return $this->respon_json($respon);	
		}
	}

	function get_profil_user(){

		$user = $this->get_user();
		if($user){
			$profil = DB::table('users')->where('id', $user->id)->first();
			$temp = array(
                'username'=>$profil->username, 
                'uuid'=>$profil->uuid,
                'nama_pengguna'=>$profil->nama_pengguna,
                'avatar'=>url('/gambar/'.$profil->avatar),
                'id'=>$user->id,
                'organisasi'=>$profil->organisasi,
                'unit_organisasi'=>$profil->unit_organisasi,
            );
			$respon = array('status'=>true,'data'=>$temp);
			return $this->respon_json($respon);	
		}else{
			$respon = array('status'=>false,'data'=>[]);
			return $this->respon_json($respon);	
		}
		
	}

	function update_profil_user(Request $r){
		$user = $this->get_user();
		if($user){
			$nama_pengguna = trim($r->nama_pengguna);
			$organisasi = trim($r->organisasi);
			$unit_organisasi = trim($r->unit_organisasi);
			DB::table('users')
				->where('id', $user->id)
				->update([
						'nama_pengguna'=>$nama_pengguna,
						'organisasi'=>$organisasi,
						'unit_organisasi'=>$unit_organisasi,
					]);
			$respon = array('status'=>true,'message'=>'Berhasil Update Profil!');
			return $this->respon_json($respon);	
		}else{
			$respon = array('status'=>false,'message'=>'Akses Tidak Valid');
			return $this->respon_json($respon);	
		}
	}

	function upload_avatar_user(Request $request){
		$user = $this->get_user();
		$not_valid = $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        $image = $request->file('image');
        $filename= 'avatar_'.rand(100,999).time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('/gambar');
        $img = Image::make($image->getRealPath());
        $height  = $img->height();
        $width  = $img->width();
        if($height > 500 && $height > $width){
            $img->resize(null, 500, function ($constraint) {
                 $constraint->aspectRatio();
                $constraint->upsize();
            })->save($destinationPath.'/'.$filename);
        }elseif($width > 500 && $height < $width){
            $img->resize(500, null, function ($constraint) {
                 $constraint->aspectRatio();
                $constraint->upsize();
            })->save($destinationPath.'/'.$filename);
        }
        elseif($width > 500 && $height == $width){
            $img->fit(500)->save($destinationPath.'/'.$filename);
        }else{
            $img->save($destinationPath.'/'.$filename);
        } 
        DB::table('users')->where('id',$user->id)->update(['avatar'=>$filename]);
        $respon = array('status'=>true, 'data'=>url('gambar/'.$filename));
        return response()->json($respon);   
	}
	
}
