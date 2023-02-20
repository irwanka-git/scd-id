<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;

class ApiDataController  extends ApiController
{
	//MODE SOAL
	//PG  PILIHAN GANDA
	//RT  RATING (SIKAP)
	//TOP 
	//TOP

	function list_data(){
		$data = DB::table('data')->get();
		$respon = array('status'=>true,'data'=>$data);
		return $this->respon_json($respon);
	}

	function post_data(Request $r){
		$nama = $r->nama;
		$keterangan = $r->keterangan;
		DB::table('data')->insert(['nama'=>$nama, 'keterangan'=>$keterangan]);
		$respon = array('status'=>true,'data'=>true,'message'=>'Data Berhasil Ditambahkan!');
		return $this->respon_json($respon);
	}

	//SUBMIT JAWABAN 
	function submit_quiz(Request $r){
		$user = $this->get_user();
		$id_user = $user->id;
		if(!$user){
			$respon = array('status'=>false,'message'=>'Akses Tidak Valid');
			return $this->respon_json($respon);
		}

		$token_quiz = $r->token_quiz;
		$sesi = $r->sesi;
			
		if(!$sesi){
			$respon = array('status'=>false,
					'data'=>null,
					'message'=>'Jawaban Belum Lengkap!'
					);
			return $this->respon_json($respon);
		}	

		$quiz_user = DB::select("select a.id_quiz_user 
					from quiz_sesi_user as a, quiz_sesi as b 
					where a.id_quiz = b.id_quiz and 
					a.id_user = $id_user and b.token = '$token_quiz'
					and b.open = 1");

		if(count($quiz_user)==0){
			$respon = array('status'=>false,'message'=>'Akses Tidak Valid');
			return $this->respon_json($respon);
		}

		
		$submit_at = date('Y-m-d H:i:s');
		$id_quiz_user = $quiz_user[0]->id_quiz_user;
		$token_submit = Crypt::encrypt($id_quiz_user);

		DB::table('quiz_sesi_user')
			->where('id_quiz_user', $id_quiz_user)
			->update([
						'submit'=>1, 
						'submit_at'=>$submit_at,
						'jawaban'=>$sesi,
						'skoring'=>0,
						'token_submit'=>$token_submit,
						'status_hasil'=>0
					]);

		$data =['submit_at'=>$submit_at,'token_submit'=>$token_submit];	
		$respon = array('status'=>true,
						'data'=>$data,
						'message'=>'Jawaban Berhasil Dikirim'
						);
		return $this->respon_json($respon);
	}


	//salam pembuka 

	function get_salam_pembuka($token){
		$cek = DB::select("SELECT c.salam_pembuka FROM
							quiz_sesi AS a , 
							quiz_sesi_template as b , 
							quiz_template_saran as c 
							where a.id_quiz_template = b.id_quiz_template and c.skoring_tabel = b.skoring_tabel and a.token = '$token' ");
		if(count($cek)==1){
			$salam_pembuka = $cek[0]->salam_pembuka;
		}else{
			$salam_pembuka = "Selamat Datang di Versi Demo Tes Si Cerdas, Mohon untuk mengikuti semua petunjuk pengisian soal,  Klik Lanjutkan Untuk Memulai!";
		}


		date_default_timezone_set("Asia/Jakarta");
		$jam = date('H:i');

		if($jam > '00:01' && $jam < '10:00') {
		  $salam = 'Pagi';
		}elseif($jam >= '10:00' && $jam < '15:00') {
		  $salam = 'Siang';
		}elseif($jam < '18:00') {
		  $salam = 'Sore';
		}else{
		  $salam = 'Malam';
		}

		$salam_pembuka = str_replace("#_waktu_#", $salam, $salam_pembuka);
		$respon = array('status'=>true,'message'=>$salam_pembuka);
		return $this->respon_json($respon);
	}

	

	//infor cerdas
	function get_list_info_cerdas(){
 		loadHelper('function');
		$list_info = DB::select("SELECT
									a.* 
								FROM info_cerdas as a order by a.created_at asc limit 10");
		$data = array();

		foreach($list_info as $l){

			$temp = [
					'uuid'=>$l->uuid, 
					'judul'=>$l->judul, 
					'url'=>$l->url,
					'created_at'=>tgl_indo_lengkap($l->created_at), 
					'gambar'=>url('gambar/'.$l->gambar),
					];

			array_push($data, $temp);
		}
		//var_dump($data);

		$respon = array('status'=>true,'data'=>$data);
		return $this->respon_json($respon);
	}

	function get_detil_info_cerdas($uuid){
		//echo $uuid;
		loadHelper('function');
		$data = DB::table('info_cerdas')->where('uuid', $uuid)->first();
		//var_dump($data);
		$data->created_at = tgl_indo_lengkap($data->created_at);
		$data->gambar = url('gambar/'.$data->gambar);

		$respon = array('status'=>true,'data'=>$data);
		return $this->respon_json($respon);
	}


	function get_quiz_user(){
		$user = $this->get_user();
		if(!$user){
			$respon = array('status'=>false,'message'=>'Akses Tidak Valid');
			return $this->respon_json($respon);
		} 

		$id_user = $user->id;
		$uuid_user = $user->uuid;
		$list_quiz = DB::select("SELECT
									a.*, c.nama_lokasi, b.submit, b.status_hasil, b.token_submit, b.no_seri
								FROM
									quiz_sesi AS a,
									quiz_sesi_user AS b, 
									lokasi as c 
								WHERE
									a.id_quiz = b.id_quiz 
									and a.id_lokasi = c.id_lokasi
									AND b.id_user = $id_user order by a.tanggal desc, 
										nama_sesi asc");
		$data = array();
		loadHelper('function');
		
		foreach($list_quiz as $l){
			$tanggal = tgl_indo_lengkap($l->tanggal);
			if($tanggal==""){
				$tanggal = "Online";
			}
			$temp = [
					'token'=>$l->token, 
					'nama_sesi'=>$l->nama_sesi, 
					'lokasi'=>$l->nama_lokasi, 
					'tanggal'=>$tanggal, 
					'gambar'=>url('gambar/'.$l->gambar),
					'submit'=>$l->submit,
					'status_hasil'=>$l->status_hasil,
					'uuid_user'=>$uuid_user,
					'open'=>$l->open,
					'url_result'=>url('download/'.Crypt::encrypt($l->no_seri))
					];

			array_push($data, $temp);
		}
		//var_dump($data);

		$respon = array('status'=>true,'data'=>$data);
		return $this->respon_json($respon);
	}

	function get_detil_quiz($token){
		loadHelper('function');
		$user = $this->get_user();
		if(!$user){
			$respon = array('status'=>false,'message'=>'Akses Tidak Valid');
			return $this->respon_json($respon);
		}
		$id_user = $user->id;

		$quiz = DB::select("SELECT
								a.*, e.nama_lokasi, d.submit, d.submit_at, d.token_submit, d.status_hasil, d.no_seri
							FROM
								quiz_sesi AS a,
								quiz_sesi_user as d, 
								lokasi as e 
							WHERE
								d.id_quiz = a.id_quiz
								and e.id_lokasi  = a.id_lokasi
								and d.id_user = $id_user 
								AND a.token = '$token'
							");

		if(count($quiz)==0){
			$respon = array('status'=>false,'data'=>null, 'message'=>'Token Tidak Valid!');
			return $this->respon_json($respon);
		}
		$quiz = $quiz[0];
		//var_dump($quiz);
		$sesi = DB::select("SELECT
								c.nama_sesi_ujian,
								c.kategori,
								b.urutan, 
								b.durasi,
								b.kunci_waktu
							FROM
								quiz_sesi AS a,
								quiz_sesi_detil AS b,
								quiz_sesi_master AS c, 
								quiz_sesi_user as d 
							WHERE
								a.id_quiz = b.id_quiz 
								and d.id_quiz = a.id_quiz
								and d.id_user = $id_user 
								AND c.id_sesi_master = b.id_sesi_master 
								AND a.token = '$token'
							ORDER BY
								b.urutan ASC");
		$url_result = '';

		if($quiz->submit==1){
			$url_result = url('download/'.$quiz->no_seri);
		}

		$tanggal = tgl_indo_lengkap($quiz->tanggal);
		if($tanggal==""){
			$tanggal = "Online";
		}

		$data_quiz = [
				'nama_sesi'=>$quiz->nama_sesi,
				'lokasi'=>$quiz->nama_lokasi,
				'tanggal'=>$tanggal,
				'token'=>$quiz->token,
				'open'=>$quiz->open,
				'gambar'=>url('gambar/'.$quiz->gambar),
				'submit'=>$quiz->submit,
				'submit_at'=>$quiz->submit_at,
				'sesi'=>$sesi,
				'url_result'=>$url_result,
				'status_hasil'=>$quiz->status_hasil
				];
		// $sesi = DB::select($sql_sesi);
		// var_dump($sesi);
		$respon = array(
					'status'=>true,
					'data'=>$data_quiz
					);
		return $this->respon_json($respon);
	}

	function get_config_sesi($token){
		$user = $this->get_user();
		$quiz = DB::table('quiz_sesi')->where('token', $token)->first();
		if(!$quiz){
			$respon = array('status'=>false,'data'=>null, 'message'=>'Token Tidak Valid!');
			return $this->respon_json($respon);
		}

		loadHelper('function');
		
		$tanggal = tgl_indo_lengkap($quiz->tanggal);
		if($tanggal==""){
			$tanggal = "Online";
		}
		$lokasi_quiz = DB::table('lokasi')->where('id_lokasi', $quiz->id_lokasi)->first();
		if($lokasi_quiz){
			$quiz->lokasi = $lokasi_quiz->nama_lokasi;
		}
		$list_sesi = DB::select("select c.id_sesi_master 
							from quiz_sesi as a, quiz_sesi_detil as b, quiz_sesi_master as c 
							where  a.id_quiz = b.id_quiz
							and c.id_sesi_master = b.id_sesi_master and  a.token = '$token' order by b.urutan asc");
		$data_sesi = [
				'nama_sesi'=>$quiz->nama_sesi,
				'lokasi'=>$quiz->lokasi,
				'tanggal'=>$tanggal,
				'token'=>$quiz->token,
				'play'=>0,
				'finish'=>0,
				'submit'=>0,
				'uuid_user'=>$user->uuid,
				'jumlah_sesi'=>count($list_sesi)
				];
		$respon = array('status'=>true,'data'=>$data_sesi);
		return $this->respon_json($respon);
	}

	function get_info_session_quiz($token){

		$quiz = DB::select("SELECT
								a.* 
							FROM
								quiz_sesi AS a 
							WHERE a.token = '$token'
							");
		
		if(count($quiz)==0){
			$respon = array('status'=>false,'data'=>null, 'message'=>'Token Tidak Valid!');
			return $this->respon_json($respon);
		}
		$quiz = $quiz[0];

		$list = DB::select("select c.*, b.durasi, b.kunci_waktu, a.token , b.urutan, a.id_quiz 
							from quiz_sesi as a, quiz_sesi_detil as b, quiz_sesi_master as c 
							where  a.id_quiz = b.id_quiz
							and c.id_sesi_master = b.id_sesi_master and  a.token = '$token' order by b.urutan asc");
		
		if (count($list)==0){
			$respon = array('status'=>false,'data'=>null, 'message'=>'Token Tidak Valid!');
			return $this->respon_json($respon);
		}
		$list_sesi = array();

		foreach ($list as $s){

			 $soal = $s->soal; //jumlah jawaban yang diisi peserta
			 $jawaban = $s->jawaban; //jumlah jawaban yang diisi peserta
			 $id_quiz = $s->id_quiz;
			 if($s->kategori=='SKALA_PEMINATAN_SMK'){
			 	//khusus peminatan smk jumlah jawaban tergantung kondisi SMK;
			 	$jawaban = DB::table('quiz_sesi_mapping_smk')->where('id_quiz', $id_quiz)->count();
			 	$soal = $soal."?token=".$token;
			 }

			 $temp = array(
			 		'token'=>$token,
					'urutan'=>$s->urutan,
					'nama_sesi_ujian'=>$s->nama_sesi_ujian,
					'mode'=>$s->mode,
					'durasi'=>$s->durasi,
					'kunci_waktu'=>$s->kunci_waktu,
					'kategori'=>$s->kategori,
					"play"=>0,
					"finish"=>0,
					'soal'=>$soal,
					'jawaban'=>$jawaban,
					'petunjuk_sesi'=>$s->petunjuk_sesi
			 	);
			 array_push($list_sesi,$temp);
		}

		$respon = array('status'=>true,'data'=>$list_sesi);
		return $this->respon_json($respon);
	}


	function get_list_sesi($token){
		$user = $this->get_user();

		$id_user = $user->id;
		$quiz = DB::select("SELECT
								a.*, d.submit, d.submit_at, d.token_submit, d.status_hasil
							FROM
								quiz_sesi AS a,
								quiz_sesi_user as d 
							WHERE
								d.id_quiz = a.id_quiz
								and d.id_user = $id_user 
								AND a.token = '$token'
							");

		if(count($quiz)==0){
			$respon = array('status'=>false,'data'=>null, 'message'=>'Token Tidak Valid!');
			return $this->respon_json($respon);
		}
		$quiz = $quiz[0];
		$url_result = "";
		if($quiz->submit==1){
			$url_result = url('result/'.$quiz->token_submit);
		}

		$status = array(
			'open'=>$quiz->open,
			'submit'=>$quiz->submit,
			'result'=>(int)$quiz->status_hasil,
			'download'=>$url_result,
			'json_url'=>$quiz->json_url,
			'json_url_encrypt'=>$quiz->json_url_encrypt,
		);

		$list = DB::select("select c.*, b.durasi, b.kunci_waktu, a.token , b.urutan, a.id_quiz 
							from quiz_sesi as a, quiz_sesi_detil as b, quiz_sesi_master as c 
							where  a.id_quiz = b.id_quiz
							and c.id_sesi_master = b.id_sesi_master and  a.token = '$token' order by b.urutan asc");
		if (count($list)==0){
			$respon = array('status'=>false,'data'=>null, 'message'=>'Token Tidak Valid!');
			return $this->respon_json($respon);
		}
		$list_sesi = array();

		foreach ($list as $s){

			 $soal = $s->soal; //jumlah jawaban yang diisi peserta
			 $jawaban = $s->jawaban; //jumlah jawaban yang diisi peserta
			 $id_quiz = $s->id_quiz;
			 if($s->kategori=='SKALA_PEMINATAN_SMK'){
			 	//khusus peminatan smk jumlah jawaban tergantung kondisi SMK;
			 	$jawaban = DB::table('quiz_sesi_mapping_smk')->where('id_quiz', $id_quiz)->count();
			 	//$soal = $soal."?token=".$token;
			 }

			 $temp = array(
			 		'token'=>$token,
					'urutan'=>$s->urutan,
					'nama_sesi_ujian'=>$s->nama_sesi_ujian,
					'mode'=>$s->mode,
					'durasi'=>$s->durasi,
					'kunci_waktu'=>$s->kunci_waktu,
					'kategori'=>$s->kategori,
					"play"=>0,
					"finish"=>0,
					'soal'=>$soal,
					'jawaban'=>$jawaban,
					'petunjuk_sesi'=>$s->petunjuk_sesi
			 	);
			 array_push($list_sesi,$temp);
		}

		$respon = array('status'=>true,'data'=>$list_sesi, 'info'=>$status);
		return $this->respon_json($respon);
	}

	function send_start_quiz($token){
		$user = $this->get_user();
		if(!$user){
			$respon = array('status'=>false,'message'=>'Akses Tidak Valid');
			return $this->respon_json($respon);
		}

		$cek = DB::table('quiz_sesi')->where('token', $token)->first();

		if (!$cek){
			$respon = array('status'=>false,'data'=>null, 'message'=>'Token Tidak Valid!');
			return $this->respon_json($respon);
		}
		if($cek->open==1){
			$id_user = $user->id;
			$cek_peserta = DB::table('quiz_sesi_user')
								->where('id_user', $id_user)
								->where('id_quiz', $cek->id_quiz)
								->first();
			if($cek_peserta){
				DB::table('quiz_sesi_user')
					->where('id_user', $id_user)
					->where('id_quiz', $cek->id_quiz)
					->update(['start_at'=>date('Y-m-d H:i:s')]);
					
				$respon = array('status'=>true,'open'=>true);
			}else{
				$respon = array('status'=>true,'open'=>false);
			}
		}else{
			$respon = array('status'=>true,'open'=>false);
		}
		return $this->respon_json($respon);
	}

	function get_open_quiz($token){

		$user = $this->get_user();
		if(!$user){
			$respon = array('status'=>false,'message'=>'Akses Tidak Valid');
			return $this->respon_json($respon);
		}

		$cek = DB::table('quiz_sesi')->where('token', $token)->first();

		if (!$cek){
			$respon = array('status'=>false,'data'=>null, 'message'=>'Token Tidak Valid!');
			return $this->respon_json($respon);
		}
		if($cek->open==1){
			$id_user = $user->id;
			$cek_peserta = DB::table('quiz_sesi_user')
								->where('id_user', $id_user)
								->where('id_quiz', $cek->id_quiz)
								->first();
			if($cek_peserta){
				DB::table('quiz_sesi_user')
					->where('id_user', $id_user)
					->where('id_quiz', $cek->id_quiz)
					->update(['start_at'=>date('Y-m-d H:i:s')]);
				$respon = array('status'=>true,'open'=>true);
			}else{
				$respon = array('status'=>true,'open'=>false);
			}
		}else{
			$respon = array('status'=>true,'open'=>false);
		}
		return $this->respon_json($respon);
	}


	//PENJURUSAN SMA
	//SOAL-KOGNITIF

	function get_soal_break(){

		$token = request()->get('token');
		$array_soal = array();
		$pilihan = array();
		$temp = [
					'nomor'=>'00',
					'uuid'=>'00',
					'token'=>$token,
					'pernyataan'=>'',
					'pilihan'=>$pilihan, 
					'kategori'=>'BREAK',
					'gambar'=>null,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'BREAK',
					'petunjuk'=>''
					];
		array_push($array_soal, $temp);
		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
	}

	function get_soal_kognitif($paket, $bidang){
		$token = request()->get('token');

		$data = DB::select("SELECT
					a.urutan,
					a.uuid,
					a.bidang,
					a.pertanyaan,
					c.image_base64,
					c.type as type_image,
					a.pilihan_a,
					a.pilihan_b,
					a.pilihan_c,
					a.pilihan_d,
					a.pilihan_e,
					b.isi_petunjuk 
				FROM
					soal_kognitif AS a
					LEFT JOIN petunjuk_soal AS b ON a.id_petunjuk = b.id_petunjuk 
					LEFT JOIN gambar AS c ON a.pertanyaan_gambar = c.filename 
					where a.bidang = '$bidang'
					and a.paket = '$paket' 
				ORDER BY
					bidang, urutan");

		$array_soal = array();
		foreach ($data as $r){

			
			$pertanyaan = htmlentities($r->pertanyaan, ENT_QUOTES, 'UTF-8');
			$pertanyaan = str_replace("&hellip;", "", $pertanyaan);
			$pertanyaan_gambar = "";
			$petunjuk = '';
			if($r->isi_petunjuk){
				$petunjuk = "<p>".$r->isi_petunjuk."</p>";
			}
			
			if($r->image_base64){
				$pertanyaan_gambar =  '<br><img width="100%" src="data:image/'.$r->type_image.';base64,'.$r->image_base64. '">';

			}
			if($pertanyaan_gambar){
				$pertanyaan = $pertanyaan.$pertanyaan_gambar;
			}
			$pilihan = array();
			if($r->pilihan_a){array_push($pilihan, ["value"=>'A', 'text'=>$r->pilihan_a]);}
			if($r->pilihan_b){array_push($pilihan, ["value"=>'B', 'text'=>$r->pilihan_b]);}
			if($r->pilihan_c){array_push($pilihan, ["value"=>'C', 'text'=>$r->pilihan_c]);}
			if($r->pilihan_d){array_push($pilihan, ["value"=>'D', 'text'=>$r->pilihan_d]);}
			if($r->pilihan_e){array_push($pilihan, ["value"=>'E', 'text'=>$r->pilihan_e]);}
		 	$prefix_kategori = 'KOGNITIF_';
		 	if($paket!='NON'){
		 		$prefix_kategori = $prefix_kategori.$paket.'_';
		 	}
			$temp = [
					'nomor'=>str_pad($r->urutan,2,"0",STR_PAD_LEFT),
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pertanyaan,
					'pilihan'=>$pilihan, 
					'kategori'=>$prefix_kategori.$r->bidang,
					'gambar'=>null,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'PG',
					'petunjuk'=>$petunjuk
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
	}
	

	//SOAL-SKALA-PEMINATAN-SMA
	function get_soal_peminatan_sma(){
		$token = request()->get('token');
				$data = DB::select("SELECT
					urutan,
					uuid,
					pernyataan,					
					pilihan_a,
					pilihan_b,
					pilihan_c
				FROM
					soal_peminatan_sma 
					order by urutan
				");
					

		$array_soal = array();
		foreach ($data as $r){
			
			$pernyataan = htmlentities($r->pernyataan, ENT_QUOTES, 'UTF-8');
			$pernyataan = str_replace("&hellip;", "", $pernyataan);
			

			if($r->pernyataan){
				$pernyataan = "<p><i>".$r->pernyataan."</i></p>";
			}			
			
			$pilihan = array();
			if($r->pilihan_a){array_push($pilihan, ["value"=>'A', 'text'=>$r->pilihan_a]);}
			if($r->pilihan_b){array_push($pilihan, ["value"=>'B', 'text'=>$r->pilihan_b]);}
			if($r->pilihan_c){array_push($pilihan, ["value"=>'C', 'text'=>$r->pilihan_c]);}		
		 
			$temp = [
					'nomor'=>str_pad($r->urutan,2,"0",STR_PAD_LEFT),
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_PEMINATAN_SMA',
					'gambar'=>null,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'PG',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
	}

	//khusus untuk demo saja
	function get_soal_peminatan_sma_demo(){
		$token = request()->get('token');
				$data = DB::select("SELECT
					urutan,
					uuid,
					pernyataan,					
					pilihan_a,
					pilihan_b,
					pilihan_c
				FROM
					soal_peminatan_sma 
					ORDER BY random() LIMIT 5;
				");
					

		$array_soal = array();
		$nomor = 1;
		foreach ($data as $r){
			
			$pernyataan = htmlentities($r->pernyataan, ENT_QUOTES, 'UTF-8');
			$pernyataan = str_replace("&hellip;", "", $pernyataan);
			

			if($r->pernyataan){
				$pernyataan = "<p><i>".$r->pernyataan."</i></p>";
			}			
			
			$pilihan = array();
			if($r->pilihan_a){array_push($pilihan, ["value"=>'A', 'text'=>$r->pilihan_a]);}
			if($r->pilihan_b){array_push($pilihan, ["value"=>'B', 'text'=>$r->pilihan_b]);}
			if($r->pilihan_c){array_push($pilihan, ["value"=>'C', 'text'=>$r->pilihan_c]);}		
		 
			$temp = [
					'nomor'=>str_pad($nomor,2,"0",STR_PAD_LEFT),
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_PEMINATAN_SMA',
					'gambar'=>null,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'PG',
					'petunjuk'=>''
					];
			$nomor++;
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
	}
	
	//SOAL-SKALA-PEMINATAN-MAN
	function get_soal_peminatan_man(){
		$token = request()->get('token');
				$data = DB::select("SELECT
					urutan,
					uuid,
					pernyataan,					
					pilihan_a,
					pilihan_b,
					pilihan_c,
					pilihan_d
				FROM
					soal_peminatan_man 
					order by urutan
				");
		$array_soal = array();
		foreach ($data as $r){

			
			$pernyataan = htmlentities($r->pernyataan, ENT_QUOTES, 'UTF-8');
			$pernyataan = str_replace("&hellip;", "", $pernyataan);
			

			if($r->pernyataan){
				$pernyataan = "<p><i>".$r->pernyataan."</i></p>";
			}			
		
			$pilihan = array();
			if($r->pilihan_a){array_push($pilihan, ["value"=>'A', 'text'=>$r->pilihan_a]);}
			if($r->pilihan_b){array_push($pilihan, ["value"=>'B', 'text'=>$r->pilihan_b]);}
			if($r->pilihan_c){array_push($pilihan, ["value"=>'C', 'text'=>$r->pilihan_c]);}
			if($r->pilihan_d){array_push($pilihan, ["value"=>'D', 'text'=>$r->pilihan_d]);}
		
		 
			$temp = [
					'nomor'=>str_pad($r->urutan,2,"0",STR_PAD_LEFT),
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_PEMINATAN_MAN',
					'gambar'=>null,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'PG',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
	}

	//SOAL-SKALA-PEMINATAN-SMK
	//pilihan tergantung mapping dari sekolah yang ada
	
	function get_soal_peminatan_smk(){
		$token = request()->get('token');
		$quiz = DB::table('quiz_sesi')->where('token', $token)->first();
		$id_quiz = $quiz->id_quiz;

		$data = DB::select("select x.*, 
							c.image_base64,
							c.type as type_image from (select 
							a.nomor as urutan,
							a.uuid,
							a.kegiatan,					
							a.gambar
							from soal_peminatan_smk as a, quiz_sesi_mapping_smk as b   
							where a.id_kegiatan = b.id_kegiatan
							and b.id_quiz = $id_quiz ) as x 
							LEFT JOIN gambar AS c ON x.gambar = c.filename
							order by x.urutan ");

		$array_soal = array();
		foreach ($data as $r){
			
			$kegiatan = htmlentities($r->kegiatan, ENT_QUOTES, 'UTF-8');
			$kegiatan = str_replace("&hellip;", "", $kegiatan);
			$gambar = "";

			if($r->kegiatan){
				$kegiatan = $r->kegiatan;
			}

			if($r->image_base64){
				$gambar =  'data:image/'.$r->type_image.';base64,'.$r->image_base64;

			}
			
			$pilihan = array();
			
		 
			$temp = [
					'nomor'=>$r->urutan, //huruf A- Ks
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$kegiatan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_PEMINATAN_SMK',
					'gambar'=>$gambar,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'TOP',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
	}

	function get_soal_peminatan_smk_paket($paket){
		$token = request()->get('token');
		$quiz = DB::table('quiz_sesi')->where('token', $token)->first();
		$id_quiz = $quiz->id_quiz;

		$data = DB::select("select x.*, 
							c.image_base64,
							c.type as type_image from (select 
							a.nomor as urutan,
							a.uuid,
							a.kegiatan,					
							a.gambar
							from soal_peminatan_smk as a, quiz_sesi_mapping_smk as b   
							where a.id_kegiatan = b.id_kegiatan
							and b.id_quiz = $id_quiz and a.paket='$paket' ) as x 
							LEFT JOIN gambar AS c ON x.gambar = c.filename
							order by x.urutan ");

		$array_soal = array();
		foreach ($data as $r){
			
			$kegiatan = htmlentities($r->kegiatan, ENT_QUOTES, 'UTF-8');
			$kegiatan = str_replace("&hellip;", "", $kegiatan);
			$gambar = "";

			if($r->kegiatan){
				$kegiatan = $r->kegiatan;
			}

			if($r->image_base64){
				$gambar =  'data:image/'.$r->type_image.';base64,'.$r->image_base64;

			}
			
			$pilihan = array();
			
		 
			$temp = [
					'nomor'=>$r->urutan, //huruf A- Ks
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$kegiatan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_PEMINATAN_SMK',
					'gambar'=>$gambar,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'TOP',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
	}
	
	function get_soal_peminatan_smk_demo(){
		$token = request()->get('token');
		 
		$data = DB::select("select x.*, 
							c.image_base64,
							c.type as type_image from (select 
							a.nomor as urutan,
							a.uuid,
							a.kegiatan,					
							a.gambar
							from soal_peminatan_smk as a   
							) as x 
							LEFT JOIN gambar AS c ON x.gambar = c.filename
							order by x.urutan ");

		$array_soal = array();
		foreach ($data as $r){
			
			$kegiatan = htmlentities($r->kegiatan, ENT_QUOTES, 'UTF-8');
			$kegiatan = str_replace("&hellip;", "", $kegiatan);
			$gambar = "";

			if($r->kegiatan){
				$kegiatan = $r->kegiatan;
			}

			if($r->image_base64){
				$gambar =  'data:image/'.$r->type_image.';base64,'.$r->image_base64;

			}
			
			$pilihan = array();
			
		 
			$temp = [
					'nomor'=>$r->urutan, //huruf A- Ks
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$kegiatan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_PEMINATAN_SMK',
					'gambar'=>$gambar,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'TOP',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
	}


	//SOAL-SIKAP-PELAJARAN
	function get_soal_sikap_pelajaran(){
		$token = request()->get('token');
				$data = DB::select("SELECT
					urutan,
					uuid,
					pelajaran,
					sikap_negatif1,
					sikap_negatif2,
					sikap_negatif3,
					sikap_positif1,
					sikap_positif2,
					sikap_positif3,
					kelompok
					
				FROM
					soal_sikap_pelajaran
					order by urutan
				");

		$array_soal = array();
		foreach ($data as $r){

			
			$pernyataan = htmlentities($r->pelajaran, ENT_QUOTES, 'UTF-8');
			$pernyataan = str_replace("&hellip;", "", $pernyataan);
			

			if($r->pelajaran){
				$pernyataan = "<strong>".$r->pelajaran."</strong> adalah pelajaran yang ...";
			}
			
			$temp = [		

					'nomor'=>str_pad($r->urutan,2,"0",STR_PAD_LEFT), //01 02 03 04...
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>[], 
					'kategori'=>'SIKAP_TERHADAP_PELAJARAN',
					'gambar'=>null,
					'sn1'=>$r->sikap_negatif1,
					'sn2'=>$r->sikap_negatif2,
					'sn3'=>$r->sikap_negatif3,
					'sp1'=>$r->sikap_positif1,
					'sp2'=>$r->sikap_positif2,
					'sp3'=>$r->sikap_positif3,
					'min_sikap'=>0,
					'max_sikap'=>7,
					'mode'=>'RT',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
	}

	function get_soal_sikap_pelajaran_demo(){
		$token = request()->get('token');
				$data = DB::select("SELECT
					urutan,
					uuid,
					pelajaran,
					sikap_negatif1,
					sikap_negatif2,
					sikap_negatif3,
					sikap_positif1,
					sikap_positif2,
					sikap_positif3,
					kelompok
					
				FROM
					soal_sikap_pelajaran
					order by random() limit 3
				");

		$array_soal = array();
		$nomor = 1;
		foreach ($data as $r){

			
			$pernyataan = htmlentities($r->pelajaran, ENT_QUOTES, 'UTF-8');
			$pernyataan = str_replace("&hellip;", "", $pernyataan);
			

			if($r->pelajaran){
				$pernyataan = "<strong>".$r->pelajaran."</strong> adalah pelajaran yang ...";
			}
			
			$temp = [		

					'nomor'=>str_pad($nomor++,2,"0",STR_PAD_LEFT), //01 02 03 04...
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>[], 
					'kategori'=>'SIKAP_TERHADAP_PELAJARAN',
					'gambar'=>null,
					'sn1'=>$r->sikap_negatif1,
					'sn2'=>$r->sikap_negatif2,
					'sn3'=>$r->sikap_negatif3,
					'sp1'=>$r->sikap_positif1,
					'sp2'=>$r->sikap_positif2,
					'sp3'=>$r->sikap_positif3,
					'min_sikap'=>0,
					'max_sikap'=>7,
					'mode'=>'RT',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
	}
 	
	//SOAL TMI (TES MINAT INDONESIA)
 	function get_soal_tes_minat_indonesia(Request $r){

 		$token = request()->get('token');
		$data = DB::select("SELECT
			a.urutan,
			a.uuid,
			a.pernyataan
		FROM
			soal_tmi as a
			order by a.urutan
		");

		$array_soal = array();
		foreach ($data as $r){
			
			$pernyataan = htmlentities($r->pernyataan, ENT_QUOTES, 'UTF-8');
			$pernyataan = str_replace("&hellip;", "", $pernyataan);
			$gambar = "";

			if($r->pernyataan){
				$pernyataan = $r->pernyataan;
			}
 
			
			$pilihan = array();
			
		 
			$temp = [
					'nomor'=>str_pad($r->urutan,2,"0",STR_PAD_LEFT), //01 02 03 04...
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_TES_MINAT_INDONESIA',
					'gambar'=>null,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'TOP',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
 	}


 	//SOAL TMI (TES MINAT INDONESIA)
 	function get_soal_tes_minat_indonesia_demo(Request $r){

 		$token = request()->get('token');
		$data = DB::select("SELECT
			a.urutan,
			a.uuid,
			a.pernyataan
		FROM
			soal_tmi as a
			order by random() limit 10
		");

		$array_soal = array();
		$nomor = 1;
		foreach ($data as $r){
			
			$pernyataan = htmlentities($r->pernyataan, ENT_QUOTES, 'UTF-8');
			$pernyataan = str_replace("&hellip;", "", $pernyataan);
			$gambar = "";

			if($r->pernyataan){
				$pernyataan = $r->pernyataan;
			}
 
			
			$pilihan = array();
			
		 
			$temp = [
					'nomor'=>str_pad($nomor++,2,"0",STR_PAD_LEFT), //01 02 03 04...
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_TES_MINAT_INDONESIA',
					'gambar'=>null,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'TOP',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
 	}

	//SOAL KARAKTERISTIK PRIBADI 
 	function get_soal_karakteristik_pribadi (Request $r){

 		$token = request()->get('token');
		$data = DB::select("SELECT
			a.urutan,
			a.uuid,
			a.pernyataan,
			a.pilihan_1,
			a.pilihan_2,
			a.pilihan_3,
			a.pilihan_4
		FROM
			soal_karakteristik_pribadi as a
			order by a.urutan
		");

		$array_soal = array();
		foreach ($data as $r){
			
			$pernyataan = htmlentities($r->pernyataan, ENT_QUOTES, 'UTF-8');
			$pernyataan = str_replace("&hellip;", "", $pernyataan);
			$gambar = "";

			if($r->pernyataan){
				$pernyataan = $r->pernyataan;
			}
 
			
			$pilihan = array();
			if($r->pilihan_1){array_push($pilihan, ["value"=>'A', 'text'=>$r->pilihan_1]);}
			if($r->pilihan_2){array_push($pilihan, ["value"=>'B', 'text'=>$r->pilihan_2]);}
			if($r->pilihan_3){array_push($pilihan, ["value"=>'C', 'text'=>$r->pilihan_3]);}
			if($r->pilihan_4){array_push($pilihan, ["value"=>'D', 'text'=>$r->pilihan_4]);}
			
		 
			$temp = [
					'nomor'=>str_pad($r->urutan,2,"0",STR_PAD_LEFT), //01 02 03 04...
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_TES_KARAKTERISTIK_PRIBADI',
					'gambar'=>null,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'PG',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
 	}

 	function get_soal_karakteristik_pribadi_demo(Request $r){

 		$token = request()->get('token');
		$data = DB::select("SELECT
			a.urutan,
			a.uuid,
			a.pernyataan,
			a.pilihan_1,
			a.pilihan_2,
			a.pilihan_3,
			a.pilihan_4
		FROM
			soal_karakteristik_pribadi as a
			order by random() limit 5
		");

		$array_soal = array();
		$nomor = 1;
		foreach ($data as $r){
			
			$pernyataan = htmlentities($r->pernyataan, ENT_QUOTES, 'UTF-8');
			$pernyataan = str_replace("&hellip;", "", $pernyataan);
			$gambar = "";

			if($r->pernyataan){
				$pernyataan = $r->pernyataan;
			}
 
			
			$pilihan = array();
			if($r->pilihan_1){array_push($pilihan, ["value"=>'A', 'text'=>$r->pilihan_1]);}
			if($r->pilihan_2){array_push($pilihan, ["value"=>'B', 'text'=>$r->pilihan_2]);}
			if($r->pilihan_3){array_push($pilihan, ["value"=>'C', 'text'=>$r->pilihan_3]);}
			if($r->pilihan_4){array_push($pilihan, ["value"=>'D', 'text'=>$r->pilihan_4]);}
			
		 
			$temp = [
					'nomor'=>str_pad($nomor,2,"0",STR_PAD_LEFT), //01 02 03 04...
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_TES_KARAKTERISTIK_PRIBADI',
					'gambar'=>null,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'PG',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
			$nomor++;
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
 	}

	//SOAL TIPOLOGI JUNG
 	function get_soal_tipologi_jung (Request $r){

 		$token = request()->get('token');
		$data = DB::select("SELECT
			a.urutan,
			a.uuid,
			a.pernyataan,
			a.pilihan_a,			
			a.pilihan_b
		FROM
			soal_tipologi_jung as a
			order by a.urutan
		");

		$array_soal = array();
		foreach ($data as $r){
			
			$pernyataan = htmlentities($r->pernyataan, ENT_QUOTES, 'UTF-8');
			$pernyataan = str_replace("&hellip;", "", $pernyataan);
			$gambar = "";

			if($r->pernyataan){
				$pernyataan = $r->pernyataan;
			}
 
			
			$pilihan = array();
			if($r->pilihan_a){array_push($pilihan, ["value"=>'A', 'text'=>$r->pilihan_a]);}
			if($r->pilihan_b){array_push($pilihan, ["value"=>'B', 'text'=>$r->pilihan_b]);}
		
			
		 
			$temp = [
					'nomor'=>str_pad($r->urutan,2,"0",STR_PAD_LEFT), //01 02 03 04...
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_TES_TIPOLOGI_JUNG',
					'gambar'=>null,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'PG',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
 	}


 	function get_soal_tipologi_jung_demo(Request $r){

 		$token = request()->get('token');
		$data = DB::select("SELECT
			a.urutan,
			a.uuid,
			a.pernyataan,
			a.pilihan_a,			
			a.pilihan_b
		FROM
			soal_tipologi_jung as a
			order by random() limit 5
		");

		$array_soal = array();
		$nomor = 1;
		foreach ($data as $r){
			
			$pernyataan = htmlentities($r->pernyataan, ENT_QUOTES, 'UTF-8');
			$pernyataan = str_replace("&hellip;", "", $pernyataan);
			$gambar = "";

			if($r->pernyataan){
				$pernyataan = $r->pernyataan;
			}
 
			
			$pilihan = array();
			if($r->pilihan_a){array_push($pilihan, ["value"=>'A', 'text'=>$r->pilihan_a]);}
			if($r->pilihan_b){array_push($pilihan, ["value"=>'B', 'text'=>$r->pilihan_b]);}
		
			
		 
			$temp = [
					'nomor'=>str_pad($nomor,2,"0",STR_PAD_LEFT), //01 02 03 04...
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_TES_TIPOLOGI_JUNG',
					'gambar'=>null,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'PG',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
			$nomor++;
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
 	}


 	//PEMINATAN KULIAH
 	//SOAL MINAT EKSTAKTA (TES MINAT INDONESIA)
 	function get_soal_minat_kuliah_eksakta(Request $r){

 		$token = request()->get('token');
		$data = DB::select("SELECT
			a.urutan,
			a.uuid,
			a.indikator, 
			a.minat
		FROM
			soal_minat_kuliah_eksakta as a
			order by a.urutan
		");

		$array_soal = array();
		foreach ($data as $r){
			
		 
			$gambar = "";

			if($r->indikator){
				$pernyataan = $r->indikator;
				$pernyataan = str_replace( 'class="ql-align-justify"', '', $pernyataan);
				$pernyataan = str_replace( 'style="color: black;"', '', $pernyataan);
				$pernyataan = strip_tags($pernyataan, '<ul><li>');
				//$pernyataan = "[".$r->minat ."] ".$pernyataan;
			}
 
			
			$pilihan = array();
			
		 
			$temp = [
					'nomor'=>str_pad($r->urutan,2,"0",STR_PAD_LEFT), //01 02 03 04...
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_PMK_MINAT_ILMU_ALAM',
					'gambar'=>null,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'TOP',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
 	}

 	function get_soal_minat_kuliah_eksakta_demo(Request $r){

 		$token = request()->get('token');
		$data = DB::select("SELECT
			a.urutan,
			a.uuid,
			a.indikator, 
			a.minat
		FROM
			soal_minat_kuliah_eksakta as a
			order by random() limit 10
		");

		$array_soal = array();

		$nomor = 1;
		foreach ($data as $r){
			
		 
			$gambar = "";

			if($r->indikator){
				$pernyataan = $r->indikator;
				$pernyataan = str_replace( 'class="ql-align-justify"', '', $pernyataan);
				$pernyataan = str_replace( 'style="color: black;"', '', $pernyataan);
				$pernyataan = strip_tags($pernyataan, '<ul><li>');
				//$pernyataan = "[".$r->minat ."] ".$pernyataan;
			}
 
			
			$pilihan = array();
			
			$temp = [
					'nomor'=>str_pad($nomor++,2,"0",STR_PAD_LEFT), //01 02 03 04...
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_PMK_MINAT_ILMU_ALAM',
					'gambar'=>null,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'TOP',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
 	}


 	function get_soal_minat_kuliah_sosial(Request $r){

 		$token = request()->get('token');
		$data = DB::select("SELECT
			a.urutan,
			a.uuid,
			a.indikator, 
			a.minat
		FROM
			soal_minat_kuliah_sosial as a
			order by a.urutan
		");

		$array_soal = array();
		foreach ($data as $r){
			
		 
			$gambar = "";

			if($r->indikator){
				$pernyataan = $r->indikator;
				$pernyataan = str_replace( 'class="ql-align-justify"', '', $pernyataan);
				$pernyataan = str_replace( 'style="color: black;"', '', $pernyataan);
				$pernyataan = strip_tags($pernyataan, '<ul><li>');
				//$pernyataan = "[".$r->minat ."] ".$pernyataan;
			}
 
			
			$pilihan = array();
			
		 
			$temp = [
					'nomor'=>str_pad($r->urutan,2,"0",STR_PAD_LEFT), //01 02 03 04...
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_PMK_MINAT_ILMU_SOSIAL',
					'gambar'=>null,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'TOP',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
 	}

 	function get_soal_minat_kuliah_kedinasan(Request $r){
 		$token = request()->get('token');
		$data = DB::select("SELECT
			a.*
		FROM
			soal_minat_kuliah_dinas as a
			order by a.nomor
		");

		$array_soal = array();
		foreach ($data as $r){
			
			$gambar = "";

			if($r->deskripsi){
				$pernyataan = $r->deskripsi;
			}
 
			
			$pilihan = array();
			if($r->pernyataan_a){array_push($pilihan, ["value"=>'A', 'text'=>$r->pernyataan_a]);}
			if($r->pernyataan_b){array_push($pilihan, ["value"=>'B', 'text'=>$r->pernyataan_b]);}
			if($r->pernyataan_c){array_push($pilihan, ["value"=>'C', 'text'=>$r->pernyataan_c]);}
			if($r->pernyataan_d){array_push($pilihan, ["value"=>'D', 'text'=>$r->pernyataan_d]);}
			if($r->pernyataan_e){array_push($pilihan, ["value"=>'E', 'text'=>$r->pernyataan_e]);}
			if($r->pernyataan_f){array_push($pilihan, ["value"=>'F', 'text'=>$r->pernyataan_f]);}
			if($r->pernyataan_g){array_push($pilihan, ["value"=>'G', 'text'=>$r->pernyataan_g]);}
			if($r->pernyataan_h){array_push($pilihan, ["value"=>'H', 'text'=>$r->pernyataan_h]);}
			if($r->pernyataan_i){array_push($pilihan, ["value"=>'I', 'text'=>$r->pernyataan_i]);}
			if($r->pernyataan_j){array_push($pilihan, ["value"=>'J', 'text'=>$r->pernyataan_j]);}
			if($r->pernyataan_k){array_push($pilihan, ["value"=>'K', 'text'=>$r->pernyataan_k]);}
			if($r->pernyataan_l){array_push($pilihan, ["value"=>'L', 'text'=>$r->pernyataan_l]);}
			
		 
			$temp = [
					'nomor'=>str_pad($r->nomor,2,"0",STR_PAD_LEFT), //01 02 03 04...
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_PMK_SEKOLAH_KEDINASAN',
					'gambar'=>null,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'PP',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
 	}

 	function get_soal_minat_kuliah_kedinasan_demo(Request $r){
 		$token = request()->get('token');
		$data = DB::select("SELECT
			a.*
		FROM
			soal_minat_kuliah_dinas as a
			order by random() limit 2
		");

		$array_soal = array();
		$nomor = 1;
		foreach ($data as $r){
			
			$gambar = "";

			if($r->deskripsi){
				$pernyataan = $r->deskripsi;
			}
 
			
			$pilihan = array();
			if($r->pernyataan_a){array_push($pilihan, ["value"=>'A', 'text'=>$r->pernyataan_a]);}
			if($r->pernyataan_b){array_push($pilihan, ["value"=>'B', 'text'=>$r->pernyataan_b]);}
			if($r->pernyataan_c){array_push($pilihan, ["value"=>'C', 'text'=>$r->pernyataan_c]);}
			if($r->pernyataan_d){array_push($pilihan, ["value"=>'D', 'text'=>$r->pernyataan_d]);}
			if($r->pernyataan_e){array_push($pilihan, ["value"=>'E', 'text'=>$r->pernyataan_e]);}
			if($r->pernyataan_f){array_push($pilihan, ["value"=>'F', 'text'=>$r->pernyataan_f]);}
			if($r->pernyataan_g){array_push($pilihan, ["value"=>'G', 'text'=>$r->pernyataan_g]);}
			if($r->pernyataan_h){array_push($pilihan, ["value"=>'H', 'text'=>$r->pernyataan_h]);}
			if($r->pernyataan_i){array_push($pilihan, ["value"=>'I', 'text'=>$r->pernyataan_i]);}
			if($r->pernyataan_j){array_push($pilihan, ["value"=>'J', 'text'=>$r->pernyataan_j]);}
			if($r->pernyataan_k){array_push($pilihan, ["value"=>'K', 'text'=>$r->pernyataan_k]);}
			if($r->pernyataan_l){array_push($pilihan, ["value"=>'L', 'text'=>$r->pernyataan_l]);}
			
		 
			$temp = [
					'nomor'=>str_pad($nomor++,2,"0",STR_PAD_LEFT), //01 02 03 04...
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_PMK_SEKOLAH_KEDINASAN',
					'gambar'=>null,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'PP',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
 	}

 	function get_soal_minat_kuliah_agama(Request $r){

 		$token = request()->get('token');
		$data = DB::select("SELECT
			a.urutan,
			a.uuid,
			a.indikator, 
			a.jurusan
		FROM
			soal_minat_kuliah_agama as a
			order by a.urutan
		");

		$array_soal = array();
		foreach ($data as $r){
			
		 
			$gambar = "";

			if($r->indikator){
				$pernyataan = $r->indikator;
				$pernyataan = str_replace( 'class="ql-align-justify"', '', $pernyataan);
				$pernyataan = str_replace( 'style="color: black;"', '', $pernyataan);
				$pernyataan = strip_tags($pernyataan, '<ul><li>');
				//$pernyataan = "[".$r->jurusan ."] ".$pernyataan;

			}
 
			
			$pilihan = array();
			
		 
			$temp = [
					'nomor'=>str_pad($r->urutan,2,"0",STR_PAD_LEFT), //01 02 03 04...
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_PMK_ILMU_AGAMA',
					'gambar'=>null,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'TOP',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
 	}

 	function get_soal_minat_suasana_kerja(Request $r){
 		$token = request()->get('token');
		//$quiz = DB::table('quiz_sesi')->where('token', $token)->first();
		//$id_quiz = $quiz->id_quiz;

		$data = DB::select("select x.*, 
							c.image_base64,
							c.type as type_image from (select 
							a.nomor as urutan,
							a.uuid,
							a.kegiatan,					
							a.gambar
							from soal_minat_kuliah_suasana_kerja as a
							) as x 
							LEFT JOIN gambar AS c ON x.gambar = c.filename
							order by x.urutan ");

		$array_soal = array();
		foreach ($data as $r){
			
			$kegiatan = htmlentities($r->kegiatan, ENT_QUOTES, 'UTF-8');
			$kegiatan = str_replace("&hellip;", "", $kegiatan);
			$gambar = "";

			if($r->kegiatan){
				$kegiatan = $r->kegiatan;
			}

			if($r->image_base64){
				//$gambar =  'data:image/'.$r->type_image.';base64,'.$r->image_base64;
			}
			
			$pilihan = array();
			
		 
			$temp = [
					'nomor'=>$r->urutan, //huruf A- Ks
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$kegiatan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_PMK_SUASANA_KERJA',
					'gambar'=>$gambar,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'TOP',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
 	}

 	function get_soal_pmk_sikap_pelajaran(Request $r){
 		$token = request()->get('token');
				$data = DB::select("SELECT
					urutan,
					uuid,
					pelajaran,
					sikap_negatif1,
					sikap_negatif2,
					sikap_negatif3,
					sikap_positif1,
					sikap_positif2,
					sikap_positif3,
					kelompok
					
				FROM
					soal_sikap_pelajaran_kuliah
					order by urutan
				");

		$array_soal = array();
		foreach ($data as $r){

			
			$pernyataan = htmlentities($r->pelajaran, ENT_QUOTES, 'UTF-8');
			$pernyataan = str_replace("&hellip;", "", $pernyataan);
			

			if($r->pelajaran){
				$pernyataan = "<strong>".$r->pelajaran."</strong>  adalah pelajaran yang ...";
			}
			
			$temp = [		

					'nomor'=>str_pad($r->urutan,2,"0",STR_PAD_LEFT), //01 02 03 04...
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>[], 
					'kategori'=>'SKALA_PMK_SIKAP_PELAJARAN',
					'gambar'=>null,
					'sn1'=>$r->sikap_negatif1,
					'sn2'=>$r->sikap_negatif2,
					'sn3'=>$r->sikap_negatif3,
					'sp1'=>$r->sikap_positif1,
					'sp2'=>$r->sikap_positif2,
					'sp3'=>$r->sikap_positif3,
					'min_sikap'=>0,
					'max_sikap'=>7,
					'mode'=>'RT',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
 	}

 	//SOAL KECERDASAN MAJEMUK
 	function get_soal_kecerdasan_majemuk(Request $r){
 		$token = request()->get('token');
		$data = DB::select("SELECT
			a.*
		FROM
			soal_kecerdasan_majemuk as a
			order by a.nomor
		");

		$array_soal = array();
		foreach ($data as $r){
			
			$gambar = "";

			if($r->deskripsi){
				$pernyataan = $r->deskripsi;
			}
 
			
			$pilihan = array();
			if($r->pernyataan_a){array_push($pilihan, ["value"=>'A', 'text'=>$r->pernyataan_a]);}
			if($r->pernyataan_b){array_push($pilihan, ["value"=>'B', 'text'=>$r->pernyataan_b]);}
			if($r->pernyataan_c){array_push($pilihan, ["value"=>'C', 'text'=>$r->pernyataan_c]);}
			if($r->pernyataan_d){array_push($pilihan, ["value"=>'D', 'text'=>$r->pernyataan_d]);}
			if($r->pernyataan_e){array_push($pilihan, ["value"=>'E', 'text'=>$r->pernyataan_e]);}
			if($r->pernyataan_f){array_push($pilihan, ["value"=>'F', 'text'=>$r->pernyataan_f]);}
			if($r->pernyataan_g){array_push($pilihan, ["value"=>'G', 'text'=>$r->pernyataan_g]);}
			if($r->pernyataan_h){array_push($pilihan, ["value"=>'H', 'text'=>$r->pernyataan_h]);}
			if($r->pernyataan_i){array_push($pilihan, ["value"=>'I', 'text'=>$r->pernyataan_i]);}
			if($r->pernyataan_j){array_push($pilihan, ["value"=>'J', 'text'=>$r->pernyataan_j]);}
			if($r->pernyataan_k){array_push($pilihan, ["value"=>'K', 'text'=>$r->pernyataan_k]);}
			if($r->pernyataan_l){array_push($pilihan, ["value"=>'L', 'text'=>$r->pernyataan_l]);}
			
		 
			$temp = [
					'nomor'=>str_pad($r->nomor,2,"0",STR_PAD_LEFT), //01 02 03 04...
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_KECERDASAN_MAJEMUK',
					'gambar'=>null,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'PP',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
 	}

 	function get_soal_kecerdasan_majemuk_demo(Request $r){
 		$token = request()->get('token');
		$data = DB::select("SELECT
			a.*
		FROM
			soal_kecerdasan_majemuk as a
			order by random() limit 2
		");

		$array_soal = array();
		$nomor = 1;

		foreach ($data as $r){
			
			$gambar = "";

			if($r->deskripsi){
				$pernyataan = $r->deskripsi;
			}
 
			$pilihan = array();
			if($r->pernyataan_a){array_push($pilihan, ["value"=>'A', 'text'=>$r->pernyataan_a]);}
			if($r->pernyataan_b){array_push($pilihan, ["value"=>'B', 'text'=>$r->pernyataan_b]);}
			if($r->pernyataan_c){array_push($pilihan, ["value"=>'C', 'text'=>$r->pernyataan_c]);}
			if($r->pernyataan_d){array_push($pilihan, ["value"=>'D', 'text'=>$r->pernyataan_d]);}
			if($r->pernyataan_e){array_push($pilihan, ["value"=>'E', 'text'=>$r->pernyataan_e]);}
			if($r->pernyataan_f){array_push($pilihan, ["value"=>'F', 'text'=>$r->pernyataan_f]);}
			if($r->pernyataan_g){array_push($pilihan, ["value"=>'G', 'text'=>$r->pernyataan_g]);}
			if($r->pernyataan_h){array_push($pilihan, ["value"=>'H', 'text'=>$r->pernyataan_h]);}
			if($r->pernyataan_i){array_push($pilihan, ["value"=>'I', 'text'=>$r->pernyataan_i]);}
			if($r->pernyataan_j){array_push($pilihan, ["value"=>'J', 'text'=>$r->pernyataan_j]);}
			if($r->pernyataan_k){array_push($pilihan, ["value"=>'K', 'text'=>$r->pernyataan_k]);}
			if($r->pernyataan_l){array_push($pilihan, ["value"=>'L', 'text'=>$r->pernyataan_l]);}
			
		 
			$temp = [
					'nomor'=>str_pad($nomor++,2,"0",STR_PAD_LEFT), //01 02 03 04...
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_KECERDASAN_MAJEMUK',
					'gambar'=>null,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'PP',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
 	}

 	//SOAL GAYA PEKERJAAN
 	function get_soal_gaya_pekerjaan(Request $r){

 		$token = request()->get('token');
		$data = DB::select("SELECT
			a.nomor,
			a.deskripsi, 
			a.uuid
		FROM
			soal_gaya_pekerjaan as a
			order by a.nomor asc
		");

		$ref_skor_gaya_pekerjaan = DB::table('ref_skor_gaya_pekerjaan')->orderby('jawaban')->get();
		$pilihan = array();
		foreach($ref_skor_gaya_pekerjaan as $s){
			array_push($pilihan, ["value"=>$s->jawaban, 'text'=>$s->respon]);
		}
		 
		$array_soal = array();
		foreach ($data as $r){
			
			$pernyataan = htmlentities($r->deskripsi, ENT_QUOTES, 'UTF-8');
			$pernyataan = str_replace("&hellip;", "", $pernyataan);
			$gambar = "";

			$temp = [
					'nomor'=>str_pad($r->nomor,2,"0",STR_PAD_LEFT), //01 02 03 04...
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_GAYA_PEKERJAAN',
					'gambar'=>null,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'PG',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
 	}

 	function get_soal_gaya_pekerjaan_demo(Request $r){

 		$token = request()->get('token');
		$data = DB::select("SELECT
			a.nomor,
			a.deskripsi, 
			a.uuid
		FROM
			soal_gaya_pekerjaan as a
			order by random() limit 2
		");

		$ref_skor_gaya_pekerjaan = DB::table('ref_skor_gaya_pekerjaan')->orderby('jawaban')->get();
		$pilihan = array();
		foreach($ref_skor_gaya_pekerjaan as $s){
			array_push($pilihan, ["value"=>$s->jawaban, 'text'=>$s->respon]);
		}
		$nomor = 1;
		$array_soal = array();
		foreach ($data as $r){
			
			$pernyataan = htmlentities($r->deskripsi, ENT_QUOTES, 'UTF-8');
			$pernyataan = str_replace("&hellip;", "", $pernyataan);
			$gambar = "";
 			
		 
			$temp = [
					'nomor'=>str_pad($nomor++,2,"0",STR_PAD_LEFT), //01 02 03 04...
					'uuid'=>$r->uuid,
					'token'=>$token,
					'pernyataan'=>$pernyataan,
					'pilihan'=>$pilihan, 
					'kategori'=>'SKALA_GAYA_PEKERJAAN',
					'gambar'=>null,
					'sn1'=>null,
					'sn2'=>null,
					'sn3'=>null,
					'sp1'=>null,
					'sp2'=>null,
					'sp3'=>null,
					'min_sikap'=>0,
					'max_sikap'=>0,
					'mode'=>'PG',
					'petunjuk'=>''
					];
			array_push($array_soal, $temp);
		}

		$respon = array('status'=>true,'data'=>$array_soal);
		return $this->respon_json($respon);
 	}

}
