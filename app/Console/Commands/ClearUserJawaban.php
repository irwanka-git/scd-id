<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class ClearUserJawaban extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skoring:clear_table_jawaban';

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
     
        KOGNITIF_PMK_INFORMASI_UMUM
        KOGNITIF_PMK_PENALARAN_VERBAL
        KOGNITIF_PMK_PENALARAN_KUANTITATIF      >>> skoring_kognitif
        KOGNITIF_PMK_PENALARAN_ABSTRAK
        KOGNITIF_PMK_PENGERTIAN_MEKANIK
        KOGNITIF_PMK_CEPAT_TELITI
         
        SKALA_PMK_SEKOLAH_KEDINASAN                 >>> skoring_minat_kuliah_dinas
        SKALA_PMK_ILMU_AGAMA                        >>> skoring_minat_kuliah_agama
        SKALA_PMK_MINAT_ILMU_ALAM                   >>> skoring_minat_kuliah_eksakta
        SKALA_PMK_MINAT_ILMU_SOSIAL                 >>> skoring_minat_kuliah_sosial
        SKALA_PMK_SIKAP_PELAJARAN                   >>> skoring_minat_kuliah_sikap_pelajaran
        SKALA_PMK_SUASANA_KERJA                     >>> skoring_minat_kuliah_suasana_kerja

     * @return mixed
     */
    
    public function handle()
    {
        $ada_skoring =  DB::select("select x.*, c.id_user from 
                                    (select a.id_quiz, b.jenis, a.nama_sesi 
                                    from quiz_sesi as a, quiz_sesi_template as b 
                                    where a.id_quiz_template  = b.id_quiz_template and b.jenis ='quiz'
                                    ) as x , quiz_sesi_user as c 
                                    where x.id_quiz = c.id_quiz and c.submit = 1 and c.skoring = 0");
        if(count($ada_skoring)==0){
            DB::table('quiz_sesi_user_jawaban')->truncate();
        }

        $clear_quiz_user= DB::select("SELECT a.id_quiz_user, b.id_quiz 
            FROM quiz_sesi_user as a left join quiz_sesi as b on a.id_quiz = b.id_quiz where b.id_quiz is null");
        foreach ($clear_quiz_user as $r){
            DB::table('quiz_sesi_user')->where('id_quiz_user', $r->id_quiz_user)->delete();
        }
        echo "SELESAI CLEAR USER JAWABAN SESI INVALID...";
    }
 

}
