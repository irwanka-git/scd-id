<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
| EDIT KOMENTAR ROOT WEB by IRWANKA
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/get-random-key', function () {
      return Crypt::encrypt(455);
});

Route::group(['prefix'=>'landing'], function(){
	Route::get('/', 'LandingPageController@index');
});


Route::get('/phpid', function () {
    phpinfo();
}); 

Route::get('/passwd', function () {
    return Hash::make('123456');
});
Route::get('debug', function(){
	loadHelper('skoring');
	$cek = DB::select("select jawaban::varchar  , token_submit, submit_at from quiz_sesi_user");
	foreach ($cek as $data) {
		 
		$sesi = json_decode(valid_json_string($data->jawaban));
		echo $data->token_submit."<br>";
		echo $data->submit_at."<br>";
		// echo str_replace('\\\"',"'",$data->jawaban);
		if($sesi){
			foreach($sesi as $s){
				$kategori = $s->kategori;
				$jawaban = ($s->jawaban);
				echo "<br>". $kategori."<br>";
				for($i=1;$i<count($jawaban);$i++){
					echo " (". $i."=".$jawaban[$i].") ";
				}
			}
		}
	}
});

include("api.php");
include("render-cetak.php");
 
Route::get('/login','LoginController@page_login');
Route::post('/submit-login','LoginController@submit_login');
Route::get('/logout','LoginController@logout');
Route::get('/ganti-password','LoginController@ganti_password');
Route::post('/update-password','LoginController@submit_update_password');

Route::get('/uuid', function () {
	list($usec, $sec) = explode(" ", microtime());
    $time = ((float)$usec + (float)$sec);
    $time = str_replace(".", "-", $time);
    $panjang = strlen($time);
    $sisa = substr($time, -1*($panjang-5));
    return Uuid::generate(3,rand(10,99).rand(0,9).substr($time, 0,5).'-'.rand(0,9).rand(0,9)."-".$sisa,Uuid::NS_DNS);
});

Route::get('/firebase', 'FirebaseController@index');


Route::group(["middleware"=>['auth.login','auth.menu']], function(){
	Route::get('/', 'HomeController@index');
	include("route-kelola-user.php");
	//info kontak
	Route::group(['prefix'=>'info-kontak'], function(){
		Route::get('/', 'SettingKontakController@index');
		Route::get('/dt', 'SettingKontakController@datatable');
		Route::get('/get-data/{uuid}', 'SettingKontakController@get_data');
		Route::post('/update', 'SettingKontakController@submit_update');
	});
	//setting-menu
	Route::group(['prefix'=>'setting-menu'], function(){
		Route::get('/', 'SettingMenuController@index');
		Route::get('/dt', 'SettingMenuController@datatable');
		Route::get('/get-data/{uuid}', 'SettingMenuController@get_data');
		Route::post('/insert', 'SettingMenuController@submit_insert');
		Route::post('/update', 'SettingMenuController@submit_update');
		Route::post('/delete', 'SettingMenuController@submit_delete');
	});

	//LOKASI-TES	
	Route::group(['prefix'=>'lokasi-tes'], function(){
		Route::get('/', 'LokasiTesController@index');
		Route::get('/dt', 'LokasiTesController@datatable');
		Route::get('/get-list-kabupaten/{kode_provinces}', 'LokasiTesController@get_list_kabupaten');
		Route::get('/get-data/{uuid}', 'LokasiTesController@get_data');
		Route::post('/insert', 'LokasiTesController@submit_insert');
		Route::post('/update', 'LokasiTesController@submit_update');
		Route::post('/delete', 'LokasiTesController@submit_delete');
	});

	//setting-role
	Route::group(['prefix'=>'setting-role'], function(){
		Route::get('/', 'SettingRoleController@index');
		Route::get('/dt-role', 'SettingRoleController@datatable_role');
		Route::get('/dt-menu/{id_role}', 'SettingRoleController@datatable_menu');//menu per role
		Route::get('/menu/{uuid}', 'SettingRoleController@menu_role');//tampilkan menu by role
		Route::get('/get-role/{uuid}', 'SettingRoleController@get_data_role');
		Route::get('/get-menu/{uuid}', 'SettingRoleController@get_data_menu');
		Route::post('/insert-role', 'SettingRoleController@submit_insert_role');
		Route::post('/update-role', 'SettingRoleController@submit_update_role');
		Route::post('/delete-role', 'SettingRoleController@submit_delete_role');
		Route::post('/insert-menu', 'SettingRoleController@submit_insert_menu');
		Route::post('/update-menu', 'SettingRoleController@submit_update_menu');
		Route::post('/delete-menu', 'SettingRoleController@submit_delete_menu');
	});
	
	//petunjuk-soal
	Route::group(['prefix'=>'petunjuk-soal'], function(){
		Route::get('/', 'PetunjukSoalController@index');
		Route::get('/dt', 'PetunjukSoalController@datatable');
		Route::get('/get-data/{uuid}', 'PetunjukSoalController@get_data');
		Route::post('/insert', 'PetunjukSoalController@submit_insert');
		Route::post('/update', 'PetunjukSoalController@submit_update');
		Route::post('/delete', 'PetunjukSoalController@submit_delete');
	});

	//informasi-cerdas
	//info-cerdas
	Route::group(['prefix'=>'info-cerdas'], function(){
		Route::get('/', 'InfoCerdasController@index');
		Route::get('/dt', 'InfoCerdasController@datatable');
		Route::get('/get-data/{uuid}', 'InfoCerdasController@get_data');
		Route::post('/insert', 'InfoCerdasController@submit_insert');
		Route::post('/update', 'InfoCerdasController@submit_update');
		Route::post('/delete', 'InfoCerdasController@submit_delete');
		Route::post('/upload-gambar', 'UploadController@upload_gambar_file');
	});

	//soal-test-koginitif
	Route::group(['prefix'=>'soal-test-koginitif'], function(){
		Route::get('/', 'SoalTestKognitifController@index');
		Route::get('/dt', 'SoalTestKognitifController@datatable');
		Route::get('/lihat-soal/{uuid}', 'SoalTestKognitifController@view_soal');
		Route::get('/get-data/{uuid}', 'SoalTestKognitifController@get_data');
		Route::post('/insert', 'SoalTestKognitifController@submit_insert');
		Route::post('/update', 'SoalTestKognitifController@submit_update');
		Route::post('/delete', 'SoalTestKognitifController@submit_delete');
		Route::post('/upload-gambar', 'UploadController@upload_gambar');
	});

	//soal-skala-peminatan-sma
	Route::group(['prefix'=>'soal-skala-peminatan-sma'], function(){
		Route::get('/', 'SoalPeminatanSMAController@index');
		Route::get('/dt', 'SoalPeminatanSMAController@datatable');
		Route::get('/lihat-soal/{uuid}', 'SoalPeminatanSMAController@view_soal');
		Route::get('/get-data/{uuid}', 'SoalPeminatanSMAController@get_data');
		Route::post('/insert', 'SoalPeminatanSMAController@submit_insert');
		Route::post('/update', 'SoalPeminatanSMAController@submit_update');
		Route::post('/delete', 'SoalPeminatanSMAController@submit_delete');
	});

	//soal-skala-peminatan-man
	Route::group(['prefix'=>'soal-skala-peminatan-man'], function(){
		Route::get('/', 'SoalPeminatanMANController@index');
		Route::get('/dt', 'SoalPeminatanMANController@datatable');
		Route::get('/lihat-soal/{uuid}', 'SoalPeminatanMANController@view_soal');
		Route::get('/get-data/{uuid}', 'SoalPeminatanMANController@get_data');
		Route::post('/insert', 'SoalPeminatanMANController@submit_insert');
		Route::post('/update', 'SoalPeminatanMANController@submit_update');
		Route::post('/delete', 'SoalPeminatanMANController@submit_delete');
	});

	//soal-skala-peminatan-smk
	Route::group(['prefix'=>'soal-skala-peminatan-smk'], function(){
		Route::get('/', 'SoalPeminatanSMKController@index');
		Route::get('/dt', 'SoalPeminatanSMKController@datatable');
		Route::get('/lihat-soal/{uuid}', 'SoalPeminatanSMKController@view_soal');
		Route::get('/get-data/{uuid}', 'SoalPeminatanSMKController@get_data');
		Route::post('/insert', 'SoalPeminatanSMKController@submit_insert');
		Route::post('/update', 'SoalPeminatanSMKController@submit_update');
		Route::post('/delete', 'SoalPeminatanSMKController@submit_delete');
		Route::post('/upload-gambar', 'UploadController@upload_gambar');
	});

	//soal-sikap-pelajaran
	Route::group(['prefix'=>'soal-sikap-pelajaran'], function(){
		Route::get('/', 'SoalSikapPelajaranController@index');
		Route::get('/dt', 'SoalSikapPelajaranController@datatable');
		Route::get('/lihat-soal/{uuid}', 'SoalSikapPelajaranController@view_soal');
		Route::get('/get-data/{uuid}', 'SoalSikapPelajaranController@get_data');
		Route::post('/insert', 'SoalSikapPelajaranController@submit_insert');
		Route::post('/update', 'SoalSikapPelajaranController@submit_update');
		Route::post('/delete', 'SoalSikapPelajaranController@submit_delete');
	});

	//soal-tmi (tes minat indonesia)
	Route::group(['prefix'=>'soal-tmi'], function(){
		Route::get('/', 'SoalTMIController@index');
		Route::get('/dt', 'SoalTMIController@datatable');
		Route::get('/lihat-soal/{uuid}', 'SoalTMIController@view_soal');
		Route::get('/get-data/{uuid}', 'SoalTMIController@get_data');
		Route::post('/insert', 'SoalTMIController@submit_insert');
		Route::post('/update', 'SoalTMIController@submit_update');
		Route::post('/delete', 'SoalTMIController@submit_delete');
	});

	//soal-tmi (tes minat indonesia)
	Route::group(['prefix'=>'soal-tmi'], function(){
		Route::get('/', 'SoalTMIController@index');
		Route::get('/dt', 'SoalTMIController@datatable');
		Route::get('/lihat-soal/{uuid}', 'SoalTMIController@view_soal');
		Route::get('/get-data/{uuid}', 'SoalTMIController@get_data');
		Route::post('/insert', 'SoalTMIController@submit_insert');
		Route::post('/update', 'SoalTMIController@submit_update');
		Route::post('/delete', 'SoalTMIController@submit_delete');
	});

	//soal-tes-jung (MBTI KEPRIBADIAN TOPOLOGI JUNG)
	Route::group(['prefix'=>'soal-tes-jung'], function(){
		Route::get('/', 'SoalTipologiJungController@index');
		Route::get('/dt', 'SoalTipologiJungController@datatable');
		Route::get('/lihat-soal/{uuid}', 'SoalTipologiJungController@view_soal');
		Route::get('/get-data/{uuid}', 'SoalTipologiJungController@get_data');
		Route::post('/insert', 'SoalTipologiJungController@submit_insert');
		Route::post('/update', 'SoalTipologiJungController@submit_update');
		Route::post('/delete', 'SoalTipologiJungController@submit_delete');
	});


	//soal-tes-jung (MBTI KEPRIBADIAN TOPOLOGI JUNG)
	Route::group(['prefix'=>'interprestasi-kepribadian-jung'], function(){
		Route::get('/', 'InterprestasiTipologiJungController@index');
		Route::get('/dt', 'InterprestasiTipologiJungController@datatable');
		Route::get('/lihat-soal/{uuid}', 'InterprestasiTipologiJungController@view_soal');
		Route::get('/get-data/{uuid}', 'InterprestasiTipologiJungController@get_data');
		Route::post('/insert', 'InterprestasiTipologiJungController@submit_insert');
		Route::post('/update', 'InterprestasiTipologiJungController@submit_update');
		Route::post('/delete', 'InterprestasiTipologiJungController@submit_delete');
	});


	//komponen (KARAKTERISTIK PRIBADI)
	Route::group(['prefix'=>'komponen-karakteristik-pribadi'], function(){
		Route::get('/', 'KomponenKarakterPribadiController@index');
		Route::get('/dt', 'KomponenKarakterPribadiController@datatable');
		Route::get('/lihat-soal/{uuid}', 'KomponenKarakterPribadiController@view_soal');
		Route::get('/get-data/{uuid}', 'KomponenKarakterPribadiController@get_data');
		Route::post('/insert', 'KomponenKarakterPribadiController@submit_insert');
		Route::post('/update', 'KomponenKarakterPribadiController@submit_update');
		Route::post('/delete', 'KomponenKarakterPribadiController@submit_delete');
	});

	//soal (KARAKTERISTIK PRIBADI)
	Route::group(['prefix'=>'soal-karakteristik-pribadi'], function(){
		Route::get('/', 'SoalKarakterPribadiController@index');
		Route::get('/dt', 'SoalKarakterPribadiController@datatable');
		Route::get('/lihat-soal/{uuid}', 'SoalKarakterPribadiController@view_soal');
		Route::get('/get-data/{uuid}', 'SoalKarakterPribadiController@get_data');
		Route::post('/insert', 'SoalKarakterPribadiController@submit_insert');
		Route::post('/update', 'SoalKarakterPribadiController@submit_update');
		Route::post('/delete', 'SoalKarakterPribadiController@submit_delete');
	});

	//SOAL MINAT KULIAH EKSAKTA
	//soal-minat-kuliah-eksakta
	Route::group(['prefix'=>'soal-minat-kuliah-eksakta'], function(){
		Route::get('/', 'SoalMinatKuliahEksaktaController@index');
		Route::get('/dt', 'SoalMinatKuliahEksaktaController@datatable');
		Route::get('/lihat-soal/{uuid}', 'SoalMinatKuliahEksaktaController@view_soal');
		Route::get('/get-data/{uuid}', 'SoalMinatKuliahEksaktaController@get_data');
		Route::post('/insert', 'SoalMinatKuliahEksaktaController@submit_insert');
		Route::post('/update', 'SoalMinatKuliahEksaktaController@submit_update');
		Route::post('/delete', 'SoalMinatKuliahEksaktaController@submit_delete');
	});


	//SOAL MINAT KULIAH SOSIAL
	//soal-minat-kuliah-sosial
	Route::group(['prefix'=>'soal-minat-kuliah-sosial'], function(){
		Route::get('/', 'SoalMinatKuliahSosialController@index');
		Route::get('/dt', 'SoalMinatKuliahSosialController@datatable');
		Route::get('/lihat-soal/{uuid}', 'SoalMinatKuliahSosialController@view_soal');
		Route::get('/get-data/{uuid}', 'SoalMinatKuliahSosialController@get_data');
		Route::post('/insert', 'SoalMinatKuliahSosialController@submit_insert');
		Route::post('/update', 'SoalMinatKuliahSosialController@submit_update');
		Route::post('/delete', 'SoalMinatKuliahSosialController@submit_delete');
	});

	//SOAL MINAT KULIAH DINAS
	//soal-minat-kuliah-dinas
	Route::group(['prefix'=>'soal-minat-kuliah-dinas'], function(){
		Route::get('/', 'SoalMinatKuliahDinasController@index');
		Route::get('/dt', 'SoalMinatKuliahDinasController@datatable');
		Route::get('/lihat-soal/{uuid}', 'SoalMinatKuliahDinasController@view_soal');
		Route::get('/get-data/{uuid}', 'SoalMinatKuliahDinasController@get_data');
		Route::post('/insert', 'SoalMinatKuliahDinasController@submit_insert');
		Route::post('/update', 'SoalMinatKuliahDinasController@submit_update');
		Route::post('/delete', 'SoalMinatKuliahDinasController@submit_delete');
	});


	//SOAL MINAT KULIAH AGAMA
	//soal-minat-kuliah-agama
	Route::group(['prefix'=>'soal-minat-kuliah-agama'], function(){
		Route::get('/', 'SoalMinatKuliahAgamaController@index');
		Route::get('/dt', 'SoalMinatKuliahAgamaController@datatable');
		Route::get('/lihat-soal/{uuid}', 'SoalMinatKuliahAgamaController@view_soal');
		Route::get('/get-data/{uuid}', 'SoalMinatKuliahAgamaController@get_data');
		Route::post('/insert', 'SoalMinatKuliahAgamaController@submit_insert');
		Route::post('/update', 'SoalMinatKuliahAgamaController@submit_update');
		Route::post('/delete', 'SoalMinatKuliahAgamaController@submit_delete');
	});

	//soal-sikap-pelajaran-kuliah
	Route::group(['prefix'=>'soal-sikap-pelajaran-kuliah'], function(){
		Route::get('/', 'SoalSikapPelajaranKuliahController@index');
		Route::get('/dt', 'SoalSikapPelajaranKuliahController@datatable');
		Route::get('/lihat-soal/{uuid}', 'SoalSikapPelajaranKuliahController@view_soal');
		Route::get('/get-data/{uuid}', 'SoalSikapPelajaranKuliahController@get_data');
		Route::post('/insert', 'SoalSikapPelajaranKuliahController@submit_insert');
		Route::post('/update', 'SoalSikapPelajaranKuliahController@submit_update');
		Route::post('/delete', 'SoalSikapPelajaranKuliahController@submit_delete');
	});

	//soal-mk-suasana-kerja
	Route::group(['prefix'=>'soal-mk-suasana-kerja'], function(){
		Route::get('/', 'SoalMinatKuliahSuasanaKerjaController@index');
		Route::get('/dt', 'SoalMinatKuliahSuasanaKerjaController@datatable');
		Route::get('/lihat-soal/{uuid}', 'SoalMinatKuliahSuasanaKerjaController@view_soal');
		Route::get('/get-data/{uuid}', 'SoalMinatKuliahSuasanaKerjaController@get_data');
		Route::post('/insert', 'SoalMinatKuliahSuasanaKerjaController@submit_insert');
		Route::post('/update', 'SoalMinatKuliahSuasanaKerjaController@submit_update');
		Route::post('/delete', 'SoalMinatKuliahSuasanaKerjaController@submit_delete');
		Route::post('/upload-gambar', 'UploadController@upload_gambar');		
	});



	//KELOLA TES > MASTER SESI
	//master-sesi
	Route::group(['prefix'=>'master-sesi'], function(){
		Route::get('/', 'MasterSesiController@index');
		Route::get('/dt', 'MasterSesiController@datatable');
		Route::get('/get-data/{uuid}', 'MasterSesiController@get_data');
		Route::post('/insert', 'MasterSesiController@submit_insert');
		Route::post('/update', 'MasterSesiController@submit_update');
		Route::post('/delete', 'MasterSesiController@submit_delete');
	});

	//KELOLA TES > MASTER SESI
	//jenis-tes
	Route::group(['prefix'=>'template-tes'], function(){
		Route::get('/', 'MasterTemplateTesController@index');
		Route::get('/dt', 'MasterTemplateTesController@datatable_template');
		Route::get('/get-data/{uuid}', 'MasterTemplateTesController@get_data');
		Route::post('/insert', 'MasterTemplateTesController@submit_insert');
		Route::post('/update', 'MasterTemplateTesController@submit_update');
		Route::post('/delete', 'MasterTemplateTesController@submit_delete');
		Route::post('/upload-gambar', 'UploadController@upload_gambar_400_250');

		//detil
		Route::get('/detil/{uuid}', 'MasterTemplateTesController@index_detil');
		Route::get('/dt-detil/{uuid}', 'MasterTemplateTesController@datatable_detil');
		Route::get('/get-data-detil/{uuid}', 'MasterTemplateTesController@get_data_detil');
		Route::post('/insert-detil', 'MasterTemplateTesController@submit_insert_detil');
		Route::post('/update-detil', 'MasterTemplateTesController@submit_update_detil');
		Route::post('/delete-detil', 'MasterTemplateTesController@submit_delete_detil');
	});
	//TARIF PAKET TES
	Route::group(['prefix'=>'tarif-paket'], function(){
		Route::get('/', 'ManajemenTarifController@index');
		Route::get('/dt', 'ManajemenTarifController@datatable');
		Route::get('/get-data/{uuid}', 'ManajemenTarifController@get_data');
		Route::post('/insert', 'ManajemenTarifController@submit_insert');
		Route::post('/update', 'ManajemenTarifController@submit_update');
		Route::post('/delete', 'ManajemenTarifController@submit_delete');

		Route::get('/detil/{uuid}', 'ManajemenTarifController@index_detil');
		Route::get('/dt-detil/{uuid}', 'ManajemenTarifController@datatable_detil');
		Route::get('/get-data-detil/{uuid}', 'ManajemenTarifController@get_data_detil');
		Route::post('/insert-detil', 'ManajemenTarifController@submit_insert_detil');
		Route::post('/update-detil', 'ManajemenTarifController@submit_update_detil');
		Route::post('/delete-detil', 'ManajemenTarifController@submit_delete_detil');
		
	});
	//KELOLA TES > MANAJEMEN SESI
	//managemen-sesi
	Route::group(['prefix'=>'manajemen-sesi'], function(){

		Route::get('/', 'ManajemenSesiTesController@index_tahun');
		Route::get('/explore/{tahun}', 'ManajemenSesiTesController@index_biro');
		Route::get('/explore/{tahun}/{biro}', 'ManajemenSesiTesController@index_provinsi');
		Route::get('/explore/{tahun}/{biro}/{provinsi}', 'ManajemenSesiTesController@index_lokasi');
		Route::get('/explore/{tahun}/{biro}/{provinsi}/{lokasi}', 'ManajemenSesiTesController@index_tes');


		Route::get('/dt', 'ManajemenSesiTesController@datatable');
		Route::get('/get-data/{uuid}', 'ManajemenSesiTesController@get_data');
		Route::get('/get-info-user/{id_user}', 'ManajemenSesiTesController@get_info_user');
		Route::post('/insert', 'ManajemenSesiTesController@submit_insert');
		Route::post('/update', 'ManajemenSesiTesController@submit_update');
		Route::post('/update-info', 'ManajemenSesiTesController@submit_update_info');
		Route::post('/update-waktu', 'ManajemenSesiTesController@submit_update_waktu');
		Route::post('/update-asesor', 'ManajemenSesiTesController@submit_update_asesor');
		Route::post('/update-status', 'ManajemenSesiTesController@submit_update_status');
		Route::post('/upload-soal-firebase', 'ManajemenSesiTesController@submit_upload_soal_firebase');

		Route::post('/delete', 'ManajemenSesiTesController@submit_delete');
 		Route::post('/upload-gambar', 'UploadController@upload_gambar_400_250');
 		Route::post('/upload-ttd', 'UploadController@upload_gambar_250_150');
 		Route::post('/upload-excel', 'UploadController@upload_excel');
 		Route::post('/publish-all-peserta', 'ManajemenSesiTesController@publish_all_peserta');
 		Route::post('/batal-publish-all-peserta', 'ManajemenSesiTesController@batal_publish_all_peserta');
 		Route::post('/batal-skoring-all-peserta', 'ManajemenSesiTesController@batal_skoring_all_peserta');
 		Route::get('/generate-import-excel/{uuid}/{token}/{filename}', 'UploadController@generate_import_data_excel_peserta');

		//detil
		Route::get('/generate-tabel-tambah-peserta', 'ManajemenSesiTesController@generate_tabel_tambah_peserta');
		Route::get('/generate-tabel-salin-peserta', 'ManajemenSesiTesController@generate_tabel_salin_peserta');
		Route::get('/detil/{uuid}', 'ManajemenSesiTesController@detil');
		Route::get('/dt-peserta/{uuid}', 'ManajemenSesiTesController@datatable_peserta');
		Route::get('/dt-tambah-peserta/{uuid}', 'ManajemenSesiTesController@datatable_tambah_peserta');
		Route::get('/dt-salin-peserta/{uuid}', 'ManajemenSesiTesController@datatable_salin_peserta');
		Route::get('/get-data-peserta/{uuid}', 'ManajemenSesiTesController@get_data_peserta');
		Route::post('/update-gambar', 'ManajemenSesiTesController@submit_update_gambar');
		Route::post('/submit-upload-peserta', 'ManajemenSesiTesController@submit_upload_peserta');
		Route::post('/insert-peserta', 'ManajemenSesiTesController@submit_insert_peserta');
		Route::post('/salin-peserta', 'ManajemenSesiTesController@submit_salin_peserta');
		Route::post('/delete-peserta', 'ManajemenSesiTesController@submit_delete_peserta');
		Route::post('/reset-peserta', 'ManajemenSesiTesController@submit_reset_peserta');
		Route::post('/clear-peserta', 'ManajemenSesiTesController@submit_clear_peserta');
		Route::post('/skoring-peserta', 'ManajemenSesiTesController@submit_skoring');

		Route::get('/view-result/{uuid}', 'ManajemenSesiTesController@view_result_tes');
		Route::get('/input-rekom/{uuid}', 'ManajemenSesiTesController@view_input_rekom');
		Route::get('/get-saran/{uuid}', 'ManajemenSesiTesController@get_saran_hasil_tes');
		Route::post('/update-saran', 'ManajemenSesiTesController@update_saran');
		Route::post('/batal-publish', 'ManajemenSesiTesController@submit_batal_publish');

		//export
		Route::get('/export-excel/{uuid}', 'ExportDataController@export_excel');

		//addons
		Route::get('/page-mapping-smk/{uuid}', 'ManajemenSesiMappingSMKController@page_mapping_smk');
		Route::get('/dt-mapping-smk/{uuid}', 'ManajemenSesiMappingSMKController@datatable_mapping_smk');
		Route::post('/insert-mapping-smk', 'ManajemenSesiMappingSMKController@insert_mapping_smk');
		Route::post('/delete-mapping-smk', 'ManajemenSesiMappingSMKController@delete_mapping_smk');


	});

	//TEMPLATE-SARAN	
	Route::group(['prefix'=>'template-saran-rekom'], function(){
		Route::get('/', 'TemplateSaranController@index');
		Route::get('/dt', 'TemplateSaranController@datatable');
		Route::get('/get-data/{uuid}', 'TemplateSaranController@get_data');
		Route::post('/insert', 'TemplateSaranController@submit_insert');
		Route::post('/update', 'TemplateSaranController@submit_update');
		Route::post('/delete', 'TemplateSaranController@submit_delete');
	});

	//skala-kecerdasan-majemuk
	Route::group(['prefix'=>'skala-kecerdasan-majemuk'], function(){
		Route::get('/', 'SoalKecerdasanMajemuk@index');
		Route::get('/dt', 'SoalKecerdasanMajemuk@datatable');
		Route::get('/lihat-soal/{uuid}', 'SoalKecerdasanMajemuk@view_soal');
		Route::get('/get-data/{uuid}', 'SoalKecerdasanMajemuk@get_data');
		Route::post('/insert', 'SoalKecerdasanMajemuk@submit_insert');
		Route::post('/update', 'SoalKecerdasanMajemuk@submit_update');
		Route::post('/delete', 'SoalKecerdasanMajemuk@submit_delete');
	});

	//skala-gaya-pekerjaan
	Route::group(['prefix'=>'skala-gaya-pekerjaan'], function(){
		Route::get('/', 'SoalGayaPekerjaan@index');
		Route::get('/dt', 'SoalGayaPekerjaan@datatable');
		Route::get('/lihat-soal/{uuid}', 'SoalGayaPekerjaan@view_soal');
		Route::get('/get-data/{uuid}', 'SoalGayaPekerjaan@get_data');
		Route::post('/insert', 'SoalGayaPekerjaan@submit_insert');
		Route::post('/update', 'SoalGayaPekerjaan@submit_update');
		Route::post('/delete', 'SoalGayaPekerjaan@submit_delete');
	});

	///soal-pauli-kraeplin 
	Route::group(['prefix'=>'soal-pauli-kraeplin'], function(){
		Route::get('/', 'SoalPauliKraeplinController@index');
		Route::get('/dt', 'SoalPauliKraeplinController@datatable');
		Route::get('/lihat-soal/{uuid}', 'SoalPauliKraeplinController@view_soal');
		Route::get('/get-data/{uuid}', 'SoalPauliKraeplinController@get_data');
		Route::post('/insert', 'SoalPauliKraeplinController@submit_insert');
		Route::post('/update', 'SoalPauliKraeplinController@submit_update');
		Route::post('/delete', 'SoalPauliKraeplinController@submit_delete');
	});

});

//ROUTER INFORMASI

Route::get('/view-info/{uuid}','InfoCerdasController@view_info_publik');

