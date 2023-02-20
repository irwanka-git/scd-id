<?php
Route::get('/render/{token_submit}','CetakController@render_report_individu');
Route::get('/render-lampiran-pmk/{token_submit}','CetakController@render_lampiran_penjurusan_kuliah');
Route::get('/pdf/{token_submit}','CetakController@pdf_report_individu');
Route::get('/result/{token_submit}','CetakController@download_pdf_result_tes');
Route::get('/vcf/{token_submit}','CetakController@cek_file_seri');  //verifikasi dan cek file qrcode
Route::get('/generate-kop/{id_quiz}', 'CetakController@generate_header');
Route::get('/download/{no_seri}','CetakController@download_pdf_no_seri');
Route::get('/generate-zip/{id_quiz}','CetakController@generate_all_result_zip');
Route::get('/generate-zip-doc/{id_quiz}','CetakController@generate_all_result_zip_doc'); 