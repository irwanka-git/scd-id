<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Storage;
use Illuminate\Support\Facades\Storage as StorageLaravel;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;
use DB;

class ExportWordReport
{
    //
     static function GenerateReportWordQuizPeserta($id_quiz, $id_user){
            $quiz = DB::table('quiz_sesi')->where('id_quiz', $id_quiz)->first();
            $result = false;
            switch ($quiz->skoring_tabel) {
                case 'skoring_iq_eq':
                    $result = ExportWordReport::GenerateReportWordTESIQEQV1($id_quiz, $id_user);
                     
                    break;
                default:
                    // code...
                    break;
            }
            return $result;
      }

      static function GenerateReportWordTESIQEQV1($id_quiz, $id_user){
            loadHelper('skoring');
            $quiz_sesi = DB::table('quiz_sesi')->where('id_quiz', $id_quiz)->first();
            $quiz_sesi_user = DB::table('quiz_sesi_user')->where('id_quiz', $id_quiz)->where('id_user', $id_user)->first();
            $token = $quiz_sesi->token;
            $file_template = public_path('template/report-word/'.$token.'.docx');
            if(file_exists($file_template)==false){
                return false;
            }

            if (is_dir(public_path('report/'.$token))==false){
                mkdir(public_path('report/'.$token));
            }

            $result = DB::select("SELECT
                                        p.*,
                                        q.klasifikasi AS klasifikasi_iq 
                                    FROM
                                        (
                                        SELECT
                                            d.nama_pengguna,
                                            d.jenis_kelamin,
                                            d.organisasi,
                                            d.username,
                                            b.* 
                                        FROM
                                            quiz_sesi_user AS a,
                                            skoring_iq_eq AS b,
                                            users AS d 
                                        WHERE
                                            a.id_quiz = b.id_quiz 
                                            AND a.id_user = b.id_user 
                                            AND a.id_user = d.id 
                                            AND a.skoring = 1  
                                            AND b.selesai_skoring = 1 
                                            AND a.id_quiz = $id_quiz  
                                            AND a.id_user = $id_user  
                                        ORDER BY
                                            d.nama_pengguna ASC 
                                        ) AS p
                                        LEFT JOIN ref_konversi_iq AS q ON p.tpa_iq = q.skor_x 
                                    ORDER BY
                                        p.nama_pengguna ASC");
             
             
            foreach ($result as $r){

                    $username = $r->username;
                    $username = str_replace(" ","", $username);
                    $nama_peserta = strtoupper($r->nama_pengguna);
                    $nama_peserta = str_replace(" ","-",$nama_peserta);
                    $nama_peserta = str_replace("`","",$nama_peserta);
                    $nama_peserta = str_replace("\"","",$nama_peserta);
                    $nama_peserta = str_replace("'","",$nama_peserta);
                    $nama_peserta = str_replace("'","",$nama_peserta);
                    $nama_peserta = str_replace(".","",$nama_peserta);
                    $nama_peserta = str_replace(",","",$nama_peserta);
                    $nama_peserta = strtoupper($nama_peserta)."-".strtoupper($username);
                    $filename = $nama_peserta.".docx";
                    $pathname = 'report/'.$token.'/'.$filename;
                    $pathToSave = public_path($pathname);

                    $templateProcessor = new TemplateProcessor($file_template);
                    
                    // set klasifikasi IQ (kognitif)

                    $SRIU="";   $RDIU="";   $SDIU="";   $TGIU="";   $STIU="";
                    $SRPV="";   $RDPV="";   $SDPV="";   $TGPV="";   $STPV="";
                    $SRPK="";   $RDPK="";   $SDPK="";   $TGPK="";   $STPK="";
                    $SRPA="";   $RDPA="";   $SDPA="";   $TGPA="";   $STPA="";
                    $SRPS="";   $RDPS="";   $SDPS="";   $TGPS="";   $STPS="";
                    $SRPM="";   $RDPM="";   $SDPM="";   $TGPM="";   $STPM="";
                    $SRKT="";   $RDKT="";   $SDKT="";   $TGKT="";   $STKT="";
                    $SRDT="";   $RDDT="";   $SDDT="";   $TGDT="";   $STDT="";

                    $klasifikasi = get_skor_predikat($r->tpa_iu,'skor','klasifikasi','ref_skala_kd_informasi_umum');
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "IU");${$var} = "✓";
                    $templateProcessor->setValue('SRIU', $SRIU);
                    $templateProcessor->setValue('RDIU', $RDIU);
                    $templateProcessor->setValue('SDIU', $SDIU);
                    $templateProcessor->setValue('TGIU', $TGIU);
                    $templateProcessor->setValue('STIU', $STIU);

                    $klasifikasi = get_skor_predikat($r->tpa_pv,'skor','klasifikasi','ref_skala_kd_penalaran_verbal');
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "PV");${$var} = "✓";
                    $templateProcessor->setValue('SRPV', $SRPV);
                    $templateProcessor->setValue('RDPV', $RDPV);
                    $templateProcessor->setValue('SDPV', $SDPV);
                    $templateProcessor->setValue('TGPV', $TGPV);
                    $templateProcessor->setValue('STPV', $STPV);

                    $klasifikasi = get_skor_predikat($r->tpa_pk,'skor','klasifikasi','ref_skala_kd_penalaran_kuantitatif');
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "PK");${$var} = "✓";
                    $templateProcessor->setValue('SRPK', $SRPK);
                    $templateProcessor->setValue('RDPK', $RDPK);
                    $templateProcessor->setValue('SDPK', $SDPK);
                    $templateProcessor->setValue('TGPK', $TGPK);
                    $templateProcessor->setValue('STPK', $STPK);

                    $klasifikasi = get_skor_predikat($r->tpa_pa,'skor','klasifikasi','ref_skala_kd_penalaran_abstrak');
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "PA");${$var} = "✓";
                    $templateProcessor->setValue('SRPA', $SRPA);
                    $templateProcessor->setValue('RDPA', $RDPA);
                    $templateProcessor->setValue('SDPA', $SDPA);
                    $templateProcessor->setValue('TGPA', $TGPA);
                    $templateProcessor->setValue('STPA', $STPA);

                    $klasifikasi = get_skor_predikat($r->tpa_ps,'skor','klasifikasi','ref_skala_kd_penalaran_spasial');
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "PS");${$var} = "✓";
                    $templateProcessor->setValue('SRPS', $SRPS);
                    $templateProcessor->setValue('RDPS', $RDPS);
                    $templateProcessor->setValue('SDPS', $SDPS);
                    $templateProcessor->setValue('TGPS', $TGPS);
                    $templateProcessor->setValue('STPS', $STPS);

                    $klasifikasi = get_skor_predikat($r->tpa_pm,'skor','klasifikasi','ref_skala_kd_penalaran_mekanika');
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "PM");${$var} = "✓";
                    $templateProcessor->setValue('SRPM', $SRPM);
                    $templateProcessor->setValue('RDPM', $RDPM);
                    $templateProcessor->setValue('SDPM', $SDPM);
                    $templateProcessor->setValue('TGPM', $TGPM);
                    $templateProcessor->setValue('STPM', $STPM);

                    $klasifikasi = get_skor_predikat($r->tpa_kt,'skor','klasifikasi','ref_skala_kd_cepat_teliti');
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "KT");${$var} = "✓";
                    $templateProcessor->setValue('SRKT', $SRKT);
                    $templateProcessor->setValue('RDKT', $RDKT);
                    $templateProcessor->setValue('SDKT', $SDKT);
                    $templateProcessor->setValue('TGKT', $TGKT);
                    $templateProcessor->setValue('STKT', $STKT);
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "DT");${$var} = "✓";
                    $templateProcessor->setValue('SRDT', $SRDT);
                    $templateProcessor->setValue('RDDT', $RDDT);
                    $templateProcessor->setValue('SDDT', $SDDT);
                    $templateProcessor->setValue('TGDT', $TGDT);
                    $templateProcessor->setValue('STDT', $STDT);

                    // set klasifikasi EQ - ASPEK SIKAP KERJA 

                    $SRMB="";   $RDMB="";   $SDMB="";   $TGMB="";   $STMB="";
                    $SRDJ="";   $RDDJ="";   $SDDJ="";   $TGDJ="";   $STDJ="";
                    $SRPP="";   $RDPP="";   $SDPP="";   $TGPP="";   $STPP="";
                    $SRPR="";   $RDPR="";   $SDPR="";   $TGPR="";   $STPR="";
                    $SRDS="";   $RDDS="";   $SDDS="";   $TGDS="";   $STDS="";
                    $SRKTL="";  $RDKTL="";   $SDKTL="";   $TGKTL="";   $STKTL="";
                    $SRKJ="";   $RDKJ="";   $SDKJ="";   $TGKJ="";      $STKJ="";

                    $klasifikasi = get_skor_predikat($r->skor_motivasi,'skor','klasifikasi','ref_karakter_pribadi');
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "MB");${$var} = "✓";
                    $templateProcessor->setValue('SRMB', $SRMB);
                    $templateProcessor->setValue('RDMB', $RDMB);
                    $templateProcessor->setValue('SDMB', $SDMB);
                    $templateProcessor->setValue('TGMB', $TGMB);
                    $templateProcessor->setValue('STMB', $STMB);

                    $klasifikasi = get_skor_predikat($r->skor_daya_juang,'skor','klasifikasi','ref_karakter_pribadi');
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "DJ");${$var} = "✓";
                    $templateProcessor->setValue('SRDJ', $SRDJ);
                    $templateProcessor->setValue('RDDJ', $RDDJ);
                    $templateProcessor->setValue('SDDJ', $SDDJ);
                    $templateProcessor->setValue('TGDJ', $TGDJ);
                    $templateProcessor->setValue('STDJ', $STDJ);

                    $klasifikasi = get_skor_predikat($r->skor_kepemimpinan,'skor','klasifikasi','ref_karakter_pribadi');
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "PP");${$var} = "✓";
                    $templateProcessor->setValue('SRPP', $SRPP);
                    $templateProcessor->setValue('RDPP', $RDPP);
                    $templateProcessor->setValue('SDPP', $SDPP);
                    $templateProcessor->setValue('TGPP', $TGPP);
                    $templateProcessor->setValue('STPP', $STPP);


                    $klasifikasi = get_skor_predikat($r->skor_kreativitas,'skor','klasifikasi','ref_karakter_pribadi');
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "PR");${$var} = "✓";
                    $templateProcessor->setValue('SRPR', $SRPR);
                    $templateProcessor->setValue('RDPR', $RDPR);
                    $templateProcessor->setValue('SDPR', $SDPR);
                    $templateProcessor->setValue('TGPR', $TGPR);
                    $templateProcessor->setValue('STPR', $STPR);

                    $klasifikasi = get_skor_predikat($r->skor_daya_tahan_stress,'skor','klasifikasi','ref_karakter_pribadi');
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "DS");${$var} = "✓";
                    $templateProcessor->setValue('SRDS', $SRDS);
                    $templateProcessor->setValue('RDDS', $RDDS);
                    $templateProcessor->setValue('SDDS', $SDDS);
                    $templateProcessor->setValue('TGDS', $TGDS);
                    $templateProcessor->setValue('STDS', $STDS);

                    $klasifikasi = get_skor_predikat($r->skor_ketelitian,'skor','klasifikasi','ref_karakter_pribadi');
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "KTL");${$var} = "✓";
                    $templateProcessor->setValue('SRKTL', $SRKTL);
                    $templateProcessor->setValue('RDKTL', $RDKTL);
                    $templateProcessor->setValue('SDKTL', $SDKTL);
                    $templateProcessor->setValue('TGKTL', $TGKTL);
                    $templateProcessor->setValue('STKTL', $STKTL);

                    $klasifikasi = get_skor_predikat($r->skor_kecepatan_kerja,'skor','klasifikasi','ref_karakter_pribadi');
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "KJ");${$var} = "✓";
                    $templateProcessor->setValue('SRKJ', $SRKJ);
                    $templateProcessor->setValue('RDKJ', $RDKJ);
                    $templateProcessor->setValue('SDKJ', $SDKJ);
                    $templateProcessor->setValue('TGKJ', $TGKJ);
                    $templateProcessor->setValue('STKJ', $STKJ);


                    $SRKD="";   $RDKD="";   $SDKD="";   $TGKD="";   $STKD="";
                    $SRCD="";   $RDCD="";   $SDCD="";   $TGCD="";   $STCD="";
                    $SRKND="";  $RDKND="";  $SDKND="";  $TGKND="";  $STKND="";
                    $SRRE="";   $RDRE="";   $SDRE="";   $TGRE="";   $STRE="";
                    $SRKS="";   $RDKS="";   $SDKS="";   $TGKS="";   $STKS="";
                    $SREM="";   $RDEM="";   $SDEM="";   $TGEM="";   $STEM="";
                    $SRKM="";   $RDKM="";   $SDKM="";   $TGKM="";   $STKM="";

                    $klasifikasi = get_skor_predikat($r->skor_keyakinan_diri,'skor','klasifikasi','ref_karakter_pribadi');
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "KD");${$var} = "✓";
                    $templateProcessor->setValue('SRKD', $SRKD);
                    $templateProcessor->setValue('RDKD', $RDKD);
                    $templateProcessor->setValue('SDKD', $SDKD);
                    $templateProcessor->setValue('TGKD', $TGKD);
                    $templateProcessor->setValue('STKD', $STKD);

                    $klasifikasi = get_skor_predikat($r->skor_percaya_diri,'skor','klasifikasi','ref_karakter_pribadi');
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "CD");${$var} = "✓";
                    $templateProcessor->setValue('SRCD', $SRCD);
                    $templateProcessor->setValue('RDCD', $RDCD);
                    $templateProcessor->setValue('SDCD', $SDCD);
                    $templateProcessor->setValue('TGCD', $TGCD);
                    $templateProcessor->setValue('STCD', $STCD);

                    $klasifikasi = get_skor_predikat($r->skor_konsep_diri,'skor','klasifikasi','ref_karakter_pribadi');
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "KND");${$var} = "✓";
                    $templateProcessor->setValue('SRKND', $SRKND);
                    $templateProcessor->setValue('RDKND', $RDKND);
                    $templateProcessor->setValue('SDKND', $SDKND);
                    $templateProcessor->setValue('TGKND', $TGKND);
                    $templateProcessor->setValue('STKND', $STKND);

                    $klasifikasi = get_skor_predikat($r->skor_regulasi_emosi,'skor','klasifikasi','ref_karakter_pribadi');
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "RE");${$var} = "✓";
                    $templateProcessor->setValue('SRRE', $SRRE);
                    $templateProcessor->setValue('RDRE', $RDRE);
                    $templateProcessor->setValue('SDRE', $SDRE);
                    $templateProcessor->setValue('TGRE', $TGRE);
                    $templateProcessor->setValue('STRE', $STRE);

                    $klasifikasi = get_skor_predikat($r->skor_keterampilan,'skor','klasifikasi','ref_karakter_pribadi');
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "KS");${$var} = "✓";
                    $templateProcessor->setValue('SRKS', $SRKS);
                    $templateProcessor->setValue('RDKS', $RDKS);
                    $templateProcessor->setValue('SDKS', $SDKS);
                    $templateProcessor->setValue('TGKS', $TGKS);
                    $templateProcessor->setValue('STKS', $STKS);

                    $klasifikasi = get_skor_predikat($r->skor_empati,'skor','klasifikasi','ref_karakter_pribadi');
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "EM");${$var} = "✓";
                    $templateProcessor->setValue('SREM', $SREM);
                    $templateProcessor->setValue('RDEM', $RDEM);
                    $templateProcessor->setValue('SDEM', $SDEM);
                    $templateProcessor->setValue('TGEM', $TGEM);
                    $templateProcessor->setValue('STEM', $STEM);

                    $klasifikasi = get_skor_predikat($r->skor_mandiri,'skor','klasifikasi','ref_karakter_pribadi');
                    $var = ExportWordReport::getCheckValueKlasifikasi($klasifikasi, "KM");${$var} = "✓";
                    $templateProcessor->setValue('SRKM', $SRKM);
                    $templateProcessor->setValue('RDKM', $RDKM);
                    $templateProcessor->setValue('SDKM', $SDKM);
                    $templateProcessor->setValue('TGKM', $TGKM);
                    $templateProcessor->setValue('STKM', $STKM);

                    $templateProcessor->setValue('username', $r->username);
                    $templateProcessor->setValue('namapengguna', $r->nama_pengguna);
                    $templateProcessor->setValue('jeniskelamin', $r->jenis_kelamin=="P" ? 'Perempuan' : 'Laki-Laki');
                    $templateProcessor->saveAs($pathToSave);


                    $no_seri = $quiz_sesi_user->no_seri;
                    DB::table('seri_cetakan_doc')->where('no_seri', $no_seri)->delete();
                  
                    $record_cetakan = array(
                                            "no_seri"=>$no_seri,
                                            "token"=>$token,
                                            "id_user"=>$id_user,
                                            "created_at"=>date('Y-m-d H:i:s'), 
                                            "url"=>url($pathname),
                                            "filename" =>$filename,
                                            "jenis_tes" =>$quiz_sesi->skoring_tabel,
                                            "pathname" =>$pathname,
                                            );

                    DB::table('seri_cetakan_doc')->insert($record_cetakan);                   
                    
            }
            return true;
      }

      static function getCheckValueKlasifikasi($klasifikasi, $suffix){
         $klasifikasi = strtoupper($klasifikasi);
         $klasifikasi = str_replace(" ", "_", $klasifikasi);
         $result = "";
         switch ($klasifikasi) {
             case 'SANGAT_TINGGI':
                 // code...
                 $result = "ST".$suffix;
                 break;
             case 'TINGGI':
                 // code...
                 $result = "TG".$suffix;
                 break;
            case 'SEDANG':
                 // code...
                 $result = "SD".$suffix;
                 break;
            case 'RENDAH':
                 // code...
                 $result = "RD".$suffix;
                 break;
            case 'SANGAT_RENDAH':
                 // code...
                 $result = "SR".$suffix;
                 break;
            default:
                 // code...
                 break;
         }

         return $result;
     }

}
