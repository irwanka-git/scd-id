<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;
use Hash;
use Auth;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Helpers\QuizConverter;
use App\Helpers\ExportWordReport;

class ManajemenSesiTesController extends Controller
{
        // TABEL: quiz_sesi

        // id_quiz          int
        // token            char
        // nama_sesi        varchar
        // lokasi           varchar
        // tanggal          varchar
        // open             int
        // gambar           varchar
        // skoring_tabel   varchar


        //TABEL: quiz_sesi_detil

        // id_quiz_sesi    int
        // id_quiz         int
        // id_sesi_master  int
        // urutan          int
        // durasi          int
        // kunci_waktu     int

        //TABEL: quiz_sesi_user

        // id_quiz_user        int
        // id_user             int
        // id_quiz             int
        // start_at            datetime
        // submit              int
        // submit_at           datetime
        // token_submit        varchar
        // jawaban             text
        // status_hasil        int



    private $id_role_peserta=7;

    function index(){
    	$pagetitle = "Manajemen Tes";
    	$smalltitle = "Data Manajemen Tes";
    	return view('manajemen-sesi.index', compact('pagetitle','smalltitle'));
    }

    function index_tahun(){
        loadHelper('akses,function');
        $id_role_biro = 5;
        $isAdminSistem = isAdminSistem();
        $isAdminBiro = isAdminBiro();
        $filter = "";
        if($isAdminBiro){
            $filter = " and id_user_biro = " .Auth::user()->id;
        }

        $pagetitle = "Manajemen Tes";
        $smalltitle = "Data Manajemen Tes";
        $list_tahun = DB::select("SELECT date_part( 'year', tanggal ) as tahun, 
            count(*) as jumlah_sesi 
            FROM quiz_sesi where id_quiz > 0 $filter
            GROUP BY date_part( 'year', tanggal )");
        return view('manajemen-sesi.index-tahun', compact('pagetitle','smalltitle', 'list_tahun'));
    }

    function index_biro($tahun){
        loadHelper('akses,function');
        $id_role_biro = 5;
        $isAdminSistem = isAdminSistem();
        $isAdminBiro = isAdminBiro();
        $filter = "";        
        $pagetitle = "Manajemen Tes";
        $smalltitle = "Data Manajemen Tes";
        $total_tahun = DB::select("select count(*) as jumlah 
                        from quiz_sesi where date_part( 'year', tanggal ) = '$tahun' and jenis = 'quiz'  ");

        $data_tahun['jumlah_tes'] = $total_tahun[0]->jumlah;
        $data_tahun['tahun'] = $tahun;
        $list_biro = DB::select("SELECT
                                    biro.id_user_biro,
                                    biro.uuid_user_biro,
                                    biro.nama_biro,
                                    MAX ( date_part( 'year', x.tanggal ) ) AS tahun,
                                    COUNT ( x.id_quiz ) AS jumlah_sesi 
                                FROM
                                    (
                                    SELECT A
                                        .nama_pengguna AS nama_biro,
                                        A.ID AS id_user_biro,
                                        A.uuid as uuid_user_biro
                                    FROM
                                        users AS A,
                                        user_role AS b 
                                    WHERE
                                        b.id_role = 5 
                                        AND A.ID = b.id_user 
                                    ) AS biro
                                    LEFT JOIN quiz_sesi AS x ON x.id_user_biro = biro.id_user_biro 
                                    AND date_part( 'year', x.tanggal ) = '$tahun' 
                                GROUP BY
                                    biro.id_user_biro,
                                    biro.uuid_user_biro,
                                    biro.nama_biro");

        return view('manajemen-sesi.index-biro', compact('pagetitle','smalltitle', 'list_biro', 'data_tahun'));
    }

    function index_provinsi($tahun,$id_user_biro){

        loadHelper('akses,function');
        $id_role_biro = 5;
        $isAdminSistem = isAdminSistem();
        $isAdminBiro = isAdminBiro();
        $filter = "";        
        $pagetitle = "Manajemen Tes";
        $smalltitle = "Data Manajemen Tes";
         
        $data_tahun['tahun'] = $tahun;

        $biro = DB::table('users')->where('uuid', $id_user_biro)->first();
        if(!$biro){
            return redirect('manajemen-sesi');
        }
        $id_user_biro = $biro->id;

        $total_biro = DB::select("select count(*) as jumlah 
                        from quiz_sesi where date_part( 'year', tanggal ) = '$tahun' and id_user_biro = $id_user_biro 
                        and jenis = 'quiz'  ");
        $data_biro['jumlah_tes'] = $total_biro[0]->jumlah;
        $data_biro['id_user_biro'] = $biro->uuid;

        $data_biro['nama_biro'] = $biro->nama_pengguna;

        $list_provinsi = DB::select("SELECT C.NAME AS provinsi, b.kode_provinsi,
                                        COUNT ( * ) AS jumlah_tes 
                                    FROM
                                        quiz_sesi AS A,
                                        lokasi AS b,
                                        provinces AS C 
                                    WHERE
                                        A.id_user_biro = '$id_user_biro' 
                                        AND date_part( 'year', A.tanggal ) = '$tahun' 
                                        AND A.id_lokasi = b.id_lokasi 
                                        AND C.ID = b.kode_provinsi 
                                    GROUP BY
                                        C.NAME,
                                        B.kode_provinsi");

        return view('manajemen-sesi.index-provinsi', compact('pagetitle','smalltitle', 'list_provinsi', 'data_tahun' ,'data_biro'));
    }

    function index_lokasi($tahun, $id_user_biro, $provinsi){
        loadHelper('akses,function');
        $id_role_biro = 5;
        $isAdminSistem = isAdminSistem();
        $isAdminBiro = isAdminBiro();
        $filter = "";        
        $pagetitle = "Manajemen Tes";
        $smalltitle = "Data Manajemen Tes";
         
        $data_tahun['tahun'] = $tahun;
        $biro = DB::table('users')->where('uuid', $id_user_biro)->first();
        if(!$biro){
            return redirect('manajemen-sesi');
        }
        $id_user_biro = $biro->id;
        $total_biro = DB::select("select count(*) as jumlah 
                        from quiz_sesi where date_part( 'year', tanggal ) = '$tahun' and id_user_biro = $id_user_biro 
                        and jenis = 'quiz'  ");

        $data_biro['id_user_biro'] = $biro->uuid;
        $data_biro['nama_biro'] = $biro->nama_pengguna;
        $data_biro['jumlah_tes'] = $total_biro[0]->jumlah;

        $data_provinsi = DB::table('provinces')->where('id', $provinsi)->first();
        if(!$data_provinsi){
            return redirect('manajemen-sesi');
        }
        $total_provinsi = DB::select("select count(*) as jumlah from quiz_sesi as a, lokasi as b 
                        where a.id_lokasi = b.id_lokasi and b.kode_provinsi = '$provinsi'
                        and a.id_user_biro = '$id_user_biro' and date_part('year',a.tanggal) = '$tahun'
                        and a.jenis = 'quiz' ");
        
        
        $data_provinsi->jumlah_tes = $total_provinsi[0]->jumlah;
        //return ($total_provinsi);
        $list_lokasi = DB::select("SELECT C.NAME AS kabupaten, b.nama_lokasi, b.uuid as id_lokasi,
                                        COUNT ( * ) AS jumlah_tes 
                                    FROM
                                        quiz_sesi AS A,
                                        lokasi AS b,
                                        regencies AS C
                                    WHERE
                                        A.id_user_biro = '$id_user_biro' 
                                        AND b.kode_provinsi  = '$provinsi'
                                        AND date_part( 'year', A.tanggal ) = '$tahun' 
                                        AND A.id_lokasi = b.id_lokasi 
                                        AND b.kode_kabupaten = c.id 
                                    GROUP BY
                                        C.NAME,
                                        b.nama_lokasi,
                                        B.uuid");
        if(count($list_lokasi)==0){
            return redirect('manajemen-sesi/explore/'.$tahun.'/'.$id_user_biro);
        }
        return view('manajemen-sesi.index-lokasi', compact('pagetitle','smalltitle', 'list_lokasi', 'data_tahun' ,'data_biro','data_provinsi'));
    }

    function index_tes($tahun, $id_user_biro, $provinsi, $lokasi){
        loadHelper('akses,function');
        $id_role_biro = 5;
        $isAdminSistem = isAdminSistem();
        $isAdminBiro = isAdminBiro();
        $filter = "";        
        $pagetitle = "Manajemen Tes";
        $smalltitle = "Data Manajemen Tes";
         

        $lokasi = DB::table('lokasi')->where('uuid', $lokasi)->first();
        if(!$lokasi){
            return redirect('manajemen-sesi');
        }
        $lokasi = $lokasi->id_lokasi;
        $data_tahun['tahun'] = $tahun;
        $biro = DB::table('users')->where('uuid', $id_user_biro)->first();
        if(!$biro){
            return redirect('manajemen-sesi');
        }
        $id_user_biro = $biro->id;
        $data_biro['id_user_biro'] = $biro->uuid;
        
        $total_biro = DB::select("select count(*) as jumlah 
                        from quiz_sesi where date_part( 'year', tanggal ) = '$tahun' and id_user_biro = $id_user_biro 
                        and jenis = 'quiz'  ");
        $data_biro['nama_biro'] = $biro->nama_pengguna;
        $data_biro['jumlah_tes'] = $total_biro[0]->jumlah;

        $data_provinsi = DB::table('provinces')->where('id', $provinsi)->first();
        if(!$data_provinsi){
            return redirect('manajemen-sesi');
        }
        $total_provinsi = DB::select("select count(*) as jumlah from quiz_sesi as a, lokasi as b 
                        where a.id_lokasi = b.id_lokasi and b.kode_provinsi = '$provinsi'
                        and a.id_user_biro = '$id_user_biro' and date_part('year',a.tanggal) = '$tahun'
                          ");
        
        
        $data_provinsi->jumlah_tes = $total_provinsi[0]->jumlah;
        $data_lokasi = DB::select("select a.*, b.name as kabupaten from lokasi as a, 
                regencies as b where a.kode_kabupaten = b.id and a.id_lokasi = $lokasi");
        if(count($data_lokasi)==0){
            return redirect('manajemen-sesi');
        }
        $data_lokasi = $data_lokasi[0];
        //return ($total_provinsi);
        $list_tes = DB::select("
            SELECT
                sesi.*,
                COUNT ( u.id_quiz_user ) AS peserta,
                sesi_detil.jumlah_sesi 
            FROM
                (
                SELECT A
                    .id_quiz,
                    A.uuid,
                    A.OPEN,
                    A.nama_sesi,
                    A.tanggal,
                    A.kota,
                    A.nama_asesor,
                    b.nama_lokasi,
                    C.nama_sesi AS jenis_tes,
                    C.gambar 
                FROM
                    quiz_sesi AS A,
                    lokasi AS b,
                    quiz_sesi_template AS C 
                WHERE
                    A.id_user_biro = '$id_user_biro' 
                    AND b.kode_provinsi = '$provinsi' 
                    AND date_part( 'year', A.tanggal ) = '$tahun' 
                    AND A.id_lokasi = b.id_lokasi 
                    AND b.id_lokasi = $lokasi 
                    AND C.id_quiz_template = A.id_quiz_template 
                ORDER BY
                    A.tanggal DESC 
                ) AS sesi
                LEFT JOIN ( SELECT id_quiz, COUNT ( * ) AS jumlah_sesi FROM quiz_sesi_detil GROUP BY id_quiz ) AS sesi_detil ON
                sesi.id_quiz = sesi_detil.id_quiz 
                LEFT JOIN quiz_sesi_user AS u ON sesi.id_quiz = u.id_quiz   
             
            GROUP BY
                sesi.id_quiz,
                sesi.uuid,
                sesi.OPEN,
                sesi.nama_sesi,
                sesi.tanggal,
                sesi.kota,
                sesi.nama_asesor,
                sesi.nama_lokasi,
                sesi.jenis_tes,
                sesi.gambar,
                sesi_detil.jumlah_sesi
                order by tanggal desc
            ");
        
        if(count($list_tes)==0){
            return redirect('manajemen-sesi/explore/'.$tahun.'/'.$id_user_biro);
        }
        return view('manajemen-sesi.index-tes', compact('pagetitle','smalltitle', 'list_tes','data_lokasi',
                'data_tahun' ,'data_biro','data_provinsi'));
    }

    function datatable(Request $r){
        loadHelper('akses');
        $filter = "";

        if(isAdminBiro()){
            $filter = " and a.id_user_biro = ". Auth::user()->id;
        }

        $cari =(int) request('cari');

        if($cari==1){
            $jenis = request('jenis');
            $biro = request('biro');
            $tahun = request('tahun');
            if($jenis!=""){
                $filter.= "and a.id_quiz_template = $jenis ";
            }
            if($biro!=""){
                $filter.= "and c.uuid = '$biro' ";
            }
            if($tahun!=""){
                $filter.= "and date_part('year', a.tanggal) = '$tahun' ";
            }
        }

        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=2){
                $keyword = strtolower($keyword);
                $filter .= " and (  lower(a.nama_sesi) like '%$keyword%' 
                            or lower(a.lokasi) like '%$keyword%'  
                            or lower(c.nama_pengguna) like '%$keyword%'  
                            or lower(a.token) like '%$keyword%'  ) ";
            }   
        }

        $sql_union = "select x.*, count(y.id_quiz_sesi) as jumlah_sesi 
                        from (SELECT
                            a.token,
                            a.nama_sesi,
                            a.lokasi,
                            a.kota,
                            a.tanggal,
                            a.open ,
                            a.uuid,
                            a.id_quiz,
                            c.nama_pengguna as nama_biro,
                            count(b.id_user) as jumlah_peserta
                        FROM
                            quiz_sesi AS a  
                            left join quiz_sesi_user as b on a.id_quiz = b.id_quiz 
                            left join users as c on a.id_user_biro = c.id 
                            where a.id_quiz > 0 $filter
                        group by a.id_quiz, c.nama_pengguna) as x 
                        left join quiz_sesi_detil as y 
                        on x.id_quiz = y.id_quiz
                        GROUP BY 
                        x.id_quiz, 
                        x.token,
                        x.nama_sesi,
                        x.open, 
                        x.tanggal,
                        x.kota,
                        x.lokasi, 
                        x.uuid, 
                        x.nama_biro,
                        x.jumlah_peserta
                        ";
        //return $sql_union;
         $query = DB::table(DB::raw("($sql_union) as x order by tanggal desc, id_quiz desc"))
                    ->select([
                        'id_quiz',
                        'nama_sesi',
                        'token',
                        'open',
                        'tanggal',
                        'kota',
                        'lokasi',
                        'uuid',
                        'nama_biro',
                        'jumlah_peserta',
                        'jumlah_sesi',
                    ]);

         return Datatables::of($query)
            ->addColumn('action', function ($query) {
                    $edit = ""; $delete = "";
                    $action = '';
                    if($this->ucu()){

                    $edit = '<button data-bs-tip="tooltip" data-bs-placement="top" data-bs-original-title="Ubah Data Sesi Tes"  data-bs-toggle="modal" data-uuid="'.$query->uuid.'" data-bs-target="#modal-edit" class="btn btn-light btn-sm" type="button"><ion-icon name="create-outline" btn></ion-icon></button>';
                    }
                    if($this->ucd()){
                        $delete = '<button data-bs-tip="tooltip" data-bs-placement="top" data-bs-original-title="Hapus Data Sesi Tes"   data-uuid="'.$query->uuid.'" class="btn btn-light btn-sm btn-konfirm-delete" type="button"><ion-icon name="trash-outline" btn></ion-icon></button>';
                    }
                    $action =  $action." ".$edit." ".$delete;
                    if ($action==""){return '<a href="#" class="act"><i class="la la-lock"></i></a>'; }
                    return '<div class="btn-group" role="group">'.$action.'</button>';
            })
            ->editColumn('nama_sesi', function($q){
                if($q->open==1){
                    $res = "<span class='badge bg-success'><i class='las la-play'></i></span>";
                }else{
                     $res = "<span class='badge bg-warning'><i class='la la-lock'></i></span>";
                }
                $res .=  " <a href='".url('manajemen-sesi/detil/'.Crypt::encrypt($q->id_quiz))."'>". $q->nama_sesi ."</a>";
                return $res;
            })
            ->editColumn('tanggal', function($q){
                loadHelper('function');
                return toDateDisplay2($q->tanggal);
            })
            ->addIndexColumn()
            ->rawColumns(['action','nama_sesi', 'open'])
            ->make(true);
    }

    function get_data($uuid){
    	$data = DB::table('quiz_sesi')->where('uuid', $uuid)->first();

        if($data){
            $data->template = DB::table('quiz_sesi_template')->where('id_quiz_template', $data->id_quiz_template)->first();
            $data->id_lokasi = DB::table('lokasi')->where('id_lokasi', $data->id_lokasi)->first()->uuid;
            $data->id_user_biro = DB::table('users')->where('id', $data->id_user_biro)->first()->uuid;
            $respon = array('status'=>true,'data'=>$data, 
            	'informasi'=>'Sesi Tes : '.$data->token." / ". $data->nama_sesi);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }

    function genTokenQuiz(){
        $token = rand(11111,99999);
        $ketemu = true;
        while($ketemu){
            $cek = DB::table('quiz_sesi')->where('token', $token)->first();
            if($cek){
                $ketemu = true;
                $token = rand(11111,99999);
            }else{
                $ketemu = false;
            }
        }
        
        return $token;
    }

    function submit_insert(Request $r){
        if($this->ucc()){
            loadHelper('format');
	    	$uuid = $this->genUUID();
             
            $token = $this->genTokenQuiz();
            $template = DB::table('quiz_sesi_template')->where('id_quiz_template', $r->id_quiz_template)->first();
            $user_biro = DB::table('users')->select('id')->where('uuid', $r->id_user_biro)->first();
            $lokasi = DB::table('lokasi')->select('id_lokasi')->where('uuid', $r->id_lokasi)->first();
	    	$record_quiz = array(                                              
                "nama_sesi"=>trim($r->nama_sesi),
                "id_lokasi"=>trim($lokasi->id_lokasi),
                "tanggal"=>trim($r->tanggal),
                "kota"=>trim($r->kota),
                "open"=>0,
                "jenis"=>$template->jenis,
                "token"=>$token,
                "skoring_tabel"=>$template->skoring_tabel,
                "gambar"=>$template->gambar,
                "id_quiz_template"=>$template->id_quiz_template,
                "id_user_biro"=>$user_biro->id,
	    		"uuid"=>$uuid,
            );

	    	DB::table('quiz_sesi')->insert($record_quiz);
            $id_quiz = DB::table('quiz_sesi')->where('uuid', $uuid)->first()->id_quiz;
            $template_sesi = DB::table('quiz_sesi_detil_template')->where('id_quiz_template', $template->id_quiz_template)->get();

            foreach($template_sesi as $t){
                $record_sesi = array(
                    'id_quiz'=>$id_quiz,
                    'id_sesi_master'=>$t->id_sesi_master,
                    'urutan'=>$t->urutan,
                    'durasi'=>$t->durasi,
                    'kunci_waktu'=>$t->kunci_waktu,
                );
                DB::table('quiz_sesi_detil')->insert($record_sesi);
            }

	    	$respon = array('status'=>true,'message'=>'Data Sesi Tes Berhasil Ditambahkan!');
        	return response()->json($respon);
    	}else{
    		$respon = array('status'=>false,'message'=>'Akses Ditolak!');
        	return response()->json($respon);
    	}
    }

    function submit_update(Request $r){
    	if($this->ucu()){
	    	loadHelper('format');
	    	$uuid = $r->uuid;
            // $pernyataan = str_replace("<p>", "", $r->pernyataan);
            // $pernyataan = str_replace("</p>", "", $pernyataan);
            //return $pernyataan;
	    	$record_quiz = array(                                              
                "nama_sesi"=>trim($r->nama_sesi),
                "lokasi"=>trim($r->lokasi),
                "kota"=>trim($r->kota),
                "tanggal"=>trim($r->tanggal)
            );

	    	DB::table('quiz_sesi')->where('uuid', $uuid)->update($record_quiz);
	    	$respon = array('status'=>true,'message'=>'Perubahan Data Sesi Tes Berhasil Disimpan!');
        	return response()->json($respon);
    	}else{
    		$respon = array('status'=>false,'message'=>'Akses Ditolak!');
        	return response()->json($respon);
    	}
    }

    function submit_update_info(Request $r){
        if($this->ucu()){
            loadHelper('akses,format');
            $uuid = $r->uuid;
            if(isAdminSistem()){
                $lokasi = DB::table('lokasi')->where('uuid', $r->id_lokasi)->first();
                $biro = DB::table('users')->where('uuid', $r->id_user_biro)->first();
                $record_quiz = array(                                              
                    "nama_sesi"=>trim($r->nama_sesi),
                    "id_user_biro"=>trim($biro->id),
                    "id_lokasi"=>trim($lokasi->id_lokasi),
                    "kota"=>trim($r->kota),
                    "tanggal"=>trim($r->tanggal),
                );
            }else{
                $lokasi = DB::table('lokasi')->where('uuid', $r->id_lokasi)->first();
                $record_quiz = array(                                              
                    "nama_sesi"=>trim($r->nama_sesi),
                    "id_lokasi"=>trim($lokasi->id_lokasi),
                    "kota"=>trim($r->kota),
                    "tanggal"=>trim($r->tanggal),
                );
            }
            

            DB::table('quiz_sesi')->where('uuid', $uuid)->update($record_quiz);
            $respon = array('status'=>true,'message'=>'Perubahan Data Sesi Tes Berhasil Disimpan!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }

    function submit_update_asesor(Request $r){
        if($this->ucu()){
            loadHelper('format');
            $uuid = $r->uuid;
            $record_quiz = array(                                              
                "nama_asesor"=>trim($r->nama_asesor), 
                "nomor_sipp"=>trim($r->nomor_sipp), 
                "ttd_asesor"=>trim($r->ttd_asesor), 
            );

            DB::table('quiz_sesi')->where('uuid', $uuid)->update($record_quiz);
            $respon = array('status'=>true,'message'=>'Data Asesor Sesi Tes Berhasil Disimpan!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }

    function submit_update_status(Request $r){
        if($this->ucu()){
            loadHelper('format');
            $uuid = $r->uuid;
            
            $open = DB::table('quiz_sesi')->where('uuid', $uuid)->first()->open;
            if(!$open){
               $quiz_sesi =  DB::table('quiz_sesi')->where('uuid', $uuid)->first();
                if($quiz_sesi->json_url==""){
                    $respon = array('status'=>false,'message'=>'Soal Belum di Upload!');
                    return response()->json($respon);
                }
                $record_quiz = array(                                              
                    "open"=> $open == 1 ? 0 : 1,
                );
            }else{
                $record_quiz = array(                                              
                    "open"=> $open == 1 ? 0 : 1,
                );
            }
            
            $message = $open == 1 ?  'Sesi Tes Berhasil Ditutup' : 'Sesi Tes Berhasil Dibuka';
            DB::table('quiz_sesi')->where('uuid', $uuid)->update($record_quiz);
            $respon = array('status'=>true,'message'=>$message);
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }

    function submit_upload_soal_firebase(Request $r){
        if($this->ucu()){
            loadHelper('format');
            $uuid = $r->uuid;
            ini_set('max_execution_time', '1300'); //300 seconds = 5 minutes
            $quiz = DB::table('quiz_sesi')->where('uuid', $uuid)->first();
            $convert = QuizConverter::convert_quiz_json($quiz->token);
            $open = DB::table('quiz_sesi')->where('uuid', $uuid)->first()->open;
            $record_quiz = array(                                              
                    "json_url"=>$convert->url_plaintext,
                    "json_url_encrypt"=>"",
                );
            DB::table('quiz_sesi')->where('uuid', $uuid)->update($record_quiz);
            $respon = array('status'=>true,'message'=>"Berhasil Upload Soal ke Firebase");
            return response()->json($respon);
           
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }

    function submit_delete(Request $r){
        if($this->ucd()){
            loadHelper('format');
            $uuid = $r->uuid;
            $id_quiz = DB::table('quiz_sesi')->where('uuid', $uuid)->first()->id_quiz;
            DB::table('quiz_sesi')->where('uuid', $uuid)->delete();
            DB::table('quiz_sesi_detil')->where('id_quiz', $id_quiz)->delete();
            DB::table('quiz_sesi_user')->where('id_quiz', $id_quiz)->delete();
            $respon = array('status'=>true,'message'=>'Data Sesi Tes Berhasil Dihapus!');          
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }


    function submit_update_gambar(Request $r){
        if($this->ucu()){
            //loadHelper('format');
            $uuid = $r->uuid;
            $record_quiz = array(                                              
                "gambar"=>trim($r->gambar),
            );

            DB::table('quiz_sesi')->where('uuid', $uuid)->update($record_quiz);
            $respon = array('status'=>true,'message'=>'Gambar Sesi Tes Berhasil Diperbarui!');          
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }


    //detil sesi tes
    function detil($crypt_id){
        $pagetitle = "Detil Sesi Tes";
        $smalltitle = "Data Manajemen Tes";
        $id_quiz = Crypt::decrypt($crypt_id);
        //$quiz = DB::table('quiz_sesi')->where('id_quiz', $id_quiz)->first();
        $quiz = DB::select("select 
                    a.*, b.gambar as cover, c.nama_lokasi, d.name as kabupaten , b.nama_sesi as jenis_tes
                    from 
                    quiz_sesi as a, 
                    quiz_sesi_template as b ,
                    lokasi as c , 
                    regencies as d 
                    where a.id_lokasi = c.id_lokasi
                    and a.id_quiz_template = b.id_quiz_template
                    and c.kode_kabupaten = d.id
                    and a.id_quiz = '$id_quiz' ");
        if(count($quiz)==0){
            return view('404');
        }
        $quiz = $quiz[0];
        $id_quiz = $quiz->id_quiz;
        $quiz_sesi = DB::select("SELECT
                                c.nama_sesi_ujian,
                                c.kategori,
                                b.urutan, 
                                b.durasi,
                                b.kunci_waktu, 
                                c.mode, 
                                b.id_quiz_sesi
                            FROM
                                quiz_sesi AS a,
                                quiz_sesi_detil AS b,
                                quiz_sesi_master AS c
                            WHERE
                                a.id_quiz = b.id_quiz 
                                AND c.id_sesi_master = b.id_sesi_master 
                                AND a.id_quiz = $id_quiz
                            ORDER BY
                                b.urutan ASC");
        $template = DB::table('quiz_sesi_template')->where('id_quiz_template', $quiz->id_quiz_template)->first();
        $jumlah_peserta = DB::table('quiz_sesi_user')->where('id_quiz', $quiz->id_quiz)->count();
        $rekap_status_peserta = DB::select("select 
                                        sum(case when a.id_user > 0 then 1 else 0 end) as peserta,
                                        sum(case when a.submit=1 then 1 else 0 end) as submit, 
                                        sum(case when a.skoring=1 then 1 else 0 end) as skoring, 
                                        sum(case when a.status_hasil=1 then 1 else 0 end) as publish 
                                         from quiz_sesi_user as a 
                                        where a.id_quiz = $id_quiz");
        $rekap_status_peserta = $rekap_status_peserta[0];

        return view('manajemen-sesi.detil', compact('pagetitle','smalltitle','rekap_status_peserta', 'quiz','quiz_sesi','template','jumlah_peserta'));
    }


    function submit_update_waktu(Request $r){
         $id_quiz_sesi = $r->id_quiz_sesi;
         $durasi = $r->durasi;
         $kunci_waktu = $r->kunci_waktu;
         DB::table('quiz_sesi_detil')
                        ->where('id_quiz_sesi', $id_quiz_sesi)
                        ->update(['durasi'=>$durasi, 'kunci_waktu'=>$kunci_waktu]);
        $respon = array('status'=>true,'message'=>'Berhasil Update Waktu Sesi Tes ');
        return response()->json($respon);
    }


    function datatable_peserta($uuid){
        
        $quiz = DB::table('quiz_sesi')->select('id_quiz')->where('uuid', $uuid)->first();

        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=2){
                $keyword = strtolower($keyword);
                $filter = " and (  lower(a.username) like '%$keyword%' or lower(a.nama_pengguna) like '%$keyword%'  ) ";
            }   
        }
         $id_role_peserta = $this->id_role_peserta;
         $id_quiz = $quiz->id_quiz;
         $sql_union = "select x.* 
                        from (
                        select  a.uuid as id_user, a.avatar, 
                                a.nama_pengguna, a.username, a.organisasi, 
                                a.unit_organisasi,
                                c.uuid , c.submit, c.status_hasil, c.token_submit,
                                c.start_at, c.skoring, c.submit_at, c.skoring_at, c.no_seri
                        from users as a, 
                            user_role as b, 
                            quiz_sesi_user as c
                        where a.id = b.id_user 
                                and b.id_role  = $id_role_peserta 
                                and a.id  = c.id_user 
                                and c.id_quiz = $id_quiz 
                        $filter) as x ";
        //return $sql_union;
         $jumlah_peserta = DB::table('quiz_sesi_user')->where('id_quiz', $id_quiz)->count();
         $rekap_status_peserta = DB::select("select 
                                        sum(case when a.id_user > 0 then 1 else 0 end) as peserta,
                                        sum(case when a.submit=1 then 1 else 0 end) as submit, 
                                        sum(case when a.skoring=1 then 1 else 0 end) as skoring, 
                                        sum(case when a.status_hasil=1 then 1 else 0 end) as publish 
                                         from quiz_sesi_user as a 
                                        where a.id_quiz = $id_quiz");
        $rekap_status_peserta = $rekap_status_peserta[0];

         $query = DB::table(DB::raw("($sql_union) as z order by start_at desc, submit_at desc, skoring_at desc, username asc"))
                    ->select([
                        'uuid',
                        'username',
                        'avatar',
                        'nama_pengguna',
                        'organisasi',
                        'no_seri',
                        'skoring_at',
                        'unit_organisasi',
                        'submit',
                        'id_user',
                        'status_hasil',
                        'token_submit',
                        'start_at',
                        'skoring',
                    ]);

         return Datatables::of($query)
            ->addColumn('action', function ($query) {
                    $bisa_publish = $query->status_hasil > 0 ? true : false;
                    $boleh_comment = $query->skoring ? true : false;
                    $reset_delete  = "";
                    if($bisa_publish){
                        $publish = '<a href="'.url('download/'.Crypt::encrypt($query->no_seri)).'"
                                    data-bs-tip="tooltip" data-bs-placement="top" data-bs-original-title="Download Hasil Tes" 
                                        class="btn btn-light btn-sm"><ion-icon name="download-outline" btn></ion-icon></a>';
                    }else{
                        $publish =  '<button  disabled data-uuid="'.$query->uuid.'"  
                                        class="btn btn-light btn-sm" type="button"><ion-icon name="download-outline" btn></ion-icon></button>';
                         $reset_delete =  '<button data-bs-tip="tooltip" data-bs-placement="top" data-bs-original-title="Reset Sesi Tes Peserta"  data-uuid="'.$query->uuid.'" class="btn btn-reset-sesi-peserta 
                            btn-light btn-sm" type="button"><ion-icon name="repeat-outline"  btn></ion-icon></button>';

                        $reset_delete .=  '<button data-bs-tip="tooltip" data-bs-placement="top" data-bs-original-title="Hapus dari Daftar Peserta"  data-uuid="'.$query->uuid.'" class="btn btn-remove-peserta 
                            btn-light btn-sm" type="button"><ion-icon name="trash-outline"  btn></ion-icon></button>';
                    }

                    if($boleh_comment){
                        $comment =  '<button data-bs-tip="tooltip" data-bs-placement="top" data-bs-original-title="Lihat Hasil Tes" data-uuid="'.$query->uuid.'"  class="btn btn-view-result 
                                        btn-light btn-sm" type="button"><ion-icon name="document-text-outline" btn></ion-icon></button>';
                    }else{
                        $comment =  '<button  disabled data-uuid="'.$query->uuid.'"  class="btn 
                                    btn-light btn-sm" type="button"><ion-icon name="document-text-outline" btn></ion-icon></button>';
                    }

                    if($this->ucu()){
                        
                        if($boleh_comment){
                            $comment .=  '<button data-bs-tip="tooltip" data-bs-placement="top" data-bs-original-title="Input Saran / Rekomendasi" data-uuid="'.$query->uuid.'"  class="btn btn-comment-result 
                                        btn-light btn-sm" type="button"><ion-icon name="chatbox-outline" btn></ion-icon></button>';
                        }else{
                            $comment .=  '<button disabled data-uuid="'.$query->uuid.'"  class="btn 
                                        btn-light btn-sm" type="button"><ion-icon name="chatbox-outline" btn></ion-icon></button>';
                        }

                        return  '<div class="btn-group" role="group">'.$comment." ".$publish." ".$reset_delete.'</button>';
                    }
                    return '<a href="#" class="act"><i class="la la-lock"></i></a>';
            })
            ->addColumn('user', function ($q) {
                  return '<div class="d-flex align-items-start">
                            <img src="'.url("gambar/".$q->avatar).'" width="36" height="36" class="rounded-circle me-2">
                            <div class="info-user">
                            <small>'.$q->username.'</small>
                            <br><a class="btn-view-user" data-id="'.$q->id_user.'">'.$q->nama_pengguna.'</a>
                            </div>
                        </div>';
            })
            ->addColumn('departement', function ($q) {
                  return '<div class="info-user">
                            <small>'.$q->unit_organisasi.'</small>
                            <br>'.$q->organisasi.'
                            </div>';
            })
            ->editColumn('start_at', function($q){
                if($q->start_at){
                    return '<ion-icon gr name="checkmark-outline"></ion-icon>';
                }
                return '<ion-icon name="time-outline"></ion-icon>';
            })
            ->editColumn('skoring', function($q){
                if($q->skoring){
                    return '<ion-icon gr name="checkmark-outline"></ion-icon>';
                }
                return '<ion-icon name="time-outline"></ion-icon>';
            })
            ->editColumn('submit', function($q){
                if($q->submit){
                    return '<ion-icon gr name="checkmark-outline"></ion-icon>';
                }
                return '<ion-icon name="time-outline"></ion-icon>';
            })
            ->editColumn('publish', function($q){
                if($q->status_hasil==1){
                    return '<ion-icon gr name="checkmark-outline"></ion-icon>';
                }
                return '<ion-icon name="time-outline"></ion-icon>';
            })
            ->addIndexColumn()
            ->rawColumns(['action','user','start_at','skoring','submit','departement','publish'])
            ->with([
                    'jumlah_peserta' => $jumlah_peserta, 
                    'rekap_status_peserta'=>$rekap_status_peserta
               ])
            ->make(true);
    }

    function datatable_tambah_peserta($uuid){

        $quiz = DB::table('quiz_sesi')->select('id_quiz','id_user_biro')->where('uuid', $uuid)->first();

        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=2){
                $keyword = strtolower($keyword);
                $filter = " and (  lower(a.username) like '%$keyword%' or lower(a.nama_pengguna) like '%$keyword%'  ) ";
            }   
        }
         $id_user_biro = $quiz->id_user_biro;
         $id_role_peserta = $this->id_role_peserta;
         $id_quiz = $quiz->id_quiz;
         $sql_union = "select x.*, y.id_quiz_user 
                        from (select a.uuid as uuid_user, a.id as id_user, a.nama_pengguna, a.avatar,
                        a.username, a.unit_organisasi, a.organisasi, a.uuid 
                        from users as a, user_role as b 
                        where a.id = b.id_user and a.create_by=$id_user_biro and b.id_role  = $id_role_peserta $filter) as x
                        left join quiz_sesi_user as y on x.id_user = y.id_user and y.id_quiz = $id_quiz
                        where y.id_quiz_user is null  ";
        //return $sql_union;
         $query = DB::table(DB::raw("($sql_union) as z order by id_user asc"))
                    ->select([
                        'uuid_user',
                        'uuid',
                        'avatar',
                        'username',
                        'organisasi',
                        'unit_organisasi',
                        'nama_pengguna',
                    ]);

         return Datatables::of($query)
            ->addColumn('action', function ($query) {
                    $edit = ""; $delete = "";
                    $action = '';
                    if($this->ucu()){
                        $edit = '<button data-uuid="'.$query->uuid.'" class="btn-add-peserta btn btn-primary btn-sm" type="button"><i class="las la-plus"></i> Tambah</button>';
                    }
                    $action =  $action." ".$edit." ".$delete;
                    if ($action==""){$action='<a href="#" class="act"><i class="la la-lock"></i></a>'; }
                        return $action;
            })
            ->addColumn('user', function ($q) {
                  return '<div class="d-flex align-items-start">
                            <img src="'.url("gambar/".$q->avatar).'" width="36" height="36" class="rounded-circle me-2">
                            <div class="info-user">
                            <small>'.$q->username.'</small>
                            <br><a class="btn-view-user" data-id="'.$q->uuid_user.'">'.$q->nama_pengguna.'</a>
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
            ->rawColumns(['action','user','departement'])
            ->make(true);
    }

    function datatable_salin_peserta($uuid){

        $quiz = DB::table('quiz_sesi')->select('id_quiz','id_user_biro')->where('uuid', $uuid)->first();

        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=2){
                $keyword = strtolower($keyword);
                $filter = " and (  lower(a.nama_sesi) like '%$keyword%' or lower(b.nama_lokasi) like '%$keyword%'  ) ";
            }   
        }
         $id_user_biro = $quiz->id_user_biro;
         $id_role_peserta = $this->id_role_peserta;
         $id_quiz = $quiz->id_quiz;
         $sql_union = "SELECT A.uuid,
                        A.nama_sesi,
                        A.tanggal,
                        b.nama_lokasi,
                        A.jenis AS jenis_tes,
                        r.peserta 
                    FROM
                        quiz_sesi AS A,
                        lokasi AS b,
                        ( SELECT x.id_quiz, COUNT ( * ) AS peserta FROM 
                            quiz_sesi_user as x, quiz_sesi  as y WHERE y.id_user_biro = '$id_user_biro' and x.id_quiz = y.id_quiz
                         GROUP BY x.id_quiz ) AS r 
                    WHERE
                        A.id_lokasi = b.id_lokasi 
                        AND r.id_quiz = A.id_quiz 
                        AND A.id_quiz !=$id_quiz
                        AND A.id_user_biro = '$id_user_biro'  $filter  ";

        //return $sql_union;
         loadHelper('format');
         $query = DB::table(DB::raw("($sql_union) as z order by tanggal desc"))
                    ->select([
                        'uuid',
                        'nama_sesi',
                        'tanggal',
                        'nama_lokasi',
                        'jenis_tes',
                        'peserta',
                    ]);

         return Datatables::of($query)
            ->addColumn('action', function ($query) {
                    $edit = ""; $delete = "";
                    $action = '';
                    if($this->ucu()){
                        $edit = '<button data-tes="'.$query->nama_sesi.'"data-peserta="'.$query->peserta. '"'. 'data-uuid="'.$query->uuid.'" class="btn-salin-peserta-tes btn btn-primary btn-sm" type="button"><i class="las la-copy"></i> Salin</button>';
                    }
                    $action =  $action." ".$edit." ".$delete;
                    if ($action==""){$action='<a href="#" class="act"><i class="la la-lock"></i></a>'; }
                        return $action;
            })
             ->editColumn('jenis_tes', function($d){
                if($d->jenis_tes=='demo'){
                    return "<span class='badge bg-warning'>".$d->jenis_tes."</span>";
                }
                return "<span class='badge bg-success'>".$d->jenis_tes."</span>";
            })
            ->editColumn('tanggal', function($d){
                $bulan = array("0"=>"","1"=>'Jan', "2"=>'Feb', "3"=>'Mar', "4"=>"Apr", "5"=>'Mei', "6"=>'Jun', "7"=>'Jul', "8"=>
                    'Agu', "9"=>'Sep', "10"=>'Okt', "11"=>'Nov',"12"=>'Des');
                $tgl = explode("-",$d->tanggal);

                if(count($tgl)==3){
                    return $tgl[2]." ".$bulan[(int)$tgl[1]]." ".$tgl[0];
                }else{
                    return "";
                }
            })
            ->addIndexColumn()
            ->rawColumns(['action','jenis_tes'])
            ->make(true);
    }

    function submit_upload_peserta(Request $request){

        if($this->ucu()){
            ini_set('max_execution_time', '300'); //300 seconds = 5 minutes

            $quiz = DB::table('quiz_sesi')->select('id_quiz')->where('uuid', $request->uuid)->first();
            $id_quiz = $quiz->id_quiz;

            $token = $request->token_upload_excel;
            $data1 = DB::table('user_upload_excel')
                    ->where('token', $token)
                    ->where('valid', 1)  //ambil yang valid aja 
                    ->get();
            //daftarkan akun dan tambahkan ke daftar peserta
            foreach ($data1 as $r){

                $uuid = $this->genUUID();
                $record_user = array(
                    "username"=>trim($r->username), 
                    "nama_pengguna"=>trim($r->nama_pengguna),
                    "organisasi"=>trim($r->organisasi),
                    "unit_organisasi"=>trim($r->unit_organisasi),
                    "jenis_kelamin"=>trim($r->jenis_kelamin),
                    "password"=>Hash::make(trim($r->password)),
                    "email"=>trim($r->email),
                    "telp"=>trim($r->telp),
                    "uuid"=>$uuid,
                    "created_at"=>date('Y-m-d H:i:s'),
                    "create_by"=>Auth::user()->id,
                    "token_upload"=>$token,
                    "avatar"=>'user.png'
                    );

                $insert_user = DB::table('users')->insert($record_user);
                if($insert_user){
                    $user_last = DB::table('users')->select('id')->where('uuid',$uuid)->first();
                    $id_user = $user_last->id;
                    $record_role = array(
                        'id_user'=>$id_user,
                        'id_role'=>$this->id_role_peserta,
                        'uuid'=>$uuid
                    );
                    $insert_role = DB::table('user_role')->insert($record_role);

                    if($insert_role){
                        $record_quiz_user = array('id_user'=>$id_user, 'id_quiz'=>$id_quiz, 'uuid'=>$uuid);
                        DB::table('quiz_sesi_user')->insert($record_quiz_user);
                    }
                }
            }


            //tambahkan akun ke daftar peserta
            $data2 = DB::table('user_upload_excel')
                    ->where('token', $token)
                    ->where('valid', 2)  //
                    ->get();

            foreach ($data2 as $r){
                 $uuid = $this->genUUID();
                $user_last = DB::table('users')->select('id')->where('username',trim($r->username))->first();
                if($user_last){
                    $id_user = $user_last->id;
                    $record_quiz_user = array('id_user'=>$id_user, 'id_quiz'=>$id_quiz, 'uuid'=>$uuid);
                    DB::table('quiz_sesi_user')->insert($record_quiz_user);
                }
            }

            DB::table('user_upload_excel')
                    ->where('token', $token)
                    ->delete();
            $respon = array('status'=>true,'message'=> count($data1) + count($data2).' Peserta Berhasil Ditambahkan!');          
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
        
    }

    function submit_delete_peserta(Request $r){
        if($this->ucu()){
            $peserta = DB::table('quiz_sesi_user')->select('id_user','id_quiz','uuid')->where('uuid', $r->uuid)->first();
            DB::table('quiz_sesi_user')->where('uuid', $peserta->uuid)->delete();
            DB::table('quiz_sesi_user_jawaban')
                    ->where('id_quiz', $peserta->id_quiz)
                    ->where('id_user', $peserta->id_user)
                    ->delete();
            $respon = array('status'=>true,'message'=>'Akun Berhasil Dihapus dari Daftar Peserta!');          
            return response()->json($respon);

        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }

    function get_data_peserta($uuid){
        $peserta = DB::table('quiz_sesi_user')->select('id_user')->where('uuid', $uuid)->first();
        if($peserta){
            $data= DB::table('users')
                        ->select('username','nama_pengguna','uuid')
                        ->where('id', $peserta->id_user)->first();
            $respon = array('status'=>true,'data'=>$data, 
                'informasi'=>$data->username." - ". $data->nama_pengguna);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }

    function get_info_user($uuid){
        $user = DB::table('users')->where('uuid', $uuid)->first();
        return view('manajemen-sesi.info-user', compact('user'));
    }

    function generate_tabel_tambah_peserta(){
        return view('manajemen-sesi.tabel-tambah-peserta');
    }

    function generate_tabel_salin_peserta(){
        return view('manajemen-sesi.tabel-salin-peserta');
    }

    function submit_insert_peserta(Request $r){
        if($this->ucu()){

            $quiz = DB::table('quiz_sesi')->select('id_quiz')->where('uuid', $r->uuid_quiz)->first();
            $user = DB::table('users')->select('id')->where('uuid', $r->uuid_user)->first();            
            $uuid = $this->genUUID();
            $record_quiz_user = array('id_user'=>$user->id, 'id_quiz'=>$quiz->id_quiz, 'uuid'=>$uuid);
            DB::table('quiz_sesi_user')->insert($record_quiz_user);
            $respon = array('status'=>true,'message'=>'Akun Berhasil Ditambahkan Sebagai Peserta!');          
            return response()->json($respon);

        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }

    function submit_salin_peserta(Request $r){
        if($this->ucu()){

            $quiz_src = DB::table('quiz_sesi')->select('id_quiz')->where('uuid', $r->uuid_src)->first();
            $quiz_dst = DB::table('quiz_sesi')->select('id_quiz')->where('uuid', $r->uuid_dst)->first();
            $list_user = DB::table('quiz_sesi_user')
                                        ->select('id_user')
                                        ->where('id_quiz', $quiz_src->id_quiz)->get();
            $berhasil = 0;
            foreach($list_user as $l){
                $ada = DB::table('quiz_sesi_user')
                                        ->select('id_user')
                                        ->where('id_quiz', $quiz_dst->id_quiz)
                                        ->where('id_user', $l->id_user)
                                        ->first();
                if(!$ada){
                    $uuid = $this->genUUID();
                    $record_quiz_user = array(
                        'id_user'=>$l->id_user, 
                        'id_quiz'=>$quiz_dst->id_quiz, 
                        'uuid'=>$uuid);
                    DB::table('quiz_sesi_user')->insert($record_quiz_user);
                    $berhasil++;
                }
            }
            
            $respon = array('status'=>true,'message'=>$berhasil .' Peserta Berhasil Ditambahkan Sebagai Peserta!');          
            return response()->json($respon);

        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }

    function submit_clear_peserta(Request $r){
        if($this->ucu()){
            $quiz = DB::table('quiz_sesi')->select('id_quiz')->where('uuid', $r->uuid_quiz)->first();
            DB::table('quiz_sesi_user')->where('id_quiz', $quiz->id_quiz)->delete();
            DB::table('quiz_sesi_user_jawaban')->where('id_quiz', $quiz->id_quiz)->delete();
            $respon = array('status'=>true,'message'=>'Daftar Peserta Berhasil Dikosongkan!');          
            return response()->json($respon);

        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }

    function view_result_tes($uuid){
        //$quiz_sesi_user = DB::table('quiz_sesi_user')->where('uuid', $uuid)->first();
        $data_sesi = DB::select("select 
                                a.id_quiz, 
                                a.id_user, 
                                a.jawaban_skoring, 
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
                                b.lokasi
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
            //dd($data_skoring);
            return view('report.'.$skoring_tabel.'.view', compact('data_sesi','data_skoring') );
        }
    }

    function get_saran_hasil_tes($uuid){
        $saran = DB::table('quiz_sesi_user')->select('saran')->where('uuid', $uuid)->first();
        $respon = array('status'=>true,'saran'=>$saran->saran);          
        return response()->json($respon);
    }

    function update_saran(Request $r){
        $uuid = $r->uuid;
        $saran = $r->saran;
        $status_hasil = $r->publish;

        if($status_hasil==1){
             $this->generate_pdf_report_peserta($uuid);
        }

        DB::table('quiz_sesi_user')->where('uuid', $uuid)->update(['saran'=>$saran,'status_hasil'=>$status_hasil]);
        $respon = array('status'=>true,'message'=>'Saran / Rekomendasi Berhasil Disimpan!');          
        return response()->json($respon);
    }

    function submit_batal_publish(Request $r){
        $uuid = $r->uuid;
        $saran = $r->saran;
        DB::table('quiz_sesi_user')->where('uuid', $uuid)->update(['status_hasil'=>0]);

        $quiz_sesi =DB::table('quiz_sesi_user')
                        ->select('id_user','token_submit')
                        ->where('uuid', $uuid)
                        ->first();
        DB::table('seri_cetakan')->where('token', $quiz_sesi->token_submit)->delete();               
        $respon = array('status'=>true,'message'=>'Saran / Rekomendasi Berhasil Disimpan!');          
        return response()->json($respon);
    }

    

    function generate_pdf_report_peserta($uuid){

        $quiz_sesi =DB::table('quiz_sesi_user')
                        ->select('id_user','token_submit','id_quiz','id_quiz_user')
                        ->where('uuid', $uuid)
                        ->first();
        $quiz = DB::table('quiz_sesi')->where('id_quiz', $quiz_sesi->id_quiz)->first();

        $token = $quiz_sesi->token_submit;
        $user = DB::table('users')->select('nama_pengguna', 'username')->where('id', $quiz_sesi->id_user)->first();
        $username = $user->username;
        $username = str_replace(" ","", $username);
        $nama_peserta = strtoupper($user->nama_pengguna);
        $nama_peserta = str_replace(" ","-",$nama_peserta);
        $nama_peserta = str_replace("`","",$nama_peserta);
        $nama_peserta = str_replace("\"","",$nama_peserta);
        $nama_peserta = str_replace("'","",$nama_peserta);
        $nama_peserta = str_replace("'","",$nama_peserta);
        $nama_peserta = str_replace(".","",$nama_peserta);
        $nama_peserta = str_replace(",","",$nama_peserta);
        $nama_peserta = strtoupper($nama_peserta)."-".strtoupper($username);
        $ketemu = 1;
        while($ketemu){
            $no_seri = rand(100,999).rand(100,999).rand(100,999);
            $ketemu = DB::table('seri_cetakan')->where('no_seri', $no_seri)->count();
        }
        //GENERATE COVER
       $path_cover = env('PATH_COVER'); 
       $command = "python3 ".env('PYTHON_SCRIPT')." ".$quiz_sesi->id_quiz_user;
       // return $command;
       $process = new Process($command);
       $process->run();
       $file_cover = $path_cover.$quiz_sesi->id_quiz_user.".pdf";

        //GENERATE REPORT
        $url = env('RENDER_REPORT').'/render/'.$token."_".$no_seri;
        $path = env('PATH_REPORT'); 
        $filename = $nama_peserta.'-'.$no_seri.'.pdf';
        $command = env('WKHTML').' --footer-spacing 3 -L 10 -R 10 -T 10  -B 20 --footer-left "Si Cerdas Indonesia"  --footer-right '.$no_seri.'  --footer-font-size 9 --footer-center [page]/[topage] -O Portrait  -s Folio '.$url.' '.$path.$filename;
        
       // return $command;
        // exit();
        
        $process = new Process($command);
        $process->run();


        //GENERATE AND MERGE LAMPIRAN
        //khusus penjurusan kuliah ada lampiran (landscape)
        if($quiz->skoring_tabel=='skoring_penjurusan_kuliah' 
            || $quiz->skoring_tabel=='skoring_penjurusan_kuliah_v2' 
            || $quiz->skoring_tabel=='skoring_minat_lengkap'
            || $quiz->skoring_tabel=='skoring_minat_sma_v2'
            || $quiz->skoring_tabel=='skoring_minat_man_v2'
            || $quiz->skoring_tabel=='skoring_minat_smk_v2'
        ){
            $filename_awal = $filename;
            $filename = $nama_peserta.'-'.$no_seri.'report.pdf';
            $url = env('RENDER_REPORT').'/render-lampiran-pmk/'.$token."_".$no_seri;
            $path = env('PATH_REPORT'); 
            $filename_lampiran = $no_seri.'_report_sicerdas_lampiran.pdf';
            $command = env('WKHTML').' --footer-spacing 3 -L 10 -R 10 -T 10  -B 20 --footer-left "Si Cerdas Indonesia"  --footer-right '.$no_seri.'  --footer-font-size 9 --footer-center [page]/[topage] -O Landscape  -s Folio '.$url.' '.$path.$filename_lampiran;
            $process = new Process($command);
            $process->run();

             //gabungkan file pdf
             //pake PDFUNITE https://www.sbarjatiya.com/notes_wiki/index.php/CentOS_7.x_pdfunite
             //pdfunite 839831277_report_sicerdas.pdf 606349316_report_sicerdas.pdf merge.pdf
            //pake cover
            $command = "pdfunite ".$file_cover." ".$path.$filename_awal." ".$path.$filename_lampiran." ".$path.$filename;
            //gak pake vover
           // $command = "pdfunite ". $path.$filename_awal." ".$path.$filename_lampiran." ".$path.$filename;
            //echo  $command;
            $process = new Process($command);
            $process->run();
        }else{
            //tanpa lampiran landscape
            $filename_awal = $filename;
            $filename = $nama_peserta.'-'.$no_seri.'report.pdf';
            $command = "pdfunite ".$file_cover." ".$path.$filename_awal." ".$path.$filename;
            //echo  $command;
            // echo  $command;
            // exit();
            $process = new Process($command);
            $process->run();
        }

        $jenis_tes = str_replace('skoring_','',$quiz->skoring_tabel);
        $jenis_tes = str_replace('_',' ',$jenis_tes);
        DB::table('seri_cetakan')->where('token', $token)->delete();
        $cetakan = array('no_seri'=>$no_seri,
                    'token'=>$token, 
                    'filename'=>$filename, 
                    'jenis_tes'=>$jenis_tes,
                    'id_user'=>$quiz_sesi->id_user, 
                    'created_at'=>date('Y-m-d H:i:s'), 
                    'url'=>url('/report/'.$filename) );
        
        DB::table('quiz_sesi_user')
                        ->where('uuid', $uuid)
                        ->update(['no_seri'=>$no_seri]);

        DB::table('seri_cetakan')->insert($cetakan);
        //tambahan export word report
        //dd($cetakan);
        ExportWordReport::GenerateReportWordQuizPeserta($quiz_sesi->id_quiz, $quiz_sesi->id_user);
    }


    function view_input_rekom($uuid){
        $data_sesi = DB::select("select 
                                a.id_quiz,
                                a.uuid, 
                                a.id_user, 
                                a.jawaban_skoring, 
                                b.skoring_tabel, 
                                c.username, 
                                c.nama_pengguna, 
                                a.status_hasil,
                                a.saran,
                                c.avatar, 
                                c.jenis_kelamin, 
                                c.unit_organisasi, 
                                c.organisasi,
                                b.nama_sesi, 
                                b.tanggal, 
                                b.lokasi
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
            return view('report.'.$skoring_tabel.'.input-rekom', compact('data_sesi','data_skoring') );
        }
    }

    function batal_publish_all_peserta(Request $r){
        $uuid = $r->uuid;
        $quiz = DB::table('quiz_sesi')->where('uuid', $uuid)->first();

        DB::table('quiz_sesi_user')->where('id_quiz', $quiz->id_quiz)->update(['status_hasil'=>0]);

        $respon = array('status'=>true,'message'=>'Berhasil Batalkan Publish Hasil Tes Peserta');
        return response()->json($respon);
    }

    function batal_skoring_all_peserta(Request $r){
        $uuid = $r->uuid;
        $quiz = DB::table('quiz_sesi')->where('uuid', $uuid)->first();
        $table_skoring = $quiz->skoring_tabel;
        DB::table('quiz_sesi_user')->where('id_quiz', $quiz->id_quiz)->update(['skoring'=>0,'skoring_at'=>null,'status_hasil'=>0]);
        DB::table($table_skoring)->where('id_quiz', $quiz->id_quiz)->delete();

        $respon = array('status'=>true,'message'=>'Berhasil Batalkan Skoring Hasil Tes Peserta');
        return response()->json($respon);
    }

    function publish_all_peserta(Request $r){
        ini_set('max_execution_time', 5000); 
        $uuid = $r->uuid;
        $quiz = DB::table('quiz_sesi')->where('uuid', $uuid)->first();

        $peserta_quiz = DB::table('quiz_sesi_user')
                        ->select('uuid','id_quiz_user')
                        ->where('id_quiz', $quiz->id_quiz)
                        ->where('skoring', 1)
                        ->where('status_hasil', 0)
                        ->limit(100)
                        ->get();
        foreach ($peserta_quiz as $p){
            DB::table('quiz_sesi_user')->where('id_quiz_user', $p->id_quiz_user)->update(['status_hasil'=>1]);

           $this->generate_pdf_report_peserta($p->uuid);
        }
        
        $respon = array('status'=>true,'message'=>'Berhasil Publish Hasil Tes '. count($peserta_quiz)." Peserta");
        return response()->json($respon);
    }

    function submit_reset_peserta(Request $r){
        if($this->ucu()){
            loadHelper('format');
            $uuid = $r->uuid;
            $peserta = DB::table('quiz_sesi_user')->select('id_user','id_quiz','uuid')->where('uuid', $uuid)->first();
            $quiz = DB::table('quiz_sesi')->where('id_quiz', $peserta->id_quiz)->first();
            $id_quiz = $peserta->id_quiz;
            $skoring_tabel = $quiz->skoring_tabel;
            //DB::table('quiz_sesi')->where('uuid', $uuid)->delete();
            //DB::table('quiz_sesi_detil')->where('id_quiz', $id_quiz)->delete();
            $reset = array(
                 'start_at'=>null,
                 'submit'=>0,
                 'submit_at'=>null,
                 'skoring'=>0,
                 'skoring_at'=>null,
                 'status_hasil'=>0,
                 'token_submit'=>null,
                 'jawaban_skoring'=>null,
                 'saran'=>null,
                 'no_seri'=>null,
                );
            DB::table('quiz_sesi_user')->where('uuid', $uuid)->update($reset);
            DB::table('quiz_sesi_user_jawaban')
                    ->where('id_quiz', $peserta->id_quiz)
                    ->where('id_user', $peserta->id_user)
                    ->delete();
            if($skoring_tabel !='non_skoring'){
                 DB::table($skoring_tabel)->where('id_quiz', $peserta->id_quiz)
                    ->where('id_user', $peserta->id_user)
                    ->delete();
            }
            $respon = array('status'=>true,'message'=>'Sesi Tes Peserta Berhasil Di-Reset!');          
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
}
