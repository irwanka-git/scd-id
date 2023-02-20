<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class SkoringMinatMAN extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skoring:peminatan_man';

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

        SKALA_PEMINATAN_MAN                 >>> skoring_minat_man
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
    private $tabel_skoring_induk = 'skoring_minat_man';
    private $tabel_referensi_pilihan_minat = 'ref_pilihan_minat_man';
    private $tabel_referensi_rekomendasi_pilihan_minat = 'ref_rekomendasi_minat_man';
    private $tabel_referensi_rekomendasi_akhir = 'ref_rekomendasi_akhir_man';
    private $kategori_skala_minat = 'SKALA_PEMINATAN_MAN';
    private $id_quiz_template = '1'; //TEMPLATE QUIZ PEMINATAN SMA =>  1; tabel: quiz_sesi_template

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
                and b.status_hasil = 0
                and a.skoring_tabel = '$tabel_skoring_induk'
                and a.open = 1
                group by
                a.id_quiz, 
                a.nama_sesi,
                a.token
            ");
        $jumlah_running_skoring = 0;
        foreach ($quiz as $qz){
            echo "Skoring Quiz ".$qz->nama_sesi." - ".$qz->token."\n";
            $ada_skoring = $sesi_user = DB::table('quiz_sesi_user')   
                                    ->select('id_quiz','id_user')
                                    ->where('id_quiz', $qz->id_quiz)
                                    ->where('submit', 1)        //sudah submit
                                    ->where('skoring',0)
                                    ->where('status_hasil', 0)  //hasil belum ada
                                    ->count();
            $jumlah_running_skoring +=$ada_skoring;
            if ($ada_skoring > 0){
                $belum_skoring = DB::table('quiz_sesi_user')
                                ->select('id_user','id_quiz')
                                    ->where('id_quiz', $qz->id_quiz)
                                    ->where('submit', 1)        //sudah submit
                                    ->where('skoring',0)
                                    ->where('status_hasil', 0) 
                                    ->get();
                foreach($belum_skoring as $b){
                    DB::table("$tabel_skoring_induk")->where('id_user', $b->id_user)->delete();
                }
                $this->generate_tabel_skor_induk($qz->id_quiz);
                $this->konversi_jawaban($qz->id_quiz);
                $this->skoring_kognitif($qz->id_quiz);
                $this->skoring_minat_man($qz->id_quiz);
                $this->skoring_sikap_pelajaran($qz->id_quiz);
                $this->skoring_minat_tmi($qz->id_quiz);
                $this->skoring_tipologi_jung($qz->id_quiz);
                $this->skoring_karakteristik_pribadi($qz->id_quiz);
                $this->finishing_skoring($qz->id_quiz);
            }
        }

        $finish_at = date("Y-m-d H:i:s");
        if( $jumlah_running_skoring > 0){
            DB::table('running_cronjob')->insert(['nama'=>"skoring:peminatan_man",'start_at'=>$start_at, 'finish_at'=>$finish_at, 'peserta'=>$jumlah_running_skoring]);
            // DB::table('quiz_sesi_user_jawaban')->truncate();
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
        //$id_quiz = $qz->id_quiz;

        //STEP 1 . Pindahkan jawaban user ke tabel quiz_sesi_user_jawaban
        $sesi_user = DB::table('quiz_sesi_user')    //ambil jawaban dari android
                    ->where('id_quiz', $id_quiz)
                    ->where('submit', 1)        //sudah submit
                    ->where('skoring',0)
                    ->where('status_hasil', 0)  //hasil belum ada
                    ->get();

        // TABEL: quiz_sesi_user_jawaban
        // id_jawaban_peserta  int
        // id_quiz int
        // id_user int
        // kategori    varchar
        // urutan  int
        // jawaban char
        // skor    int
        echo  $this->space1."Mulai Konversi Jawaban User \n";
        foreach($sesi_user as $su){
            $id_user = $su->id_user;
            $jawaban = json_decode($su->jawaban);
            

            //kosongkan dulu jawaban di tabel quiz_sesi_user_jawaban
            // DB::table('quiz_sesi_user_jawaban')
            //         ->where('id_user', $id_user)
            //         ->where('id_quiz', $id_quiz)
            //         ->delete();
            $jawaban_submit = json_decode($su->jawaban);

            if($jawaban_submit){
                //echo $this->space1."Mulai Konversi Jawaban User ".$su->id_user."\n";
                foreach($jawaban_submit as $js){
                    $kategori = $js->kategori;
                    $jawaban = json_decode($js->jawaban);
                     
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
                                from skoring_minat_man 
                                where id_quiz = $id_quiz ");
        
        $ref_konversi_iq = DB::table('ref_konversi_iq')->get();
        $arr_iq = [];
        foreach ($ref_konversi_iq as $rq){
            $arr_iq[$rq->skor_x] = $rq->tot_iq;
        }
        foreach ($skor_iq as $si){
            $record = [];
            $record['tpa_iq'] = $si->skor_tpa_iq;
            $record['skor_iq'] = $arr_iq[$si->skor_tpa_iq];
            DB::table($tabel_skoring_induk)
                ->where('id', $si->id)
                ->update($record);
        }

        echo  $this->space1."Berhasil Skoring Kognitif \n";

    }

    //pake rekom
    public function skoring_minat_man($id_quiz){

        $tabel_skoring_induk = $this->tabel_skoring_induk;
        $kategori_skala_minat = $this->kategori_skala_minat; //'SKALA_PEMINATAN_MAN';
        $tabel_referensi_pilihan_minat = $this->tabel_referensi_pilihan_minat;
        $tabel_referensi_rekomendasi_pilihan_minat = $this->tabel_referensi_rekomendasi_pilihan_minat;
        echo  $this->space1."Mulai Skoring ".$kategori_skala_minat." \n";

        $skor_kelompok = DB::select("select 
                                        b.id_quiz, 
                                        b.id_user, 
                                        a.field_skoring,
                                        count(b.jawaban) as total
                                        from 
                                        $tabel_referensi_pilihan_minat as a 
                                        left join quiz_sesi_user_jawaban as b 
                                        on a.kd_pilihan = b.jawaban
                                        and b.id_quiz = $id_quiz and b.kategori = '$kategori_skala_minat'
                                    GROUP BY 
                                        b.id_quiz, 
                                        b.id_user, 
                                        a.field_skoring");
        foreach ($skor_kelompok as $sk){
            $record = [];
            $record[$sk->field_skoring] = $sk->total;
            DB::table($tabel_skoring_induk)
                ->where('id_quiz', $sk->id_quiz)
                ->where('id_user', $sk->id_user)
                ->update($record);
        }

        $skor_rekomendasi = DB::select("
                                        SELECT
                                            x.*,
                                            y.rekomendasi
                                            FROM
                                            (
                                                SELECT
                                                id_user,
                                                id_quiz,
                                                minat_sains,
                                                minat_bahasa,
                                                minat_humaniora,
                                                minat_agama,
                                                COALESCE ( minat_sains,0)  
                                                - COALESCE(minat_bahasa,0) 
                                                - COALESCE(minat_humaniora, 0) 
                                                - COALESCE(minat_agama, 0) 
                                                AS minat_rentang
                                                FROM
                                                $tabel_skoring_induk AS a
                                                WHERE
                                                a.id_quiz = $id_quiz
                                                and a.selesai_skoring = 0
                                            ) AS x
                                        LEFT JOIN $tabel_referensi_rekomendasi_pilihan_minat AS y ON x.minat_rentang = y.perbedaan ");

        foreach ($skor_rekomendasi as $sr){
            $record = [];
            $record['minat_rentang'] = $sr->minat_rentang;
            $record['rekom_minat'] = $sr->rekomendasi;
            DB::table($tabel_skoring_induk)
                ->where('id_quiz', $sr->id_quiz)
                ->where('id_user', $sr->id_user)
                ->update($record);
        }

        echo  $this->space1."Berhasil Skoring ".$kategori_skala_minat." \n";

    }

    //pake rekom
    public function skoring_sikap_pelajaran($id_quiz){

        $tabel_skoring_induk = $this->tabel_skoring_induk;
        $kategori = 'SIKAP_TERHADAP_PELAJARAN';
        echo  $this->space1."Mulai Skoring ".$kategori." \n";

        $update_skor = DB::select("UPDATE  quiz_sesi_user_jawaban 
                                        set skor = SUBSTR(jawaban,1,1)::INTEGER 
                                        + SUBSTR(jawaban,2,1)::INTEGER 
                                        + SUBSTR(jawaban,3,1)::INTEGER
                                    where id_quiz = $id_quiz 
                                        and kategori = '$kategori'");

        $skor_kelompok = DB::select("SELECT 
                                        a.id_quiz, 
                                        a.id_user, 
                                        a.skor as total , 
                                        b.field_skoring
                                    FROM quiz_sesi_user_jawaban as a, soal_sikap_pelajaran as b  
                                    where 
                                        a.urutan = b.urutan
                                        and a.id_quiz = $id_quiz
                                        and a.kategori = '$kategori' ");

        foreach ($skor_kelompok as $sk){
            $record = [];
            $record[$sk->field_skoring] = $sk->total;
            DB::table($tabel_skoring_induk)
                ->where('id_quiz', $sk->id_quiz)
                ->where('id_user', $sk->id_user)
                ->update($record);
        }

        $skor_kelompok_ilmu = DB::select("SELECT 
                                            a.id_quiz, 
                                            a.id_user, 
                                            c.field_skoring,
                                            sum(a.skor) as total
                                            FROM quiz_sesi_user_jawaban as a, 
                                            soal_sikap_pelajaran as b  , 
                                            ref_kelompok_sikap_pelajaran as c 
                                        where 
                                            a.urutan = b.urutan and a.id_quiz = $id_quiz
                                            and c.kelompok::VARCHAR = b.kelompok::VARCHAR
                                            and a.kategori = '$kategori'
                                        group by 
                                            id_quiz, id_user, c.field_skoring");

        foreach ($skor_kelompok_ilmu as $ski){
            $record = [];
            $record[$ski->field_skoring] = $ski->total;
            DB::table($tabel_skoring_induk)
                ->where('id_quiz', $ski->id_quiz)
                ->where('id_user', $ski->id_user)
                ->update($record);
        }


        $skor_rekomendasi = DB::select(" select 
                                            a.id_user, a.id_quiz,
                                            a.sikap_ilmu_alam, 
                                            a.sikap_ilmu_sosial, 
                                            a.sikap_ilmu_alam -  a.sikap_ilmu_sosial as sikap_rentang,
                                            b.rekomendasi 
                                        from $tabel_skoring_induk as a, ref_rekomendasi_sikap_pelajaran as b  
                                        where 
                                            a.id_quiz = $id_quiz 
                                            and b.perbedaan = (a.sikap_ilmu_alam -  a.sikap_ilmu_sosial) 
                                            and a.selesai_skoring = 0
                                            ");

        foreach ($skor_rekomendasi as $sr){
            $record = [];
            $record['sikap_rentang'] = $sr->sikap_rentang;
            $record['rekom_sikap_pelajaran'] = $sr->rekomendasi;
            DB::table($tabel_skoring_induk)
                ->where('id_quiz', $sr->id_quiz)
                ->where('id_user', $sr->id_user)
                ->update($record);
        }

        echo  $this->space1."Berhasil Skoring ".$kategori." \n";

    }

    //pake rekom
    public function skoring_minat_tmi($id_quiz){

        $tabel_skoring_induk = $this->tabel_skoring_induk;
        $kategori = 'SKALA_TES_MINAT_INDONESIA';
        $skor_maksimal = 7;


        echo  $this->space1."Mulai Skoring ".$kategori." \n";

        $update_skor = DB::select("update quiz_sesi_user_jawaban 
                                        set skor = ($skor_maksimal + 1) - urutan
                                    where 
                                        id_quiz = $id_quiz and kategori = '$kategori';");

        $skor_kelompok = DB::select("   select
                                            a.id_quiz, 
                                            a.id_user,  
                                            c.field_skoring,
                                            sum(a.skor) as total 
                                        from quiz_sesi_user_jawaban as a, soal_tmi as b, ref_kelompok_tmi as c 
                                        where  
                                            a.id_quiz = $id_quiz 
                                            and a.kategori = '$kategori'
                                            and a.jawaban = lpad(b.urutan::TEXT ,2,'0'::TEXT)
                                            and c.kelompok::VARCHAR = b.kelompok::VARCHAR  
                                        GROUP BY 
                                            a.id_quiz, 
                                            a.id_user,  
                                            c.field_skoring");
        foreach ($skor_kelompok as $sk){
            $record = [];
            $record[$sk->field_skoring] = $sk->total;
            DB::table($tabel_skoring_induk)
                ->where('id_quiz', $sk->id_quiz)
                ->where('id_user', $sk->id_user)
                ->update($record);
        }

        $skor_rekomendasi = DB::select(" select 
                                                a.id_user, a.id_quiz,
                                                a.tmi_ilmu_alam -  a.tmi_ilmu_sosial as tmi_rentang,
                                                b.rekomendasi 
                                            from $tabel_skoring_induk as a,
                                                ref_rekomendasi_minat_tmi as b  
                                            where a.id_quiz = $id_quiz
                                                and b.perbedaan = (a.tmi_ilmu_alam -  a.tmi_ilmu_sosial)
                                                and a.selesai_skoring = 0 
                                             ");

        foreach ($skor_rekomendasi as $sr){
            $record = [];
            $record['tmi_rentang'] = $sr->tmi_rentang;
            $record['rekom_tmi'] = $sr->rekomendasi;
            DB::table($tabel_skoring_induk)
                ->where('id_quiz', $sr->id_quiz)
                ->where('id_user', $sr->id_user)
                ->update($record);
        }

        echo  $this->space1."Berhasil Skoring ".$kategori." \n";

    }

    public function skoring_tipologi_jung($id_quiz){
        $tabel_skoring_induk = $this->tabel_skoring_induk;
        $kategori = 'SKALA_TES_TIPOLOGI_JUNG';

        echo  $this->space1."Mulai Skoring ".$kategori." \n";
        $skor_kelompok = DB::select("select x.id_user, 
                                            x.id_quiz, 
                                            y.kolom, 
                                            z.kode, 
                                            x.skor_a as total_a, 
                                            x.skor_b as total_b, 
                                            z.field_skoring_a, 
                                            z.field_skoring_b
                                            from (SELECT
                                                    a.kolom,
                                                    b.id_quiz, 
                                                    b.id_user,
                                                    sum(case when b.jawaban = 'A' then 1 else 0 end) as skor_a,
                                                    sum(case when b.jawaban = 'B' then 1 else 0 end) as skor_b
                                                FROM
                                                    soal_tipologi_jung AS a,
                                                    quiz_sesi_user_jawaban AS b 
                                                WHERE
                                                    a.urutan = b.urutan 
                                                    AND b.kategori = '$kategori' 
                                                    AND b.id_quiz = $id_quiz  
                                                GROUP BY a.kolom, b.id_quiz, b.id_user) as x, 
                                                ref_skoring_tipologi_jung as y,
                                                ref_klasifikasi_tipologi_jung as z 
                                            where 
                                                x.kolom::VARCHAR = y.kolom::VARCHAR
                                                and x.skor_a = y.skor_a
                                                and y.kode_klasifikasi = z.kode
                                            order by 
                                                x.id_user, y.kolom");

         $tipojung_user = [];
         $tipojung_kode = [];
         $id_user = 0;
         foreach ($skor_kelompok as $sk){
            $record = [];
            $record[$sk->field_skoring_a] = $sk->total_a;
            $record[$sk->field_skoring_b] = $sk->total_b;
            DB::table($tabel_skoring_induk)
                ->where('id_quiz', $sk->id_quiz)
                ->where('id_user', $sk->id_user)
                ->update($record);
            if($sk->id_user!=$id_user){
                $id_user = $sk->id_user;
                array_push($tipojung_user, $id_user);
                $tipojung_kode[$id_user] = $sk->kode;
            }else{
                $tipojung_kode[$id_user] .= $sk->kode;
            }
        }

        for($i = 0;$i<count($tipojung_user);$i++){
            $id_user = $tipojung_user[$i];
            $kode    = $tipojung_kode[$id_user];

            $record = [];
            $record['tipojung_kode'] = $kode;
            DB::table($tabel_skoring_induk)
                ->where('id_quiz', $id_quiz)
                ->where('id_user', $id_user)
                ->update($record);
        }

        echo  $this->space1."Berhasil Skoring ".$kategori." \n";
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

    //rekomendasi akhir
    public function finishing_skoring($id_quiz) {
        $kategori = "AKHIR";
        $tabel_skoring_induk = $this->tabel_skoring_induk;
        $tabel_referensi_rekomendasi_akhir = $this->tabel_referensi_rekomendasi_akhir;

        echo  $this->space1."Mulai Skoring ".$kategori." \n";

        $skor_rekomendasi = DB::select(" 
                                            select a.id_quiz, 
                                                a.id_user, 
                                                a.rekom_minat, 
                                                a.rekom_sikap_pelajaran,
                                                a.rekom_tmi,
                                                b.rekom_akhir 
                                            from $tabel_skoring_induk as a 
                                            left join $tabel_referensi_rekomendasi_akhir as b
                                                 on a.rekom_minat = b.rekom_minat 
                                                 and a.rekom_tmi = b.rekom_tmi 
                                                 and a.rekom_sikap_pelajaran = b.rekom_sikap_pelajaran 
                                            where 
                                                a.id_quiz = $id_quiz and a.selesai_skoring = 0
                                             ");

        foreach ($skor_rekomendasi as $sr){
            $record = [];
            $record['selesai_skoring'] = 1;
            $record['rekom_akhir'] = $sr->rekom_akhir;
            
            DB::table($tabel_skoring_induk)
                ->where('id_quiz', $sr->id_quiz)
                ->where('id_user', $sr->id_user)
                ->update($record);

            //update status skoring dan hasil
            $record2 = [];   
            //ambil saran dari template
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

            //hapus tabel jawaban
            // DB::table('quiz_sesi_user_jawaban')
            //     ->where('id_quiz', $sr->id_quiz)
            //     ->where('id_user', $sr->id_user)
            //     ->delete();
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
                $obj = json_decode(json_encode(['urutan'=>$r->urutan,'jawaban'=>$r->jawaban,'skor'=>$r->skor]));
                array_push($temp,$obj);
            }else{
                $obj = json_decode(json_encode(['urutan'=>$r->urutan, 'jawaban'=>$r->jawaban,'skor'=>$r->skor]));
                array_push($temp,$obj);
            }

            $result[$kategori] = $temp;
        }

        $result = json_encode($result);
        return $result;
    }




    //public function konversi_jawaban()


}
