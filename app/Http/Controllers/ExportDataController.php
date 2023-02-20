<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;
use Image;
use Response;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class ExportDataController extends Controller
{
     
     function export_excel($uuid){
        //export-excel
         $quiz = DB::table('quiz_sesi')->where('uuid', $uuid)->first();

         //EXPORT PEMINATAN SMA
         if($quiz->skoring_tabel=='skoring_minat_sma'){
            $path = $this->export_excel_skoring_minat_sma($quiz->id_quiz);
         }

         //EXPORT PEMINATAN MAN
         if($quiz->skoring_tabel=='skoring_minat_man'){
            $path = $this->export_excel_skoring_minat_man($quiz->id_quiz);
         }

         //EXPORT PEMINATAN SMK
         if($quiz->skoring_tabel=='skoring_minat_smk'){
            $path = $this->export_excel_skoring_minat_smk($quiz->id_quiz);
         }

         //EXPORT PEMINATAN LENGKAP
         if($quiz->skoring_tabel=='skoring_minat_lengkap'){
            $path = $this->export_excel_skoring_minat_lengkap($quiz->id_quiz);
         }

         //EXPORT PEMINATAN SMA VERSI 2
         if($quiz->skoring_tabel=='skoring_minat_sma_v2'){
            $path = $this->export_excel_skoring_minat_sma_v2($quiz->id_quiz);
         }

         //EXPORT PENJURUSAN KULIAH VERSI 2
         if($quiz->skoring_tabel=='skoring_penjurusan_kuliah_v2'){
            $path = $this->export_excel_skoring_penjurusan_kuliah_v2($quiz->id_quiz);
         }

         $filename = $quiz->token." - ".$quiz->nama_sesi.".xlsx";
         return response()->download(storage_path($path), $filename, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
         ]);

         //return redirect(url('report/'.$filename));
     }

    function export_excel_skoring_minat_sma($id_quiz){

        loadHelper('function');
        $quiz = DB::table('quiz_sesi')->where('id_quiz', $id_quiz)->first();
        $lokasi = DB::table('lokasi')->where('id_lokasi', $quiz->id_lokasi)->first();
        $inputFileName = public_path('template/skoring_minat_sma.xlsx');
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setLoadSheetsOnly(["SKORING","MINAT","REKOM"]);
        $spreadsheet = $reader->load($inputFileName);

        $data_hasil  = DB::select("select p.*, q.klasifikasi as klasifikasi_iq  
                                    from (SELECT
                                            d.nama_pengguna,
                                            d.jenis_kelamin,
                                            d.organisasi,
                                            b.* 
                                        FROM
                                            quiz_sesi_user AS a,
                                            skoring_minat_sma AS b,
                                            users AS d 
                                        WHERE
                                            a.id_quiz = b.id_quiz 
                                            AND a.id_user = b.id_user 
                                            AND a.id_user = d.id 
                                            AND a.skoring = 1 
                                            AND a.status_hasil = 1
                                            AND b.selesai_skoring = 1
                                            and a.id_quiz = $id_quiz
                                            order by  d.nama_pengguna asc) as p 
                                    left join ref_konversi_iq as q on p.tpa_iq = q.skor_x order by p.nama_pengguna asc ");

        //style border
        $styleBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        //sheet REKOM
        $worksheet1 = $spreadsheet->getSheetByName("REKOM");
        $worksheet1->setCellValue('C2', $lokasi->nama_lokasi);
        $worksheet1->setCellValue('C3', tgl_indo_lengkap($quiz->tanggal));
        
        $awal = 6;
        $baris = 6;
        $no = 1;

        foreach ($data_hasil as $r){
            $worksheet1->setCellValue('A'.$baris, $no);$no++;
            $worksheet1->setCellValue('B'.$baris, $r->nama_pengguna);
            $worksheet1->setCellValue('C'.$baris, $r->jenis_kelamin);
            $worksheet1->setCellValue('D'.$baris, $r->organisasi);
            $worksheet1->setCellValue('E'.$baris, $r->skor_iq);
            $worksheet1->setCellValue('F'.$baris, $r->klasifikasi_iq);
            $worksheet1->setCellValue('G'.$baris, str_replace("_"," ",$r->rekom_minat));
            $worksheet1->setCellValue('H'.$baris, str_replace("_"," ",$r->rekom_sikap_pelajaran));
            $worksheet1->setCellValue('I'.$baris, str_replace("_"," ",$r->rekom_tmi));
            $worksheet1->setCellValue('J'.$baris, str_replace("_"," ",$r->rekom_akhir));
            $baris++;
        }        
        $worksheet1->getStyle('A'.$awal.':J'.($baris-1))->applyFromArray($styleBorder);


        


        //sheet MINAT
        $worksheet4 = $spreadsheet->getSheetByName("MINAT");
        $worksheet4->setCellValue('C2', $lokasi->nama_lokasi);
        $worksheet4->setCellValue('C3', tgl_indo_lengkap($quiz->tanggal));
        
        $awal = 7;
        $baris = 7;
        $no = 1;
        $arrayData = [];

        $klasifikasi_minat = DB::table('ref_klasifikasi_minat_sma')->get();
        $ref_klasifikasi_minat = [];
        foreach($klasifikasi_minat as $km){
            $ref_klasifikasi_minat[$km->skor] = str_replace("_"," ",$km->klasifikasi);
        }

        foreach ($data_hasil as $r){
            $temp = [
                $no, 
                $r->nama_pengguna,
                $r->jenis_kelamin,
                $r->organisasi,
                $ref_klasifikasi_minat[(int)$r->minat_sains],
                $ref_klasifikasi_minat[(int)$r->minat_humaniora],
                $ref_klasifikasi_minat[(int)$r->minat_bahasa],
                ];
            array_push($arrayData, $temp);
            $no++;
            $baris++;
        } 
        $worksheet4->fromArray(
                        $arrayData,  // The data to set
                        NULL,        // Array values with this value will not be set
                        'A7'         // Top left coordinate of the worksheet range where
                    );

        $worksheet4->getStyle('A'.$awal.':G'.($baris-1))->applyFromArray($styleBorder);


        //sheet SKORING
        $worksheet2 = $spreadsheet->getSheetByName("SKORING");
        $worksheet2->setCellValue('C2', $lokasi->nama_lokasi);
        $worksheet2->setCellValue('C3', tgl_indo_lengkap($quiz->tanggal));
        
        $awal = 7;
        $baris = 7;
        $no = 1;
        $arrayData = [];

        foreach ($data_hasil as $r){
            $temp = [
                $no, 
                $r->nama_pengguna,
                $r->jenis_kelamin,
                $r->organisasi,
                $r->tpa_iu,
                $r->tpa_pv,
                $r->tpa_pk,
                $r->tpa_pa,
                $r->tpa_ps,
                $r->tpa_pm,
                $r->tpa_kt,
                $r->skor_iq,
                $r->minat_sains,
                $r->minat_humaniora,
                $r->minat_bahasa,
                $r->sikap_agm,
                $r->sikap_pkn,
                $r->sikap_ind,
                $r->sikap_eng,
                $r->sikap_mat,
                $r->sikap_fis,
                $r->sikap_bio,
                $r->sikap_eko,
                $r->sikap_sej,
                $r->sikap_geo,
                $r->tmi_ilmu_alam,
                $r->tmi_ilmu_sosial,
                $r->tipojung_e,
                $r->tipojung_i,
                $r->tipojung_s,
                $r->tipojung_n,
                $r->tipojung_t,
                $r->tipojung_f,
                $r->tipojung_j,
                $r->tipojung_p,
                $r->tipojung_kode,
                $r->pribadi_motivasi,
                $r->pribadi_juang,
                $r->pribadi_yakin,
                $r->pribadi_percaya,
                $r->pribadi_konsep,
                $r->pribadi_kreativitas,
                $r->pribadi_mimpin,
                $r->pribadi_entrepreneur,
                $r->pribadi_stress,
                $r->pribadi_emosi,
                $r->pribadi_sosial,
                $r->pribadi_empati,
                str_replace("_"," ",$r->rekom_minat),
                str_replace("_"," ",$r->rekom_sikap_pelajaran),
                str_replace("_"," ",$r->rekom_tmi),
                str_replace("_"," ",$r->rekom_akhir)
                ];
            array_push($arrayData, $temp);
            $no++;
            $baris++;
        } 
        $worksheet2->fromArray(
                        $arrayData,  // The data to set
                        NULL,        // Array values with this value will not be set
                        'A7'         // Top left coordinate of the worksheet range where
                    );

        $worksheet2->getStyle('A'.$awal.':AZ'.($baris-1))->applyFromArray($styleBorder);

        //save  as file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileexport = time().'_PEMINATAN_SMA.xlsx';
        $path_save = storage_path('report/'.$fileexport);
        $writer->save($path_save);

        return 'report/'.$fileexport;
    }

    function export_excel_skoring_minat_sma_v2($id_quiz){

        loadHelper('function');
        $quiz = DB::table('quiz_sesi')->where('id_quiz', $id_quiz)->first();
        $lokasi = DB::table('lokasi')->where('id_lokasi', $quiz->id_lokasi)->first();
        $inputFileName = public_path('template/skoring_minat_sma_v2.xlsx');
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setLoadSheetsOnly(["SKORING","MINAT_KULIAH","REKOM", "HASIL"]);
        $spreadsheet = $reader->load($inputFileName);
        
        $data_hasil  = DB::select("select p.*, q.klasifikasi as klasifikasi_iq  , z.mapel_utama, z.mapel_tambahan
                                    from (SELECT
                                            d.nama_pengguna,
                                            d.jenis_kelamin,
                                            d.organisasi,
                                            b.* , 
                                            get_nama_minat_kuliah_dinas(b.minat_dinas1) as nama_minat_dinas1,
                                            get_nama_minat_kuliah_dinas(b.minat_dinas2) as nama_minat_dinas2,
                                            get_nama_minat_kuliah_dinas(b.minat_dinas3) as nama_minat_dinas3,
                                                                                        
                                            get_nama_minat_kuliah_sains(b.minat_ipa1) as nama_minat_sains1,
                                            get_nama_minat_kuliah_sains(b.minat_ipa2) as nama_minat_sains2,
                                            get_nama_minat_kuliah_sains(b.minat_ipa3) as nama_minat_sains3,
                                            get_nama_minat_kuliah_sains(b.minat_ipa4) as nama_minat_sains4,
                                            get_nama_minat_kuliah_sains(b.minat_ipa5) as nama_minat_sains5,
                                            
                                            get_nama_minat_kuliah_sosial(b.minat_ips1) as nama_minat_sosial1,
                                            get_nama_minat_kuliah_sosial(b.minat_ips2) as nama_minat_sosial2,
                                            get_nama_minat_kuliah_sosial(b.minat_ips3) as nama_minat_sosial3,
                                            get_nama_minat_kuliah_sosial(b.minat_ips4) as nama_minat_sosial4,
                                            get_nama_minat_kuliah_sosial(b.minat_ips5) as nama_minat_sosial5
                                        FROM
                                            quiz_sesi_user AS a,
                                            skoring_minat_sma_v2 AS b,
                                            users AS d
                                        WHERE
                                            a.id_quiz = b.id_quiz 
                                            AND a.id_user = b.id_user 
                                            AND a.id_user = d.id 
                                            AND a.skoring = 1 
                                            AND a.status_hasil = 1
                                            AND b.selesai_skoring = 1
                                            and a.id_quiz = $id_quiz
                                            order by  d.nama_pengguna asc) as p 
                                    left join ref_konversi_iq as q on p.tpa_iq = q.skor_x 
                                    left join ref_kelas_minat_sma as z on p.rekom_kelas = z.kelas
                                    order by p.nama_pengguna asc ");

        //style border
        $styleBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        //sheet HASIL
        $worksheet1 = $spreadsheet->getSheetByName("HASIL");
        $worksheet1->setCellValue('C2', $lokasi->nama_lokasi);
        $worksheet1->setCellValue('C3', tgl_indo_lengkap($quiz->tanggal));
        
        $awal = 6;
        $baris = $awal;
        $no = 1;

        foreach ($data_hasil as $r){
            $worksheet1->setCellValue('A'.$baris, $no);$no++;
            $worksheet1->setCellValue('B'.$baris, $r->nama_pengguna);
            $worksheet1->setCellValue('C'.$baris, $r->jenis_kelamin);
            $worksheet1->setCellValue('D'.$baris, $r->organisasi);
            $worksheet1->setCellValue('E'.$baris, $r->skor_iq);
            $worksheet1->setCellValue('F'.$baris, $r->klasifikasi_iq);
            $worksheet1->setCellValue('G'.$baris, str_replace("_"," ",$r->rekom_akhir));
            $worksheet1->setCellValue('H'.$baris, str_replace("_"," ",$r->rekom_kelas));
            $worksheet1->setCellValue('I'.$baris, str_replace("_"," ",$r->mapel_utama));
            $worksheet1->setCellValue('J'.$baris, str_replace("_"," ",$r->mapel_tambahan));
            $baris++;
        }        
        $worksheet1->getStyle('A'.$awal.':J'.($baris-1))->applyFromArray($styleBorder);


        

        //sheet REKOM
        $worksheet2 = $spreadsheet->getSheetByName("REKOM");
        $worksheet2->setCellValue('C2', $lokasi->nama_lokasi);
        $worksheet2->setCellValue('C3', tgl_indo_lengkap($quiz->tanggal));
        
        $awal = 7;
        $baris = $awal;
        $no = 1;

        foreach ($data_hasil as $r){
            $worksheet2->setCellValue('A'.$baris, $no);$no++;
            $worksheet2->setCellValue('B'.$baris, $r->nama_pengguna);
            $worksheet2->setCellValue('C'.$baris, $r->jenis_kelamin);
            $worksheet2->setCellValue('D'.$baris, $r->organisasi);
            $worksheet2->setCellValue('E'.$baris, $r->skor_iq);
            $worksheet2->setCellValue('F'.$baris, str_replace("_"," ",$r->rekom_minat));
            $worksheet2->setCellValue('G'.$baris, str_replace("_"," ",$r->rekom_sikap_pelajaran));
            $worksheet2->setCellValue('H'.$baris, str_replace("_"," ",$r->rekom_akhir));
            $worksheet2->setCellValue('I'.$baris, str_replace("_"," ",$r->nama_minat_dinas1));
            $worksheet2->setCellValue('J'.$baris, str_replace("_"," ",$r->nama_minat_sains1));
            $worksheet2->setCellValue('K'.$baris, str_replace("_"," ",$r->nama_minat_sosial1));
            $worksheet2->setCellValue('L'.$baris, str_replace("_"," ",$r->rekom_kelas));
            $baris++;
        }        
        $worksheet2->getStyle('A'.$awal.':L'.($baris-1))->applyFromArray($styleBorder);


        //sheet REKOM
        $worksheet2 = $spreadsheet->getSheetByName("MINAT_KULIAH");
        $worksheet2->setCellValue('C2', $lokasi->nama_lokasi);
        $worksheet2->setCellValue('C3', tgl_indo_lengkap($quiz->tanggal));
        
        $awal = 7;
        $baris = $awal;
        $no = 1;

        foreach ($data_hasil as $r){
            $info_minat_kedinasan = "(1) ".$r->nama_minat_dinas1.", ";
            $info_minat_kedinasan .= "(2) ".$r->nama_minat_dinas2.", ";
            $info_minat_kedinasan .= "(3) ".$r->nama_minat_dinas3;

            $info_minat_sains = "(1) ".$r->nama_minat_sains1.", ";
            $info_minat_sains .= "(2) ".$r->nama_minat_sains2.", ";
            $info_minat_sains .= "(3) ".$r->nama_minat_sains3.", ";
            $info_minat_sains .= "(4) ".$r->nama_minat_sains4.", ";
            $info_minat_sains .= "(5) ".$r->nama_minat_sains5;

            $info_minat_sosial = "(1) ".$r->nama_minat_sosial1.", ";
            $info_minat_sosial .= "(2) ".$r->nama_minat_sosial2.", ";
            $info_minat_sosial .= "(3) ".$r->nama_minat_sosial3.", ";
            $info_minat_sosial .= "(4) ".$r->nama_minat_sosial4.", ";
            $info_minat_sosial .= "(5) ".$r->nama_minat_sosial5;
             
            $worksheet2->setCellValue('A'.$baris, $no);$no++;
            $worksheet2->setCellValue('B'.$baris, $r->nama_pengguna);
            $worksheet2->setCellValue('C'.$baris, $r->jenis_kelamin);
            $worksheet2->setCellValue('D'.$baris, $r->organisasi);
            $worksheet2->setCellValue('E'.$baris, $r->skor_iq);
            $worksheet2->setCellValue('F'.$baris, str_replace("_"," ",$info_minat_kedinasan));
            $worksheet2->setCellValue('G'.$baris, str_replace("_"," ",$info_minat_sains));
            $worksheet2->setCellValue('H'.$baris, str_replace("_"," ",$info_minat_sosial));
            $baris++;
        }        
        $worksheet2->getStyle('A'.$awal.':H'.($baris-1))->applyFromArray($styleBorder);


        //sheet SKORING
        $worksheet4 = $spreadsheet->getSheetByName("SKORING");
        $worksheet4->setCellValue('C2', $lokasi->nama_lokasi);
        $worksheet4->setCellValue('C3', tgl_indo_lengkap($quiz->tanggal));
        
        $awal = 7;
        $baris = 7;
        $no = 1;
        $arrayData = [];

        foreach ($data_hasil as $r){
            $temp = [
                $no, 
                $r->nama_pengguna,
                $r->jenis_kelamin,
                $r->organisasi,
                $r->tpa_iu,
                $r->tpa_pv,
                $r->tpa_pk,
                $r->tpa_pa,
                $r->tpa_ps,
                $r->tpa_pm,
                $r->tpa_kt,
                $r->skor_iq,
                $r->minat_sains,
                $r->minat_humaniora,
                $r->minat_bahasa,
                $r->sikap_agm,
                $r->sikap_pkn,
                $r->sikap_ind,
                $r->sikap_eng,
                $r->sikap_mat,
                $r->sikap_fis,
                $r->sikap_bio,
                $r->sikap_eko,
                $r->sikap_sej,
                $r->sikap_geo,
                $r->tipojung_e,
                $r->tipojung_i,
                $r->tipojung_s,
                $r->tipojung_n,
                $r->tipojung_t,
                $r->tipojung_f,
                $r->tipojung_j,
                $r->tipojung_p,
                $r->tipojung_kode,
                $r->pribadi_motivasi,
                $r->pribadi_juang,
                $r->pribadi_yakin,
                $r->pribadi_percaya,
                $r->pribadi_konsep,
                $r->pribadi_kreativitas,
                $r->pribadi_mimpin,
                $r->pribadi_entrepreneur,
                $r->pribadi_stress,
                $r->pribadi_emosi,
                $r->pribadi_sosial,
                $r->pribadi_empati
                ];
            array_push($arrayData, $temp);
            $no++;
            $baris++;
        } 
        $worksheet4->fromArray(
                        $arrayData,  // The data to set
                        NULL,        // Array values with this value will not be set
                        'A7'         // Top left coordinate of the worksheet range where
                    );

        $worksheet4->getStyle('A'.$awal.':AT'.($baris-1))->applyFromArray($styleBorder);

        //save  as file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileexport = time().'_PEMINATAN_SMA_V2.xlsx';
        $path_save = storage_path('report/'.$fileexport);
        $writer->save($path_save);

        return 'report/'.$fileexport;
    }


    function export_excel_skoring_penjurusan_kuliah_v2($id_quiz){

        loadHelper('function');
        $quiz = DB::table('quiz_sesi')->where('id_quiz', $id_quiz)->first();
        $lokasi = DB::table('lokasi')->where('id_lokasi', $quiz->id_lokasi)->first();
        $inputFileName = public_path('template/skoring_penjurusan_kuliah_v2.xlsx');
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setLoadSheetsOnly(["SKORING","MINAT"]);
        $spreadsheet = $reader->load($inputFileName);
        
        $data_hasil  = DB::select("select p.*, q.klasifikasi as klasifikasi_iq
                                    from (SELECT
                                            d.nama_pengguna,
                                            d.jenis_kelamin,
                                            d.organisasi,
                                            b.* , 
                                            get_nama_minat_kuliah_dinas(b.minat_dinas1) as nama_minat_dinas1,
                                            get_nama_minat_kuliah_dinas(b.minat_dinas2) as nama_minat_dinas2,
                                            get_nama_minat_kuliah_dinas(b.minat_dinas3) as nama_minat_dinas3,
                                                                                        
                                            get_nama_minat_kuliah_sains(b.minat_ipa1) as nama_minat_sains1,
                                            get_nama_minat_kuliah_sains(b.minat_ipa2) as nama_minat_sains2,
                                            get_nama_minat_kuliah_sains(b.minat_ipa3) as nama_minat_sains3,
                                            get_nama_minat_kuliah_sains(b.minat_ipa4) as nama_minat_sains4,
                                            get_nama_minat_kuliah_sains(b.minat_ipa5) as nama_minat_sains5,
                                            
                                            get_nama_minat_kuliah_sosial(b.minat_ips1) as nama_minat_sosial1,
                                            get_nama_minat_kuliah_sosial(b.minat_ips2) as nama_minat_sosial2,
                                            get_nama_minat_kuliah_sosial(b.minat_ips3) as nama_minat_sosial3,
                                            get_nama_minat_kuliah_sosial(b.minat_ips4) as nama_minat_sosial4,
                                            get_nama_minat_kuliah_sosial(b.minat_ips5) as nama_minat_sosial5, 

                                            get_nama_minat_kuliah_agama(b.minat_agm1) as nama_minat_agama1,
                                            get_nama_minat_kuliah_agama(b.minat_agm2) as nama_minat_agama2,
                                            get_nama_minat_kuliah_agama(b.minat_agm3) as nama_minat_agama3,
                                            get_nama_minat_kuliah_agama(b.minat_agm4) as nama_minat_agama4,
                                            get_nama_minat_kuliah_agama(b.minat_agm5) as nama_minat_agama5,

                                            get_nama_gaya_pekerjaan(b.rangking_gp1) as gaya_pekerjaan1,
                                            get_nama_gaya_pekerjaan(b.rangking_gp2) as gaya_pekerjaan2,
                                            get_nama_gaya_pekerjaan(b.rangking_gp3) as gaya_pekerjaan3 

                                        FROM
                                            quiz_sesi_user AS a,
                                            skoring_penjurusan_kuliah_v2 AS b,
                                            users AS d
                                        WHERE
                                            a.id_quiz = b.id_quiz 
                                            AND a.id_user = b.id_user 
                                            AND a.id_user = d.id 
                                            AND a.skoring = 1 
                                            AND a.status_hasil = 1
                                            AND b.selesai_skoring = 1
                                            and a.id_quiz = $id_quiz
                                            order by  d.nama_pengguna asc) as p 
                                    left join ref_konversi_iq_105 as q on p.tpa_iq = q.skor_x 
                                    order by p.nama_pengguna asc ");

        //style border
        $styleBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        //sheet HASIL
        $worksheet1 = $spreadsheet->getSheetByName("MINAT");
        $worksheet1->setCellValue('C2', $lokasi->nama_lokasi);
        $worksheet1->setCellValue('C3', tgl_indo_lengkap($quiz->tanggal));
        
        $awal = 7;
        $baris = $awal;
        $no = 1;

        foreach ($data_hasil as $r){
            $worksheet1->setCellValue('A'.$baris, $no);$no++;
            $worksheet1->setCellValue('B'.$baris, $r->nama_pengguna);
            $worksheet1->setCellValue('C'.$baris, $r->jenis_kelamin);
            $worksheet1->setCellValue('D'.$baris, $r->organisasi);
            $worksheet1->setCellValue('E'.$baris, $r->skor_iq);
            $worksheet1->setCellValue('F'.$baris, $r->klasifikasi_iq);

            $info_minat_kedinasan = "(1) ".$r->nama_minat_dinas1.", ";
            $info_minat_kedinasan .= "(2) ".$r->nama_minat_dinas2.", ";
            $info_minat_kedinasan .= "(3) ".$r->nama_minat_dinas3;

            $info_minat_sains = "(1) ".$r->nama_minat_sains1.", ";
            $info_minat_sains .= "(2) ".$r->nama_minat_sains2.", ";
            $info_minat_sains .= "(3) ".$r->nama_minat_sains3.", ";
            $info_minat_sains .= "(4) ".$r->nama_minat_sains4.", ";
            $info_minat_sains .= "(5) ".$r->nama_minat_sains5;

            $info_minat_sosial = "(1) ".$r->nama_minat_sosial1.", ";
            $info_minat_sosial .= "(2) ".$r->nama_minat_sosial2.", ";
            $info_minat_sosial .= "(3) ".$r->nama_minat_sosial3.", ";
            $info_minat_sosial .= "(4) ".$r->nama_minat_sosial4.", ";
            $info_minat_sosial .= "(5) ".$r->nama_minat_sosial5;

            $info_minat_agama = "(1) ".$r->nama_minat_agama1.", ";
            $info_minat_agama .= "(2) ".$r->nama_minat_agama2.", ";
            $info_minat_agama .= "(3) ".$r->nama_minat_agama3.", ";
            $info_minat_agama .= "(4) ".$r->nama_minat_agama4.", ";
            $info_minat_agama .= "(5) ".$r->nama_minat_agama5;


            // $info_gaya_pekerjaan = "(1) ".$r->gaya_pekerjaan1.", ";
            // $info_gaya_pekerjaan .= "(2) ".$r->gaya_pekerjaan2.", ";
            // $info_gaya_pekerjaan .= "(3) ".$r->gaya_pekerjaan3;

            $worksheet1->setCellValue('G'.$baris, str_replace("_"," ",$info_minat_kedinasan));
            $worksheet1->setCellValue('H'.$baris, str_replace("_"," ",$info_minat_sains));
            $worksheet1->setCellValue('I'.$baris, str_replace("_"," ",$info_minat_sosial));
            $worksheet1->setCellValue('J'.$baris, str_replace("_"," ",$info_minat_agama));
            $worksheet1->setCellValue('K'.$baris, str_replace("_"," ","(1) ".$r->gaya_pekerjaan1));
            $worksheet1->setCellValue('L'.$baris, str_replace("_"," ","(2) ".$r->gaya_pekerjaan2));
            $worksheet1->setCellValue('M'.$baris, str_replace("_"," ","(3) ".$r->gaya_pekerjaan3));
            $baris++;
        }        
        $worksheet1->getStyle('A'.$awal.':M'.($baris-1))->applyFromArray($styleBorder);


        //sheet SKORING
        $worksheet4 = $spreadsheet->getSheetByName("SKORING");
        $worksheet4->setCellValue('C2', $lokasi->nama_lokasi);
        $worksheet4->setCellValue('C3', tgl_indo_lengkap($quiz->tanggal));
        
        $awal = 7;
        $baris = 7;
        $no = 1;
        $arrayData = [];

        foreach ($data_hasil as $r){
            $temp = [
                $no, 
                $r->nama_pengguna,
                $r->jenis_kelamin,
                $r->organisasi,
                $r->tpa_iu,
                $r->tpa_pv,
                $r->tpa_pk,
                $r->tpa_pa,
                $r->tpa_ps,
                $r->tpa_pm,
                $r->tpa_kt,
                $r->skor_iq,
                $r->klasifikasi_iq,
                $r->sikap_agm,
                $r->sikap_pkn,
                $r->sikap_ind,
                $r->sikap_eng,
                $r->sikap_mat,
                $r->sikap_fis,
                $r->sikap_kim,
                $r->sikap_bio,
                $r->sikap_eko,
                $r->sikap_sos,
                $r->sikap_sej,
                $r->sikap_geo,
                $r->sikap_sbd,
                $r->sikap_org,
                $r->sikap_mlk,
                $r->sikap_tik
                ];
            array_push($arrayData, $temp);
            $no++;
            $baris++;
        } 
        $worksheet4->fromArray(
                        $arrayData,  // The data to set
                        NULL,        // Array values with this value will not be set
                        'A7'         // Top left coordinate of the worksheet range where
                    );

        $worksheet4->getStyle('A'.$awal.':AC'.($baris-1))->applyFromArray($styleBorder);

        //save  as file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileexport = time().'_PENJURUSAN_KULIAH_V2.xlsx';
        $path_save = storage_path('report/'.$fileexport);
        $writer->save($path_save);

        return 'report/'.$fileexport;
    }


    function export_excel_skoring_minat_man($id_quiz){

        loadHelper('function');
        $quiz = DB::table('quiz_sesi')->where('id_quiz', $id_quiz)->first();
        $lokasi = DB::table('lokasi')->where('id_lokasi', $quiz->id_lokasi)->first();
        $inputFileName = public_path('template/skoring_minat_man.xlsx');
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setLoadSheetsOnly(["SKORING","MINAT","REKOM"]);
        $spreadsheet = $reader->load($inputFileName);

        $data_hasil  = DB::select("select p.*, q.klasifikasi as klasifikasi_iq  
                                    from (SELECT
                                            d.nama_pengguna,
                                            d.jenis_kelamin,
                                            d.organisasi,
                                            b.* 
                                        FROM
                                            quiz_sesi_user AS a,
                                            skoring_minat_man AS b,
                                            users AS d 
                                        WHERE
                                            a.id_quiz = b.id_quiz 
                                            AND a.id_user = b.id_user 
                                            AND a.id_user = d.id 
                                            AND a.skoring = 1 
                                            AND a.status_hasil = 1
                                            AND b.selesai_skoring = 1
                                            and a.id_quiz = $id_quiz
                                            order by  d.nama_pengguna asc) as p 
                                    left join ref_konversi_iq as q on p.tpa_iq = q.skor_x order by p.nama_pengguna asc ");

        //style border
        $styleBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        //sheet REKOM
        $worksheet1 = $spreadsheet->getSheetByName("REKOM");
        $worksheet1->setCellValue('C2', $lokasi->nama_lokasi);
        $worksheet1->setCellValue('C3', tgl_indo_lengkap($quiz->tanggal));
        
        $awal = 6;
        $baris = 6;
        $no = 1;

        foreach ($data_hasil as $r){
            $worksheet1->setCellValue('A'.$baris, $no);$no++;
            $worksheet1->setCellValue('B'.$baris, $r->nama_pengguna);
            $worksheet1->setCellValue('C'.$baris, $r->jenis_kelamin);
            $worksheet1->setCellValue('D'.$baris, $r->organisasi);
            $worksheet1->setCellValue('E'.$baris, $r->skor_iq);
            $worksheet1->setCellValue('F'.$baris, $r->klasifikasi_iq);
            $worksheet1->setCellValue('G'.$baris, str_replace("_"," ",$r->rekom_minat));
            $worksheet1->setCellValue('H'.$baris, str_replace("_"," ",$r->rekom_sikap_pelajaran));
            $worksheet1->setCellValue('I'.$baris, str_replace("_"," ",$r->rekom_tmi));
            $worksheet1->setCellValue('J'.$baris, str_replace("_"," ",$r->rekom_akhir));
            $baris++;
        }        
        $worksheet1->getStyle('A'.$awal.':J'.($baris-1))->applyFromArray($styleBorder);


        //sheet MINAT
        $worksheet4 = $spreadsheet->getSheetByName("MINAT");
        $worksheet4->setCellValue('C2', $lokasi->nama_lokasi);
        $worksheet4->setCellValue('C3', tgl_indo_lengkap($quiz->tanggal));
        
        $awal = 7;
        $baris = 7;
        $no = 1;
        $arrayData = [];

        $klasifikasi_minat = DB::table('ref_klasifikasi_minat_man')->get();
        $ref_klasifikasi_minat = [];
        foreach($klasifikasi_minat as $km){
            $ref_klasifikasi_minat[$km->skor] = str_replace("_"," ",$km->klasifikasi);
        }

        foreach ($data_hasil as $r){
            $temp = [
                $no, 
                $r->nama_pengguna,
                $r->jenis_kelamin,
                $r->organisasi,
                $ref_klasifikasi_minat[(int)$r->minat_sains],
                $ref_klasifikasi_minat[(int)$r->minat_humaniora],
                $ref_klasifikasi_minat[(int)$r->minat_bahasa],
                $ref_klasifikasi_minat[(int)$r->minat_agama],
                ];
            array_push($arrayData, $temp);
            $no++;
            $baris++;
        } 
        $worksheet4->fromArray(
                        $arrayData,  // The data to set
                        NULL,        // Array values with this value will not be set
                        'A7'         // Top left coordinate of the worksheet range where
                    );

        $worksheet4->getStyle('A'.$awal.':H'.($baris-1))->applyFromArray($styleBorder);


        //sheet SKORING
        $worksheet2 = $spreadsheet->getSheetByName("SKORING");
        $worksheet2->setCellValue('C2', $lokasi->nama_lokasi);
        $worksheet2->setCellValue('C3', tgl_indo_lengkap($quiz->tanggal));
        
        $awal = 7;
        $baris = 7;
        $no = 1;
        $arrayData = [];

        foreach ($data_hasil as $r){
            $temp = [
                $no, 
                $r->nama_pengguna,
                $r->jenis_kelamin,
                $r->organisasi,
                $r->tpa_iu,
                $r->tpa_pv,
                $r->tpa_pk,
                $r->tpa_pa,
                $r->tpa_ps,
                $r->tpa_pm,
                $r->tpa_kt,
                $r->skor_iq,
                $r->minat_sains,
                $r->minat_humaniora,
                $r->minat_bahasa,
                $r->minat_agama,
                $r->sikap_agm,
                $r->sikap_pkn,
                $r->sikap_ind,
                $r->sikap_eng,
                $r->sikap_mat,
                $r->sikap_fis,
                $r->sikap_bio,
                $r->sikap_eko,
                $r->sikap_sej,
                $r->sikap_geo,
                $r->tmi_ilmu_alam,
                $r->tmi_ilmu_sosial,
                $r->tipojung_e,
                $r->tipojung_i,
                $r->tipojung_s,
                $r->tipojung_n,
                $r->tipojung_t,
                $r->tipojung_f,
                $r->tipojung_j,
                $r->tipojung_p,
                $r->tipojung_kode,
                $r->pribadi_motivasi,
                $r->pribadi_juang,
                $r->pribadi_yakin,
                $r->pribadi_percaya,
                $r->pribadi_konsep,
                $r->pribadi_kreativitas,
                $r->pribadi_mimpin,
                $r->pribadi_entrepreneur,
                $r->pribadi_stress,
                $r->pribadi_emosi,
                $r->pribadi_sosial,
                $r->pribadi_empati,
                str_replace("_"," ",$r->rekom_minat),
                str_replace("_"," ",$r->rekom_sikap_pelajaran),
                str_replace("_"," ",$r->rekom_tmi),
                str_replace("_"," ",$r->rekom_akhir)
                ];
            array_push($arrayData, $temp);
            $no++;
            $baris++;
        } 
        $worksheet2->fromArray(
                        $arrayData,  // The data to set
                        NULL,        // Array values with this value will not be set
                        'A7'         // Top left coordinate of the worksheet range where
                    );

        $worksheet2->getStyle('A'.$awal.':BA'.($baris-1))->applyFromArray($styleBorder);

        //save  as file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileexport = time().'_PEMINATAN_MAN.xlsx';
        $path_save = storage_path('report/'.$fileexport);
        $writer->save($path_save);

        return 'report/'.$fileexport;
    }

    function export_excel_skoring_minat_smk($id_quiz){
        loadHelper('function');
        $quiz = DB::table('quiz_sesi')->where('id_quiz', $id_quiz)->first();
        $lokasi = DB::table('lokasi')->where('id_lokasi', $quiz->id_lokasi)->first();
        $inputFileName = public_path('template/skoring_minat_smk.xlsx');
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setLoadSheetsOnly(["SKORING","MINAT","REKOM"]);
        $spreadsheet = $reader->load($inputFileName);

        $data_hasil  = DB::select("select p.*, q.klasifikasi as klasifikasi_iq  
                                    from (SELECT
                                            d.nama_pengguna,
                                            d.jenis_kelamin,
                                            d.organisasi,
                                            b.* 
                                        FROM
                                            quiz_sesi_user AS a,
                                            skoring_minat_smk AS b,
                                            users AS d 
                                        WHERE
                                            a.id_quiz = b.id_quiz 
                                            AND a.id_user = b.id_user 
                                            AND a.id_user = d.id 
                                            AND a.skoring = 1 
                                            AND a.status_hasil = 1
                                            AND b.selesai_skoring = 1
                                            and a.id_quiz = $id_quiz
                                            order by  d.nama_pengguna asc) as p 
                                    left join ref_konversi_iq as q on p.tpa_iq = q.skor_x order by p.nama_pengguna asc ");

        $klasifikasi_minat = DB::table('soal_peminatan_smk')->get();
        $ref_klasifikasi_minat = [];
        foreach($klasifikasi_minat as $km){
            $ref_klasifikasi_minat[$km->nomor] = $km->keterangan;
        }

        //style border
        $styleBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        //sheet REKOM
        $worksheet1 = $spreadsheet->getSheetByName("REKOM");
        $worksheet1->setCellValue('C2', $lokasi->nama_lokasi);
        $worksheet1->setCellValue('C3', tgl_indo_lengkap($quiz->tanggal));
        
        $awal = 6;
        $baris = 6;
        $no = 1;

        foreach ($data_hasil as $r){
            $worksheet1->setCellValue('A'.$baris, $no);$no++;
            $worksheet1->setCellValue('B'.$baris, $r->nama_pengguna);
            $worksheet1->setCellValue('C'.$baris, $r->jenis_kelamin);
            $worksheet1->setCellValue('D'.$baris, $r->organisasi);
            $worksheet1->setCellValue('E'.$baris, $r->skor_iq);
            $worksheet1->setCellValue('F'.$baris, $r->klasifikasi_iq);
            $worksheet1->setCellValue('G'.$baris, str_replace("_"," ",$r->rekom_minat));
            $worksheet1->setCellValue('H'.$baris, str_replace("_"," ",$r->rekom_sikap_pelajaran));
            $worksheet1->setCellValue('I'.$baris, str_replace("_"," ",$r->rekom_tmi));
            $worksheet1->setCellValue('J'.$baris, str_replace("_"," ",$r->rekom_akhir));
            $baris++;
        }        
        $worksheet1->getStyle('A'.$awal.':J'.($baris-1))->applyFromArray($styleBorder);


        //sheet MINAT
        $worksheet4 = $spreadsheet->getSheetByName("MINAT");
        $worksheet4->setCellValue('C2', $lokasi->nama_lokasi);
        $worksheet4->setCellValue('C3', tgl_indo_lengkap($quiz->tanggal));
        
        $awal = 7;
        $baris = 7;
        $no = 1;
        $arrayData = [];

        

        foreach ($data_hasil as $r){
            $temp = [
                $no, 
                $r->nama_pengguna,
                $r->jenis_kelamin,
                $r->organisasi,
                $ref_klasifikasi_minat[$r->minat_1],
                $ref_klasifikasi_minat[$r->minat_2],
                $ref_klasifikasi_minat[$r->minat_3],
                $ref_klasifikasi_minat[$r->minat_4],
                $ref_klasifikasi_minat[$r->minat_5],
                ];
            array_push($arrayData, $temp);
            $no++;
            $baris++;
        } 
        $worksheet4->fromArray(
                        $arrayData,  // The data to set
                        NULL,        // Array values with this value will not be set
                        'A7'         // Top left coordinate of the worksheet range where
                    );

        $worksheet4->getStyle('A'.$awal.':I'.($baris-1))->applyFromArray($styleBorder);


        //sheet SKORING
        $worksheet2 = $spreadsheet->getSheetByName("SKORING");
        $worksheet2->setCellValue('C2', $lokasi->nama_lokasi);
        $worksheet2->setCellValue('C3', tgl_indo_lengkap($quiz->tanggal));
        
        $awal = 7;
        $baris = 7;
        $no = 1;
        $arrayData = [];

        foreach ($data_hasil as $r){
            $temp = [
                $no, 
                $r->nama_pengguna,
                $r->jenis_kelamin,
                $r->organisasi,
                $r->tpa_iu,
                $r->tpa_pv,
                $r->tpa_pk,
                $r->tpa_pa,
                $r->tpa_ps,
                $r->tpa_pm,
                $r->tpa_kt,
                $r->skor_iq,
                $ref_klasifikasi_minat[$r->minat_1],
                $ref_klasifikasi_minat[$r->minat_2],
                $ref_klasifikasi_minat[$r->minat_3],
                $ref_klasifikasi_minat[$r->minat_4],
                $ref_klasifikasi_minat[$r->minat_5],
                $r->sikap_agm,
                $r->sikap_pkn,
                $r->sikap_ind,
                $r->sikap_eng,
                $r->sikap_mat,
                $r->sikap_fis,
                $r->sikap_bio,
                $r->sikap_eko,
                $r->sikap_sej,
                $r->sikap_geo,
                $r->tmi_ilmu_alam,
                $r->tmi_ilmu_sosial,
                $r->tipojung_e,
                $r->tipojung_i,
                $r->tipojung_s,
                $r->tipojung_n,
                $r->tipojung_t,
                $r->tipojung_f,
                $r->tipojung_j,
                $r->tipojung_p,
                $r->tipojung_kode,
                $r->pribadi_motivasi,
                $r->pribadi_juang,
                $r->pribadi_yakin,
                $r->pribadi_percaya,
                $r->pribadi_konsep,
                $r->pribadi_kreativitas,
                $r->pribadi_mimpin,
                $r->pribadi_entrepreneur,
                $r->pribadi_stress,
                $r->pribadi_emosi,
                $r->pribadi_sosial,
                $r->pribadi_empati,
                str_replace("_"," ",$r->rekom_minat),
                str_replace("_"," ",$r->rekom_sikap_pelajaran),
                str_replace("_"," ",$r->rekom_tmi),
                str_replace("_"," ",$r->rekom_akhir)
                ];
            array_push($arrayData, $temp);
            $no++;
            $baris++;
        } 
        $worksheet2->fromArray(
                        $arrayData,  // The data to set
                        NULL,        // Array values with this value will not be set
                        'A7'         // Top left coordinate of the worksheet range where
                    );

        $worksheet2->getStyle('A'.$awal.':BB'.($baris-1))->applyFromArray($styleBorder);

        //save  as file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileexport = time().'_PEMINATAN_SMK.xlsx';
        $path_save = storage_path('report/'.$fileexport);
        $writer->save($path_save);

        return 'report/'.$fileexport;
    }

    function export_excel_skoring_minat_lengkap($id_quiz){

        loadHelper('function');
        $quiz = DB::table('quiz_sesi')->where('id_quiz', $id_quiz)->first();
        $lokasi = DB::table('lokasi')->where('id_lokasi', $quiz->id_lokasi)->first();
        $inputFileName = public_path('template/skoring_minat_lengkap.xlsx');
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setLoadSheetsOnly(["SKOR","MINAT","IQ", "KELOMPOK"]);
        $spreadsheet = $reader->load($inputFileName);

        //sheet KELOMPOK

        $data_hasil  = DB::select("select 
                        b.nama_pengguna, b.unit_organisasi , b.organisasi, b.jenis_kelamin, 
                        d.jurusan as minat_kuliah_eksakta,
                        e.jurusan as minat_kuliah_sosial,
                        f.akronim as minat_sekolah_dinas,
                        g.kelompok as kelompok_ipa,
                        h.kelompok as kelompok_ips,
                        case when i.kelompok !='-' then concat(i.kelompok, ' (',f.akronim,')')  else '-' end as kelompok_sekolah_dinas 
                        from skoring_minat_lengkap as a , 
                        users as b ,  
                        soal_minat_kuliah_eksakta as d, 
                        soal_minat_kuliah_sosial as e,
                        ref_sekolah_dinas as f ,
                        ref_kelompok_minat_kuliah as g,
                        ref_kelompok_minat_kuliah as h,
                        ref_kelompok_minat_kuliah as i, 
                        quiz_sesi_user AS j
                        where a.id_quiz = $id_quiz and a.id_user = b.id 
                        and a.minat_ipa1 = d.urutan
                        and a.minat_ips1 = e.urutan
                        and a.minat_dinas1 = f.no
                        and d.id_kelompok = g.id
                        and e.id_kelompok = h.id
                        and f.id_kelompok = i.id 
                        and j.id_quiz = a.id_quiz and j.id_user = a.id_user
                        and j.skoring = 1 and j.status_hasil = 1 and a.selesai_skoring = 1
                        order by b.nama_pengguna asc 
                        ");

        //style border
        $styleBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        //sheet REKOM
        $worksheet1 = $spreadsheet->getSheetByName("KELOMPOK");
        $worksheet1->setCellValue('C2', $lokasi->nama_lokasi);
        $worksheet1->setCellValue('C3', tgl_indo_lengkap($quiz->tanggal));
        
        $awal = 7;
        $baris = 7;
        $no = 1;

        foreach ($data_hasil as $r){
            $worksheet1->setCellValue('A'.$baris, $no);$no++;
            $worksheet1->setCellValue('B'.$baris, $r->nama_pengguna);
            $worksheet1->setCellValue('C'.$baris, $r->jenis_kelamin);
            $worksheet1->setCellValue('D'.$baris, $r->organisasi);
            $worksheet1->setCellValue('E'.$baris, $r->unit_organisasi);
            $worksheet1->setCellValue('F'.$baris, $r->kelompok_ipa);
            $worksheet1->setCellValue('G'.$baris, $r->kelompok_ips);
            $worksheet1->setCellValue('H'.$baris, $r->kelompok_sekolah_dinas);
            $baris++;
        }        
        $worksheet1->getStyle('A'.$awal.':H'.($baris-1))->applyFromArray($styleBorder);



        //sheet IQ
        $data_hasil  = DB::select("select p.*, q.klasifikasi as klasifikasi_iq  
                                    from (SELECT
                                            d.nama_pengguna,
                                            d.jenis_kelamin,
                                            d.organisasi,
                                            b.* ,
                                            get_nama_minat_kuliah_dinas(b.minat_dinas1) as nama_minat_dinas1,
                                            get_nama_minat_kuliah_dinas(b.minat_dinas2) as nama_minat_dinas2,
                                            get_nama_minat_kuliah_dinas(b.minat_dinas3) as nama_minat_dinas3,
                                                                                        
                                            get_nama_minat_kuliah_sains(b.minat_ipa1) as nama_minat_sains1,
                                            get_nama_minat_kuliah_sains(b.minat_ipa2) as nama_minat_sains2,
                                            get_nama_minat_kuliah_sains(b.minat_ipa3) as nama_minat_sains3,
                                            get_nama_minat_kuliah_sains(b.minat_ipa4) as nama_minat_sains4,
                                            get_nama_minat_kuliah_sains(b.minat_ipa5) as nama_minat_sains5,
                                            
                                            get_nama_minat_kuliah_sosial(b.minat_ips1) as nama_minat_sosial1,
                                            get_nama_minat_kuliah_sosial(b.minat_ips2) as nama_minat_sosial2,
                                            get_nama_minat_kuliah_sosial(b.minat_ips3) as nama_minat_sosial3,
                                            get_nama_minat_kuliah_sosial(b.minat_ips4) as nama_minat_sosial4,
                                            get_nama_minat_kuliah_sosial(b.minat_ips5) as nama_minat_sosial5,
                                            
                                            get_nama_minat_suasana_kerja(b.suasana_kerja1) as nama_minat_suasana1,
                                            get_nama_minat_suasana_kerja(b.suasana_kerja2) as nama_minat_suasana2,
                                            get_nama_minat_suasana_kerja(b.suasana_kerja3) as nama_minat_suasana3
                                                                                        
                                        FROM
                                            quiz_sesi_user AS a,
                                            skoring_minat_lengkap AS b,
                                            users AS d 
                                        WHERE
                                            a.id_quiz = b.id_quiz 
                                            AND a.id_user = b.id_user 
                                            AND a.id_user = d.id 
                                            AND a.skoring = 1 
                                            AND a.status_hasil = 1
                                            AND b.selesai_skoring = 1
                                            and a.id_quiz = $id_quiz
                                            order by  d.nama_pengguna asc) as p 
                                    left join ref_konversi_iq as q on p.tpa_iq = q.skor_x order by p.nama_pengguna asc ");

        //style border
        $styleBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        //sheet REKOM
        $worksheet1 = $spreadsheet->getSheetByName("IQ");
        $worksheet1->setCellValue('C2', $lokasi->nama_lokasi);
        $worksheet1->setCellValue('C3', tgl_indo_lengkap($quiz->tanggal));
        
        $awal = 6;
        $baris = 6;
        $no = 1;

        foreach ($data_hasil as $r){
            $worksheet1->setCellValue('A'.$baris, $no);$no++;
            $worksheet1->setCellValue('B'.$baris, $r->nama_pengguna);
            $worksheet1->setCellValue('C'.$baris, $r->jenis_kelamin);
            $worksheet1->setCellValue('D'.$baris, $r->organisasi);
            $worksheet1->setCellValue('E'.$baris, $r->skor_iq);
            $worksheet1->setCellValue('F'.$baris, $r->klasifikasi_iq);
            $baris++;
        }        
        $worksheet1->getStyle('A'.$awal.':F'.($baris-1))->applyFromArray($styleBorder);
        


        


        //sheet MINAT
        $worksheet4 = $spreadsheet->getSheetByName("MINAT");
        $worksheet4->setCellValue('C2', $lokasi->nama_lokasi);
        $worksheet4->setCellValue('C3', tgl_indo_lengkap($quiz->tanggal));
        
        $awal = 7;
        $baris = 7;
        $no = 1;
        $arrayData = [];

        foreach ($data_hasil as $r){
            $info_minat_kedinasan = "(1) ".$r->nama_minat_dinas1.", ";
            $info_minat_kedinasan .= "(2) ".$r->nama_minat_dinas2.", ";
            $info_minat_kedinasan .= "(3) ".$r->nama_minat_dinas3;

            $info_minat_sains = "(1) ".$r->nama_minat_sains1.", ";
            $info_minat_sains .= "(2) ".$r->nama_minat_sains2.", ";
            $info_minat_sains .= "(3) ".$r->nama_minat_sains3.", ";
            $info_minat_sains .= "(4) ".$r->nama_minat_sains4.", ";
            $info_minat_sains .= "(5) ".$r->nama_minat_sains5;

            $info_minat_sosial = "(1) ".$r->nama_minat_sosial1.", ";
            $info_minat_sosial .= "(2) ".$r->nama_minat_sosial2.", ";
            $info_minat_sosial .= "(3) ".$r->nama_minat_sosial3.", ";
            $info_minat_sosial .= "(4) ".$r->nama_minat_sosial4.", ";
            $info_minat_sosial .= "(5) ".$r->nama_minat_sosial5;
             

            $info_suasana_kerja = "(1) ".$r->nama_minat_suasana1.", ";
            $info_suasana_kerja .= "(2) ".$r->nama_minat_suasana2.", ";
            $info_suasana_kerja .= "(3) ".$r->nama_minat_suasana3;

            $temp = [
                $no, 
                $r->nama_pengguna,
                $r->jenis_kelamin,
                $r->organisasi,
                $info_minat_kedinasan,
                $info_minat_sains,
                $info_minat_sosial,
                $info_suasana_kerja,
                ];
            array_push($arrayData, $temp);
            $no++;
            $baris++;
        } 
        $worksheet4->fromArray(
                        $arrayData,  // The data to set
                        NULL,        // Array values with this value will not be set
                        'A7'         // Top left coordinate of the worksheet range where
                    );

        $worksheet4->getStyle('A'.$awal.':H'.($baris-1))->applyFromArray($styleBorder);
        $worksheet4->getStyle('E'.$awal.':H'.($baris-1))->getAlignment()->setWrapText(true);
        //$worksheet4->getStyle('E'.$awal.':H'.($baris-1))->getAlignment()->setAlignment('center');



        //sheet SKORING
        $worksheet2 = $spreadsheet->getSheetByName("SKOR");
        $worksheet2->setCellValue('C2', $lokasi->nama_lokasi);
        $worksheet2->setCellValue('C3', tgl_indo_lengkap($quiz->tanggal));
        
        $awal = 7;
        $baris = 7;
        $no = 1;
        $arrayData = [];

        foreach ($data_hasil as $r){
            $temp = [
                $no, 
                $r->nama_pengguna,
                $r->jenis_kelamin,
                $r->organisasi,
                $r->tpa_iu,
                $r->tpa_pv,
                $r->tpa_pk,
                $r->tpa_pa,
                $r->tpa_ps,
                $r->tpa_pm,
                $r->tpa_kt,
                $r->skor_iq,
                $r->sikap_agm,
                $r->sikap_pkn,
                $r->sikap_ind,
                $r->sikap_eng,
                $r->sikap_mat,
                $r->sikap_fis,
                $r->sikap_bio,
                $r->sikap_eko,
                $r->sikap_sej,
                $r->sikap_geo,
                $r->tipojung_e,
                $r->tipojung_i,
                $r->tipojung_s,
                $r->tipojung_n,
                $r->tipojung_t,
                $r->tipojung_f,
                $r->tipojung_j,
                $r->tipojung_p,
                $r->tipojung_kode,
                $r->pribadi_motivasi,
                $r->pribadi_juang,
                $r->pribadi_yakin,
                $r->pribadi_percaya,
                $r->pribadi_konsep,
                $r->pribadi_kreativitas,
                $r->pribadi_mimpin,
                $r->pribadi_entrepreneur,
                $r->pribadi_stress,
                $r->pribadi_emosi,
                $r->pribadi_sosial,
                $r->pribadi_empati
                ];
            array_push($arrayData, $temp);
            $no++;
            $baris++;
        } 
        $worksheet2->fromArray(
                        $arrayData,  // The data to set
                        NULL,        // Array values with this value will not be set
                        'A7'         // Top left coordinate of the worksheet range where
                    );

        $worksheet2->getStyle('A'.$awal.':AQ'.($baris-1))->applyFromArray($styleBorder);

        //save  as file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileexport = time().'_PSIKOTES_LENGKAP.xlsx';
        $path_save = storage_path('report/'.$fileexport);
        $writer->save($path_save);

        return 'report/'.$fileexport;
    }

}
