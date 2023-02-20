<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;
use Auth;
use Image;
use Response;
use Zipper;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Storage;
use Illuminate\Contracts\Encryption\DecryptException;
 




class CetakController extends Controller
{
    function render_report_individu($token){
         $_token = explode("_", $token);
         $token = $_token[0];
         $nomor_seri = $_token[1];
    	 $quiz_sesi_user =DB::table('quiz_sesi_user')
                        ->select('uuid','id_user','id_quiz')
                        ->where('token_submit', $token)
                        ->first();

         $user = DB::table('users')->where('id', $quiz_sesi_user->id_user)->first();
         $quiz = DB::table('quiz_sesi')->where('id_quiz', $quiz_sesi_user->id_quiz)->first();

         $uuid = $quiz_sesi_user->uuid;
         $data_sesi = DB::select("select 
                                a.id_quiz, 
                                b.id_user_biro,
                                a.skoring_at,
                                a.id_user, 
                                a.jawaban_skoring, 
                                a.submit_at,
                                b.kota,
                                b.skoring_tabel, 
                                c.username, 
                                c.nama_pengguna, 
                                a.saran,
                                c.avatar, 
                                c.jenis_kelamin, 
                                c.unit_organisasi, 
                                c.organisasi,
                                b.nama_sesi, 
                                b.tanggal, 
                                b.lokasi, 
                                b.nama_asesor,
                                b.nomor_sipp,
                                b.ttd_asesor
                                from 
                                    quiz_sesi_user as a , quiz_sesi as  b, users as c 
                                where 
                                    a.id_quiz = b.id_quiz and c.id = a.id_user
                                    and a.uuid = '$uuid' ");
        if(count($data_sesi)==1){
            $data_sesi  = $data_sesi[0];
            $skoring_tabel = $data_sesi->skoring_tabel;
            $data_skoring = DB::table($skoring_tabel)
                            ->where('id_quiz', $data_sesi->id_quiz)
                            ->where('id_user', $data_sesi->id_user)
                            ->first();
            return view('report.'.$skoring_tabel.'.cetak', compact('nomor_seri','quiz','user','data_sesi','data_skoring') );
        }
    }


    //lampiran penjurusan kuliah (PMK)
    function render_lampiran_penjurusan_kuliah($token){
         $_token = explode("_", $token);
         $token = $_token[0];
         $nomor_seri = $_token[1];
         $quiz_sesi_user =DB::table('quiz_sesi_user')
                        ->select('uuid','id_user','id_quiz')
                        ->where('token_submit', $token)
                        ->first();

         $user = DB::table('users')->where('id', $quiz_sesi_user->id_user)->first();
         $quiz = DB::table('quiz_sesi')->where('id_quiz', $quiz_sesi_user->id_quiz)->first();

         $uuid = $quiz_sesi_user->uuid;
         $data_sesi = DB::select("select 
                                a.id_quiz, 
                                b.id_user_biro,
                                a.skoring_at,
                                a.id_user, 
                                a.jawaban_skoring, 
                                a.submit_at,
                                b.kota,
                                b.skoring_tabel, 
                                c.username, 
                                c.nama_pengguna, 
                                a.saran,
                                c.avatar, 
                                c.jenis_kelamin, 
                                c.unit_organisasi, 
                                c.organisasi,
                                b.nama_sesi, 
                                b.tanggal, 
                                b.lokasi, 
                                b.nama_asesor,
                                b.nomor_sipp
                                from 
                                    quiz_sesi_user as a , quiz_sesi as  b, users as c 
                                where 
                                    a.id_quiz = b.id_quiz and c.id = a.id_user
                                    and a.uuid = '$uuid' ");
        if(count($data_sesi)==1){
            $data_sesi  = $data_sesi[0];
            $skoring_tabel = $data_sesi->skoring_tabel;
            $data_skoring = DB::table($skoring_tabel)
                            ->where('id_quiz', $data_sesi->id_quiz)
                            ->where('id_user', $data_sesi->id_user)
                            ->first();
            return view('report.'.$skoring_tabel.'.lampiran', compact('nomor_seri','quiz','user','data_sesi','data_skoring') );
        }
    }

    function pdf_report_individu($token)
    {
        $quiz_sesi =DB::table('quiz_sesi_user')
                        ->select('id_user','token_submit','id_quiz')
                        ->where('token_submit', $token)
                        ->first();

        $token = $quiz_sesi->token_submit;
        $ketemu = 1;
        while($ketemu){
            $no_seri = rand(100,999).rand(100,999).rand(100,999);
            $ketemu = DB::table('seri_cetakan')->where('no_seri', $no_seri)->count();
        }
        //$organisasi = DB::table('organisasi')->where('id_organisasi', $id_organisasi)->first();
        $url = env('RENDER_REPORT').'/render/'.$token."_".$no_seri;
        
        //return $url;
        
        $path = env('PATH_REPORT'); 
        $filename = $no_seri.'_report_sicerdas.pdf';
        $url_header = env('RENDER_REPORT').'/generate-kop/'.$quiz_sesi->id_quiz;
        $command = env('WKHTML').' --footer-spacing 3 -L 10 -R 10 -T 10  -B 20 --footer-left "Si Cerdas Indonesia"  --footer-right '.$no_seri.'  --footer-font-size 9 --header-html '. $url_header. ' --footer-center [page]/[topage] -O Portrait  -s Folio '.$url.' '.$path.$filename;
        return $command;
        //shell_exec($command);
        $process = new Process($command);
        $process->run();
        DB::table('seri_cetakan')->where('token', $token)->delete();
        $cetakan = array('no_seri'=>$no_seri,
                    'token'=>$token, 
                    'filename'=>$filename, 
                    'id_user'=>$quiz_sesi->id_user, 
                    'created_at'=>date('Y-m-d H:i:s'), 
                    'url'=>url('/report/'.$filename) );
        DB::table('seri_cetakan')->insert($cetakan);
    }


    function download_pdf_result_tes($token){
        $cek_token = DB::table('seri_cetakan')->where('token', $token)->first();
        if($cek_token){
                //return redirect($cek_token->url);
                $user = DB::table('users')->select('nama_pengguna')->where('id', $cek_token->id_user)->first();
                $filename = $user->nama_pengguna." - ".$cek_token->jenis_tes." - ".rand(10,99).$cek_token->no_seri.".pdf";
                $file = storage_path('report/'.$cek_token->filename);
                $headers = array(
                          'Content-Type: application/pdf',
                        );
                return Response::download($file, $filename, $headers);
        }
        return view('404');
    }

    function cek_file_seri($no_seri){
        $cek_token = DB::table('seri_cetakan')->where('no_seri', $no_seri)->first();
        if($cek_token){
                //return redirect($cek_token->url);
                $user = DB::table('users')->select('nama_pengguna')->where('id', $cek_token->id_user)->first();
                $filename = $user->nama_pengguna." - ".$cek_token->no_seri.".pdf";
                $file = storage_path('report/'.$cek_token->filename);
                $headers = array(
                          'Content-Type: application/pdf',
                        );
                return Response::download($file, $filename, $headers);
        }
        return view('404');
    }


    function generate_header($id_quiz){
        $quiz = DB::table('quiz_sesi')->where('id_quiz', $id_quiz)->first();
        $biro = DB::table('users')->where('id', $quiz->id_user_biro)->first();
        return view('report.kop', compact('biro'));
    }

    function download_pdf_no_seri($no_seri){
        try {
            $no_seri = decrypt($no_seri);
        } catch (DecryptException $e) {
            //
            echo "Not Found";
            exit();
        }
        $cek_token = DB::table('seri_cetakan')->where('no_seri', $no_seri)->first();
        if($cek_token){
                //return redirect($cek_token->url);
                //$user = DB::table('users')->select('nama_pengguna')->where('id', $cek_token->id_user)->first();
                $user = DB::table('users')->select('nama_pengguna', 'username')->where('id', $cek_token->id_user)->first();
                $username = $user->username;
                $username = str_replace(" ","", $username);
                $nama_peserta = $user->nama_pengguna;
                $nama_peserta = str_replace(" ","-",$nama_peserta);
                $nama_peserta = str_replace("`","",$nama_peserta);
                $nama_peserta = str_replace("\"","",$nama_peserta);
                $nama_peserta = str_replace("'","",$nama_peserta);
                $nama_peserta = str_replace("'","",$nama_peserta);
                $nama_peserta = str_replace(".","",$nama_peserta);
                $nama_peserta = str_replace(",","",$nama_peserta);
                $nama_peserta = strtoupper($nama_peserta);
                $nama_peserta = $nama_peserta."-".strtoupper($username);
                $filename =$nama_peserta."-".$cek_token->no_seri.".pdf";
                $file = storage_path('report/'.$cek_token->filename);
                $headers = array(
                          'Content-Type: application/pdf',
                        );
                return Response::download($file, $filename, $headers);
        }
        return view('404');
    }

    function generate_all_result_zip($id_quiz){
        $id_quiz = Crypt::decrypt($id_quiz);
        $quiz = DB::table('quiz_sesi')->where('id_quiz', $id_quiz)->first();
        $list_result = DB::select("SELECT a.no_seri, a.filename FROM seri_cetakan as a , quiz_sesi_user as b 
                                    where a.no_seri = b.no_seri and b.id_quiz = $id_quiz and b.status_hasil = 1 ");
        $files_list = array();
        foreach($list_result as $r){
            array_push($files_list, storage_path('report/'.$r->filename));
        }
        $no_seri = $quiz->tanggal."v".rand(1000,9999)."-PDF";
        $nama_file = (str_replace(" ","_",$quiz->nama_sesi));
        $nama_file = strtoupper($nama_file);
        $filename_zip = $nama_file.'-'.$no_seri.".zip";
        //Storage::delete(public_path('report/'.$filename_zip));
        // if($quiz->filename_report_zip !=""){
        //     if(file_exists(public_path('report/'.$quiz->filename_report_zip))){
        //         unlink(public_path('report/'.$quiz->filename_report_zip));
        //     }
        // }
        Zipper::make(public_path('report/'.$filename_zip))->add($files_list);
        DB::table('quiz_sesi')->where('id_quiz', $id_quiz)->update(['filename_report_zip'=>$filename_zip]);

        $respon = array('status'=>true,'data'=>true,'url'=>url("report/".$filename_zip));
        return response()->json($respon);
    }

    function generate_all_result_zip_doc($id_quiz){

        

        $id_quiz = Crypt::decrypt($id_quiz);
        $quiz = DB::table('quiz_sesi')->where('id_quiz', $id_quiz)->first();

        $file_template = public_path('template/report-word/'.$quiz->token.'.docx');
        if(file_exists($file_template)==false){
            $respon = array('status'=>false,'message'=>"Jenis Tes Tidak Support Untuk Format Document Word");
            return response()->json($respon);
        }
        
        $list_result = DB::select("SELECT a.no_seri, a.filename , a.pathname FROM seri_cetakan_doc as a , quiz_sesi_user as b 
                                    where a.no_seri = b.no_seri and b.id_quiz = $id_quiz and b.status_hasil = 1 ");
        $files_list = array();
        foreach($list_result as $r){
            array_push($files_list, public_path($r->pathname));
        }
        $no_seri = $quiz->tanggal."v".rand(1000,9999)."-DOCX";
        $nama_file = (str_replace(" ","_",$quiz->nama_sesi));
        $nama_file = strtoupper($nama_file);
        $filename_zip = $nama_file.'-'.$no_seri.".zip";
        //Storage::delete(public_path('report/'.$filename_zip));
        // if($quiz->filename_report_zip !=""){
        //     if(file_exists(public_path('report/'.$quiz->filename_report_zip))){
        //         unlink(public_path('report/'.$quiz->filename_report_zip));
        //     }
        // }
        Zipper::make(public_path('report/'.$filename_zip))->add($files_list);
        DB::table('quiz_sesi')->where('id_quiz', $id_quiz)->update(['filename_report_zip'=>$filename_zip]);

        $respon = array('status'=>true,'data'=>true,'url'=>url("report/".$filename_zip));
        return response()->json($respon);
    }
    
}
 