<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class SkoringTestIQdanEQ extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skoring:test_iq_dan_eq';

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
        
        SKALA_TES_KARAKTERISTIK_PRIBADI     >>> SKORING_KARAKTERISITIK_PRIBADI

     * @return mixed
     */
    private $space1 = "---> ";
    private $space2 = "--------> ";
    private $space3 = "-------------> ";
    private $paket_soal = 'NON';
    private $tabel_skoring_induk = 'skoring_iq_eq';   

    public function handle()
    {
        $start_at = date("Y-m-d H:i:s");
        $tabel_skoring_induk =$this->tabel_skoring_induk;
        //$quiz = DB::table('quiz_sesi')->where('skoring_tabel', )->get();
        $quiz = DB::select("
                select 
                a.id_quiz, 
                a.nama_sesi, 
                a.token,
                count(b.id_quiz_user) as peserta_submit 
                from 
                quiz_sesi as a, 
                quiz_sesi_user as b
                where 
                a.id_quiz = b.id_quiz
                and b.submit = 1
                and b.skoring = 0
                and a.skoring_tabel = '$tabel_skoring_induk'
                group by
                a.id_quiz, 
                a.nama_sesi,
                a.token
            ");
         
        //var_dump($quiz);

        $jumlah_running_skoring = 0;
        foreach ($quiz as $qz){
            
            $ada_skoring =  DB::table('quiz_sesi_user')   
                                    ->select('id_quiz','id_user')
                                    ->where('id_quiz', $qz->id_quiz)
                                    ->where('submit', 1)        //sudah submit
                                    ->where('skoring',0)
                                    ->where('status_hasil', 0)  //hasil belum ada
                                    ->count();
            $jumlah_running_skoring +=$ada_skoring;
            echo "Skoring Quiz ".$qz->nama_sesi." - ".$qz->token."\t".$ada_skoring." User \n";
            if($ada_skoring > 0){
                $belum_skoring = DB::table('quiz_sesi_user')
                                ->select('id_user','id_quiz')
                                    ->where('id_quiz', $qz->id_quiz)
                                    ->where('submit', 1)        //sudah submit
                                    ->where('skoring',0)
                                    ->where('status_hasil', 0) 
                                    ->get();
                foreach($belum_skoring as $b){
                    DB::table("$tabel_skoring_induk")->where('id_user', $b->id_user)->delete();
                    DB::table("quiz_sesi_user_jawaban")
                                        ->where('id_user', $b->id_user)
                                        ->where('id_quiz', $b->id_quiz)
                                        ->delete();
                }

                $this->generate_tabel_skor_induk($qz->id_quiz);
                $this->konversi_jawaban($qz->id_quiz);
                $this->skoring_kognitif($qz->id_quiz); //skoring IQ
                $this->skoring_eq_kepribadian($qz->id_quiz); //skoring EQ ASPEK SIKAP KERJA dan ASPEK KEPRIBADIAN
                $this->finishing_skoring($qz->id_quiz);
            }
        }

        $finish_at = date("Y-m-d H:i:s");
        if( $jumlah_running_skoring > 0){
            DB::table('running_cronjob')->insert(['nama'=>"skoring:test_iq_dan_eq",'start_at'=>$start_at,
                 'finish_at'=>$finish_at, 'peserta'=>$jumlah_running_skoring]);
            //truncate table sementara;
            //DB::table('quiz_sesi_user_jawaban')->truncate();
        }
    }

    public function generate_tabel_skor_induk($id_quiz){
        $tabel_skoring_induk = $this->tabel_skoring_induk;
        $jumlah_user = 0;
        $sesi_user = DB::table('quiz_sesi_user')   
                    ->select('id_quiz','id_user')
                    ->where('id_quiz', $id_quiz)
                    ->where('submit', 1)        //sudah submit
                    ->where('skoring',0)
                    ->where('status_hasil', 0)  //hasil belum ada
                    ->get();
        foreach($sesi_user as $su){
            $exist_skor_induk = DB::table($tabel_skoring_induk)
                                ->where('id_quiz', $id_quiz)
                                ->where('id_user', $su->id_user)
                                ->count();
            if($exist_skor_induk==0){
                $jumlah_user++;
                $data_awal = array(
                    'id_user'=>$su->id_user, 
                    'id_quiz'=>$su->id_quiz,
                );
                DB::table($tabel_skoring_induk)->insert($data_awal);
            }
        }

        echo  $this->space1."Generate Data Awal Skor ". $tabel_skoring_induk ." - ". $jumlah_user." User \n";
    }
 

    public function konversi_jawaban($id_quiz){
        loadHelper('skoring');
        //STEP 1 . Pindahkan jawaban user ke tabel quiz_sesi_user_jawaban
        $sesi_user = DB::table('quiz_sesi_user')    //ambil jawaban dari android
                    ->where('id_quiz', $id_quiz)
                    ->where('submit', 1)        //sudah submit
                    ->where('skoring',0)
                    ->where('status_hasil', 0)  //hasil belum ada
                    ->get();
         
        echo  $this->space1."Mulai Konversi Jawaban User \n";
        foreach($sesi_user as $su){
            $id_user = $su->id_user;
            $jawaban_submit = json_decode(valid_json_string($su->jawaban));

            if($jawaban_submit){
                //var_dump($jawaban_submit);
                //echo $this->space1."Mulai Konversi Jawaban User ".$su->id_user."\n";
                foreach($jawaban_submit as $js){
                    $kategori = $js->kategori;
                    $jawaban = ($js->jawaban);
                     
                    for($urutan=1;$urutan<count($jawaban);$urutan++){
                        $record_jawaban = array(
                            'id_user'=>$id_user,
                            'id_quiz'=>$id_quiz,
                            'kategori'=>$kategori,
                            'urutan'=>$urutan,
                            'jawaban'=>$jawaban[$urutan],
                            'skor'=>0
                        );
                        DB::table('quiz_sesi_user_jawaban')->insert($record_jawaban);
                    }
                    //echo $this->space2."Berhasil Konversi Jawaban: ".$kategori."\n";
                }
                //echo  $this->space1."Berhasil Konversi Jawaban User ".$su->id_user."\n";
                //echo  $this->space1."=======================================================\n";
            }
        }
        echo  $this->space1."Berhasil Konversi Jawaban User \n";
    }

    public function skoring_kognitif($id_quiz){

        $tabel_skoring_induk = $this->tabel_skoring_induk;
        $paket_soal = $this->paket_soal;
        //Step 1. koreksi jawaban kognitif
        $update_skor = DB::select("update quiz_sesi_user_jawaban
                                        set skor = get_skor_jawaban_kognitif(urutan, jawaban, REPLACE(kategori,'KOGNITIF_',''), '$paket_soal' )
                                    where  
                                        SUBSTR(kategori,1,9)='KOGNITIF_' 
                                        and id_quiz = $id_quiz");

        

        //STEP 2 Grouping dan mapping ke tabel skor_minat_sma
        $skor_kelompok = DB::select("SELECT
                                        a.id_quiz,
                                        a.id_user,
                                        b.nama_bidang,
                                        b.field_skoring,
                                        sum( a.skor ) AS total 
                                    FROM
                                        quiz_sesi_user_jawaban AS a,
                                        ref_bidang_kognitif AS b 
                                    WHERE
                                        a.id_quiz = $id_quiz 
                                        AND SUBSTR( a.kategori, 1, 9 )= 'KOGNITIF_' 
                                        AND REPLACE ( a.kategori, 'KOGNITIF_', '' ) = b.nama_bidang 
                                    GROUP BY
                                        a.id_quiz,
                                        a.id_user,
                                        b.nama_bidang,
                                        b.field_skoring");

        foreach ($skor_kelompok as $sk){
            $record = [];
            $record[$sk->field_skoring] = $sk->total;
            DB::table($tabel_skoring_induk)
                ->where('id_quiz', $sk->id_quiz)
                ->where('id_user', $sk->id_user)
                ->update($record);
        }

        $skor_iq = DB::select("select 
                                id, 
                                tpa_iu + tpa_pv + 
                                tpa_pk + tpa_pa +
                                tpa_ps + tpa_pm + 
                                tpa_kt as skor_tpa_iq  
                                from $tabel_skoring_induk 
                                where id_quiz = $id_quiz and selesai_skoring = 0");

        $ref_konversi_iq = DB::table('ref_konversi_iq')->get();
        $arr_iq = [];
        foreach ($ref_konversi_iq as $rq){
            $arr_iq[$rq->skor_x] = $rq->tot_iq;
        }
        foreach ($skor_iq as $si){
            $record = [];
            //echo $si->skor_tpa_iq. "\n";
            $record['tpa_iq'] = $si->skor_tpa_iq;
            $record['skor_iq'] = $arr_iq[$si->skor_tpa_iq];
            DB::table($tabel_skoring_induk)
                ->where('id', $si->id)
                ->update($record);
        }
        
        echo  $this->space1."Berhasil Skoring Kognitif \n";

    }
    

    public function skoring_eq_kepribadian($id_quiz){
        
        $tabel_skoring_induk = $this->tabel_skoring_induk;
        $kategori = 'SKALA_TES_KARAKTERISTIK_PRIBADI';

        echo  $this->space1."Mulai Skoring ".$kategori." \n";

        $update_skor = DB::select("update quiz_sesi_user_jawaban
                                        set skor = get_skor_jawaban_karakteristik_pribadi(jawaban)
                                    where 
                                        id_quiz = $id_quiz and kategori='$kategori' ");
        
        $skor_komponen = DB::select("select * from (SELECT 
                                        s.id_user,
                                        b.field_skoring,
                                        SUM ( s.skor ) AS skor_total 
                                    FROM
                                        quiz_sesi_user_jawaban AS s,
                                        soal_karakteristik_pribadi AS a,
                                        ref_komponen_eq AS b 
                                    WHERE
                                        a.urutan IN ( SELECT UNNEST ( string_to_array( b.nomor_soal, ',' ) :: INT [] ) ) 
                                        AND s.id_quiz = $id_quiz 
                                        AND s.urutan = a.urutan 
                                        AND s.kategori = '$kategori' 
                                    GROUP BY
                                        s.id_user,
                                        b.field_skoring) as x order by id_user");

        foreach ($skor_komponen as $sk){
            $record = [];
            $record[$sk->field_skoring] = $sk->skor_total;
            DB::table($tabel_skoring_induk)
                ->where('id_quiz', $id_quiz)
                ->where('id_user', $sk->id_user)
                ->update($record);
        }

        echo  $this->space1."Selesai Skoring ".$kategori." \n";
        
    }

     
    public function skoring_karakteristik_pribadi($id_quiz){

        $tabel_skoring_induk = $this->tabel_skoring_induk;
        $kategori = 'SKALA_TES_KARAKTERISTIK_PRIBADI';

        echo  $this->space1."Mulai Skoring ".$kategori." \n";

        $update_skor = DB::select("update quiz_sesi_user_jawaban
                                        set skor = get_skor_jawaban_karakteristik_pribadi(jawaban)
                                    where 
                                        id_quiz = $id_quiz and kategori='$kategori' ");
        
        $skor_kelompok = DB::select("SELECT 
                                        a.id_quiz, a.id_user, 
                                        c.field_skoring, sum(a.skor) as total
                                    FROM quiz_sesi_user_jawaban as a  , soal_karakteristik_pribadi as b , ref_komponen_karakteristik_pribadi as c 
                                    where 
                                        a.urutan = b.urutan 
                                        and c.id_komponen = b.id_komponen
                                        and  a.id_quiz = $id_quiz
                                        and a.kategori = '$kategori'
                                    group by 
                                        a.id_user, a.id_quiz, c.field_skoring");

        foreach ($skor_kelompok as $sk){
            $record = [];
            $record[$sk->field_skoring] = $sk->total;
            DB::table($tabel_skoring_induk)
                ->where('id_quiz', $sk->id_quiz)
                ->where('id_user', $sk->id_user)
                ->update($record);
        }

        echo  $this->space1."Mulai Skoring ".$kategori." \n";
    }

   
    public function finishing_skoring($id_quiz) {
        $kategori = "AKHIR";
        $tabel_skoring_induk = $this->tabel_skoring_induk;
        
        echo  $this->space1."Mulai Skoring ".$kategori." \n";

        $sesi_user = DB::table($tabel_skoring_induk)   
                        ->select('id_quiz','id_user')
                        ->where('id_quiz', $id_quiz)
                        ->where('selesai_skoring',0)
                        ->get();

        foreach ($sesi_user as $sr){

            //update status skoring dan hasil
            $record = [];
            $record['selesai_skoring'] = 1;
            DB::table($tabel_skoring_induk)
                ->where('id_quiz', $sr->id_quiz)
                ->where('id_user', $sr->id_user)
                ->update($record);

            //ambil saran dari template
            $record2 = [];
            $saran = DB::table('quiz_template_saran')
                        ->where('skoring_tabel', $tabel_skoring_induk) 
                        ->first();
            if($saran){
                $saran = $saran->isi;
            }else{
                $saran = "";
            }
            //$record2['status_hasil'] = 1;
            $record2['skoring_at'] = date('Y-m-d H:i:s');
            $record2['skoring'] = 1;
            $record2['saran'] = $saran;
            $record2['jawaban_skoring'] = $this->konversi_jawaban_skoring($sr->id_quiz, $sr->id_user);

            DB::table('quiz_sesi_user')
                ->where('id_quiz', $sr->id_quiz)
                ->where('id_user', $sr->id_user)
                ->update($record2);           
        }
        
        echo  $this->space1."Selesai Skoring ".$kategori." \n";
    }

    //konversi jawaban dari tabel quiz_user_sesi_jawaban ke format JSON
    public function konversi_jawaban_skoring($id_quiz, $id_user){

        $data = DB::select("select kategori, urutan, jawaban, skor from quiz_sesi_user_jawaban
                        where id_quiz = $id_quiz and id_user = $id_user
                        order by kategori, urutan asc");
        $result = array();
        $kategori = "";
        $temp = [];
        foreach($data as $r){
            if($kategori != $r->kategori){
                $temp = [];
                $kategori = $r->kategori;
                $obj = json_decode(json_encode(['urutan'=>$r->urutan,'jawaban'=>trim($r->jawaban),'skor'=>$r->skor]));
                array_push($temp,$obj);
            }else{
                $obj = json_decode(json_encode(['urutan'=>$r->urutan, 'jawaban'=>trim($r->jawaban),'skor'=>$r->skor]));
                array_push($temp,$obj);
            }

            $result[$kategori] = $temp;
        }

        $result = json_encode($result);
        // DB::table('quiz_sesi_user_jawaban')
        //         ->where('id_quiz', $id_quiz)
        //         ->where('id_user', $id_user)
        //         ->delete();
        return $result;
    }
    
}
