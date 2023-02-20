<?php

Route::group(['prefix'=>'api'], function(){
		Route::post('/login', 'ApiUserController@submit_login');
		Route::get('/get-info-kontak', 'ApiUserController@get_info_contact');
});


Route::group(['prefix'=>'api', "middleware"=>['auth.api']], function(){
		
		Route::get('/logout', 'ApiUserController@logout');
		Route::get('/current-user', 'ApiUserController@get_current_user');
		Route::get('/get-info-user', 'ApiUserController@get_info_user');

		Route::get('/get-profil-user', 'ApiUserController@get_profil_user');
		Route::post('/update-profil-user', 'ApiUserController@update_profil_user');
		Route::post('/upload-avatar-user', 'ApiUserController@upload_avatar_user');

		Route::post('/submit-change-password', 'ApiUserController@submit_change_password');
		Route::get('/new-token-user', 'ApiUserController@gen_new_token_bearer');
		//contoh API DATA
		// Route::get('/list-data', 'ApiDataController@list_data');
		// Route::post('/post-data', 'ApiDataController@post_data');
		
		//GET INFO CERDAS		
		Route::get('get-list-info-cerdas', 'ApiDataController@get_list_info_cerdas');
		Route::get('get-detil-info-cerdas/{uuid}', 'ApiDataController@get_detil_info_cerdas');

		//POSTING JAWABAN
		Route::post('/submit-quiz', 'ApiDataController@submit_quiz');

		//GET QUIZ USER		
		Route::get('get-quiz-user', 'ApiDataController@get_quiz_user');
		Route::get('get-detil-quiz/{token}', 'ApiDataController@get_detil_quiz');
		//GET CONFIGRUASU QUIZ
		Route::get('/get-open-quiz/{token}', 'ApiDataController@get_open_quiz');
		Route::get('/send-start-quiz/{token}', 'ApiDataController@send_start_quiz');
		Route::get('/get-config-sesi/{token}', 'ApiDataController@get_config_sesi');
		Route::get('/get-list-sesi/{token}', 'ApiDataController@get_list_sesi');
		
		//JSON SOAL_SOAL
		Route::get('/soal-break', 'ApiDataController@get_soal_break');
		//SOAL KOGINITIF
		Route::get('/soal-kognitif/{paket}/{bidang}', 'ApiDataController@get_soal_kognitif');
		//SOAL SKALA PEMINATAN SMA/SMK/MAN
		Route::get('/soal-skala-peminatan-sma', 'ApiDataController@get_soal_peminatan_sma');
		Route::get('/soal-skala-peminatan-sma-demo', 'ApiDataController@get_soal_peminatan_sma_demo');
		Route::get('/soal-skala-peminatan-man', 'ApiDataController@get_soal_peminatan_man');
		Route::get('/soal-skala-peminatan-smk', 'ApiDataController@get_soal_peminatan_smk');
		Route::get('/soal-skala-peminatan-smk/{paket}', 'ApiDataController@get_soal_peminatan_smk_paket');
		Route::get('/soal-skala-peminatan-smk-demo', 'ApiDataController@get_soal_peminatan_smk_demo');
		//SOAK SIKAP PELAJARAN (TIPE RATING)
		Route::get('/soal-sikap-pelajaran', 'ApiDataController@get_soal_sikap_pelajaran');
		Route::get('/soal-sikap-pelajaran-demo', 'ApiDataController@get_soal_sikap_pelajaran_demo');
		//SOAL TEST MINAT INDONESIA
		Route::get('/soal-tmi', 'ApiDataController@get_soal_tes_minat_indonesia');
		Route::get('/soal-tmi-demo', 'ApiDataController@get_soal_tes_minat_indonesia_demo');
		//BELUM
		Route::get('/soal-tes-karakteristik-pribadi', 'ApiDataController@get_soal_karakteristik_pribadi');
		Route::get('/soal-tes-karakteristik-pribadi-demo', 'ApiDataController@get_soal_karakteristik_pribadi_demo');
		Route::get('/soal-tipologi-jung', 'ApiDataController@get_soal_tipologi_jung');
		Route::get('/soal-tipologi-jung-demo', 'ApiDataController@get_soal_tipologi_jung_demo');

		//PEMINATAN KULIAH
		Route::get('/soal-minat-kuliah-eksakta', 'ApiDataController@get_soal_minat_kuliah_eksakta');
		Route::get('/soal-minat-kuliah-eksakta-demo', 'ApiDataController@get_soal_minat_kuliah_eksakta_demo');
		Route::get('/soal-minat-kuliah-sosial', 'ApiDataController@get_soal_minat_kuliah_sosial');
		Route::get('/soal-minat-kuliah-kedinasan', 'ApiDataController@get_soal_minat_kuliah_kedinasan');
		Route::get('/soal-minat-kuliah-kedinasan-demo', 'ApiDataController@get_soal_minat_kuliah_kedinasan_demo');
		Route::get('/soal-minat-kuliah-agama', 'ApiDataController@get_soal_minat_kuliah_agama');
		Route::get('/soal-minat-kuliah-suasana-kerja', 'ApiDataController@get_soal_minat_suasana_kerja');
		Route::get('/soal-pmk-sikap-pelajaran', 'ApiDataController@get_soal_pmk_sikap_pelajaran');

		//BAKAT DAN KARIR
		
		Route::get('/soal-kecerdasan-majemuk', 'ApiDataController@get_soal_kecerdasan_majemuk');
		Route::get('/soal-gaya-pekerjaan', 'ApiDataController@get_soal_gaya_pekerjaan');
		Route::get('/soal-kecerdasan-majemuk-demo', 'ApiDataController@get_soal_kecerdasan_majemuk_demo');
		Route::get('/soal-gaya-pekerjaan-demo', 'ApiDataController@get_soal_gaya_pekerjaan_demo');
		//END JSON SOAL SOAL

		//SALAM PEMBUKA
		//get-salam-pembuka
		Route::get('/get-salam-pembuka/{token}', 'ApiDataController@get_salam_pembuka');

		//GET INFO SESI TES
		Route::get('/get-info-session/{token}', 'ApiDataController@get_info_session_quiz');
		
		

});

Route::get('/gen_bearer/{id}', function ($id) {
    return Crypt::encrypt($id);
});

