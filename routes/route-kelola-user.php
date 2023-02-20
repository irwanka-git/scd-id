<?php 
Route::group(['prefix'=>'manajemen-user-admin'], function(){
    Route::get('/', 'UserAdminController@index');
    Route::get('/dt', 'UserAdminController@datatable');
    Route::get('/get-data/{uuid}', 'UserAdminController@get_data');
    Route::post('/insert', 'UserAdminController@submit_insert');
    Route::post('/update', 'UserAdminController@submit_update');
    Route::post('/update_password', 'UserAdminController@submit_update_password');
    Route::post('/delete', 'UserAdminController@submit_delete');
});
//################################ MANAJEMEN USER AUTHOR ########################################
Route::group(['prefix'=>'manajemen-user-author'], function(){
    Route::get('/', 'UserAuthorController@index');
    Route::get('/dt', 'UserAuthorController@datatable');
    Route::get('/get-data/{uuid}', 'UserAuthorController@get_data');
    Route::post('/insert', 'UserAuthorController@submit_insert');
    Route::post('/update', 'UserAuthorController@submit_update');
    Route::post('/update_password', 'UserAuthorController@submit_update_password');
    Route::post('/delete', 'UserAuthorController@submit_delete');
});
//################################ MANAJEMEN USER Asesor ########################################
Route::group(['prefix'=>'manajemen-user-asesor'], function(){
    Route::get('/', 'UserAsesorController@index');
    Route::get('/dt', 'UserAsesorController@datatable');
    Route::get('/get-data/{uuid}', 'UserAsesorController@get_data');
    Route::post('/insert', 'UserAsesorController@submit_insert');
    Route::post('/update', 'UserAsesorController@submit_update');
    Route::post('/update_password', 'UserAsesorController@submit_update_password');
    Route::post('/delete', 'UserAsesorController@submit_delete');
});
//################################ MANAJEMEN USER Student ########################################
Route::group(['prefix'=>'manajemen-user-peserta'], function(){
    Route::get('/', 'UserPesertaController@index');
    Route::get('/dt', 'UserPesertaController@datatable');
    Route::get('/get-data/{uuid}', 'UserPesertaController@get_data');
    Route::post('/insert', 'UserPesertaController@submit_insert');
    Route::post('/update', 'UserPesertaController@submit_update');
    Route::post('/update_password', 'UserPesertaController@submit_update_password');
    Route::post('/delete', 'UserPesertaController@submit_delete');
    Route::post('/revoke', 'UserPesertaController@submit_revoke');
});

//MANAJEMEN AKUN BIRO
Route::group(['prefix'=>'manajemen-akun-biro'], function(){
    Route::get('/', 'AkunBiroController@index');
    Route::get('/dt', 'AkunBiroController@datatable');
    Route::get('/get-data/{uuid}', 'AkunBiroController@get_data');
    Route::post('/insert', 'AkunBiroController@submit_insert');
    Route::post('/update', 'AkunBiroController@submit_update');
    Route::post('/update_password', 'AkunBiroController@submit_update_password');
    Route::post('/delete', 'AkunBiroController@submit_delete');
});

//PROFIL BIRO
Route::group(['prefix'=>'manajemen-profil-biro'], function(){
    Route::get('/', 'ProfilBiroController@index');
    Route::post('/update', 'ProfilBiroController@submit_update');
    Route::post('/upload-gambar', 'UploadController@upload_gambar_kop');
});