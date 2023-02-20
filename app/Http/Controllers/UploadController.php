<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;
use Image;

use PhpOffice\PhpSpreadsheet\Spreadsheet;



class UploadController extends Controller
{
     
     function upload_gambar(Request $request){
        $not_valid = $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        $image = $request->file('image');
        $filename= rand(100,999).time().'.'.$image->getClientOriginalExtension();
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

        $path = public_path().'/gambar/'.$filename;
        $data = file_get_contents($path);
        $image_base64 = base64_encode($data);
        $type = pathinfo($path, PATHINFO_EXTENSION);

        DB::table('gambar')->insert(['filename'=>$filename, 
                    'type'=>$type, 
                    'image_base64'=>$image_base64]);   

        $respon = array('status'=>true,'url_image'=>url('gambar/'.$filename), 
                'filename'=>$filename, 
                'height'=>$height, 'width'=>$width);
        return response()->json($respon);   
    }

    function upload_gambar_file(Request $request){
        $not_valid = $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        $image = $request->file('image');
        $filename= rand(100,999).time().'.'.$image->getClientOriginalExtension();
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
        
        $respon = array('status'=>true,
                'url_image'=>url('gambar/'.$filename), 
                'filename'=>$filename, 
                'height'=>$height, 'width'=>$width);
        return response()->json($respon);   
    }


     function upload_gambar_400_250(Request $request){
        $not_valid = $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        $image = $request->file('image');
        $filename= rand(100,999).time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('/gambar');
        $img = Image::make($image->getRealPath());
        $height  = $img->height();
        $width  = $img->width();
        $img->fit(400,250)->save($destinationPath.'/'.$filename);
        
        $respon = array('status'=>true,
                'url_image'=>url('gambar/'.$filename), 
                'filename'=>$filename, 
                'height'=>$height, 'width'=>$width);
        return response()->json($respon);   
    }


     function upload_gambar_kop(Request $request){
        $not_valid = $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        $image = $request->file('image');


        $height = Image::make($image)->height();
        $width = Image::make($image)->width();
        if($height !=90 || $width !=800){
            $respon = array('status'=>false, 'message'=>'Ukuran gambar tidak sesuai (ukuran wajib 800 pixel(Lebar) x 90 pixel (tinggi) )');
            return response()->json($respon);   
        }

        $filename= rand(100,999).time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('/kop');
        $img = Image::make($image->getRealPath());
        $height  = $img->height();
        $width  = $img->width();
        $img->fit(800,80)->save($destinationPath.'/'.$filename);
        
        $respon = array('status'=>true,
                'url_image'=>url('kop/'.$filename), 
                'filename'=>$filename, 
                'height'=>$height, 'width'=>$width);
        return response()->json($respon);   
    }

    function upload_gambar_250_150(Request $request){
        $not_valid = $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        $image = $request->file('image');
        
        $height = Image::make($image)->height();
        $width = Image::make($image)->width();
        if($height !=150 || $width !=250){
            $respon = array('status'=>false, 'message'=>'Ukuran gambar tidak sesuai (ukuran wajib 250 pixel(Lebar) x 150 pixel (tinggi) )');
            return response()->json($respon);   
        }

        $filename= rand(100,999).time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('/gambar');
        $img = Image::make($image->getRealPath());
        $height  = $img->height();
        $width  = $img->width();
        $img->fit(250,150)->save($destinationPath.'/'.$filename);

        $data = file_get_contents($destinationPath.'/'.$filename);
        $image_base64 = base64_encode($data);
        $type = pathinfo($destinationPath.'/'.$filename, PATHINFO_EXTENSION);

        DB::table('gambar')->insert(['filename'=>$filename, 
                    'type'=>$type, 
                    'image_base64'=>$image_base64]);   

        $respon = array('status'=>true,
                'url_image'=>url('gambar/'.$filename), 
                'filename'=>$filename, 
                'height'=>$height, 'width'=>$width);
        return response()->json($respon);   
    }


    function upload_excel(Request $request){

        $valid = $this->validate($request, ['excel'=> 'required|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
        $file = $request->file('excel');
        $ext = $file->getClientOriginalExtension();
        $filename = time().rand(1111,5555).".".$ext;
        $file->move('uploads',$filename);
        $respon = array('status'=>true, 'filename'=>$filename, 'token'=>$this->randomToken());
        return response()->json($respon);  
    }

    function randomToken(){
        $time = time();
        //$random= rand(1000,2000);
        $alpa = ['','0','1','2','3','4','5','6','7','8','9'];
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

    function generate_import_data_excel_peserta($uuid, $token, $filename){

        $quiz = DB::table('quiz_sesi')->where('uuid', $uuid)->first();
        $id_quiz = $quiz->id_quiz;

        $inputFileName = public_path('uploads/'.$filename);
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setLoadSheetsOnly(["DATA"]);
        $spreadsheet = $reader->load($inputFileName);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = 9; //SEMBILAN KOLOM SESUAI TEMPLATE 
        //MULAI BARIS KE DUA;
        $data = array();
        $fieldname = array('',
                        'token',
                        'username',
                        'nama_pengguna',
                        'jenis_kelamin',
                        'organisasi',
                        'unit_organisasi',
                        'password',
                        'email',
                        'telp',
                    );
        for ($row = 2; $row <= $highestRow; ++$row) {
            $record['token'] = $token;
            for ($col = 2; $col <= $highestColumnIndex; ++$col) {
                $value = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                $record[$fieldname[$col]] = $value;
            }
            array_push($data, $record);
        }

        DB::table('user_upload_excel')->where('token', $token)->delete();
        foreach($data as $record){
            DB::table('user_upload_excel')->insert($record);
        }

        $cek_data = DB::select("select 
                                a.username, 
                                a.nama_pengguna, 
                                a.jenis_kelamin,
                                a.organisasi, 
                                a.unit_organisasi ,
                                COALESCE(b.id, 0) as cek_akun, 
                                COALESCE(c.id_quiz_user, 0)  as cek_peserta
                                from user_upload_excel as a
                                left join users as b on a.username = b.username
                                left join quiz_sesi_user as c on (b.id = c.id_user and c.id_quiz = $id_quiz)
                                where a.token = '$token'");
        foreach($cek_data as $r){
            if($r->cek_peserta){
                DB::table('user_upload_excel')
                    ->where('username', $r->username)
                    ->where('token', $token)
                    ->update(['valid'=>0]);
            }else if($r->cek_akun){
                DB::table('user_upload_excel')
                    ->where('username', $r->username)
                    ->where('token', $token)
                    ->update(['valid'=>2]);
            }
        }

        return view('manajemen-sesi.tabel-upload', compact('cek_data'));
    }

}
