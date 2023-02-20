<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Helpers\QuizConverter;
use App\Helpers\ExportWordReport;

class TestingGETDATAAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testing:get_data_api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     
        KOGNITIF_INFORMASI_UMUM
        KOGNITIF_PENALARAN_VERBAL
        KOGNITIF_PENALARAN_KUANTITATIF      >>> SKORING_KOGNITF
        KOGNITIF_PENALARAN_ABSTRAK
        KOGNITIF_PENALARAN_SPASIAL
        KOGNITIF_PENGERTIAN_MEKANIK
        KOGNITIF_CEPAT_TELITI

        SKALA_PEMINATAN_smk                 >>> skoring_minat_smk
        SIKAP_TERHADAP_PELAJARAN            >>> SKORING_SIKAP_PELAJARAN
        SKALA_TES_MINAT_INDONESIA           >>> SKORING_MINAT_TMI
        SKALA_TES_TIPOLOGI_JUNG             >>> SKORING_TIPOLOGI_JUNG
        SKALA_TES_KARAKTERISTIK_PRIBADI     >>> SKORING_KARAKTERISITIK_PRIBADI

     * @return mixed
     */
    private $space1 = "---> ";
    private $space2 = "--------> ";
    private $space3 = "-------------> ";
    private $paket_soal = 'NON';
    private $tabel_skoring_induk = 'skoring_minat_smk';
    //private $tabel_referensi_pilihan_minat = 'ref_pilihan_minat_smk';
    //private $tabel_referensi_rekomendasi_pilihan_minat = 'ref_rekomendasi_minat_smk';
    //private $tabel_referensi_rekomendasi_akhir = 'ref_rekomendasi_akhir_smk';
    private $kategori_skala_minat = 'SKALA_PEMINATAN_SMK';
    

    public function handle()
    {
        $token = "92717";
        $path = "/get-info-session/".$token;
        $url = env('BASE_URL_API').$path;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
           "Accept: application/json",
           "Authorization: Bearer eyJpdiI6IjlDSzVRY2FkTTNmOFdjVU1jR1IwTFE9PSIsInZhbHVlIjoiSldiaUFDV3J3WGtTbldBWTZubXo3dz09IiwibWFjIjoiMjRmNjU5YTRjOGI3MTUzMGFjMDZlNDY0ZmE5OTI0MDg0ODkwN2Y1MjVlZGFhYTkwZDY0NmE3Yjg5NjlmY2YzNSJ9",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        $respon_object = json_decode($resp);
        return $respon_object->data;
    }
 
}
