<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;
use Hash;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ManajemenSesiMappingSMKController extends Controller
{
        
    //tamabahan mapping smk
    function page_mapping_smk($uuid){
        $quiz = DB::table('quiz_sesi')->where('uuid', $uuid)->first();
        return view('manajemen-sesi.addons.mapping-smk', compact('quiz'));
    }

    function datatable_mapping_smk($uuid){
        $quiz = DB::table('quiz_sesi')->select('id_quiz', 'open')->where('uuid', $uuid)->first();

        $filter = "";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=2){
                $keyword = strtolower($keyword);
                $filter = " and (  lower(a.keterangan) like '%$keyword%' ) ";
            }   
        }
         //$id_role_peserta = $this->id_role_peserta;
         $id_quiz = $quiz->id_quiz;
         $open = $quiz->open;
         $sql_union = "select 
                        b.uuid, a.nomor, a.keterangan, a.deskripsi 
                        from 
                        soal_peminatan_smk as a, 
                        quiz_sesi_mapping_smk as b 
                        where a.id_kegiatan = b.id_kegiatan
                        and b.id_quiz = '$id_quiz' $filter ";
        //return $sql_union;
         $query = DB::table(DB::raw("($sql_union) as z order by nomor asc"))
                    ->select([
                        'nomor',
                        'uuid',
                        'keterangan',
                        'deskripsi'
                    ]);

         return Datatables::of($query)
            ->addColumn('action', function ($query) use ($open) {
                    $edit = ""; $delete = "";
                    $action = '';
                    if($this->ucu() && !$open){
                       return '<button data-pilihan="'.$query->keterangan.'" data-uuid="'.$query->uuid.'" class="btn-delete-pilihan-smk btn btn-danger btn-sm" type="button"><i class="las la-trash"></i> Hapus</button>';
                    }else{
                        return '<a  disabled class="btn btn-sm btn-secondary"><i class="la la-lock"></i></a>';
                    }
            })
            ->editColumn('nomor', function($q){
                return "(".$q->nomor. ") ". $q->keterangan."<br><small>".$q->deskripsi."</small>";
            })
            ->addIndexColumn()
            ->rawColumns(['action','nomor'])
            ->make(true);
    }

    function insert_mapping_smk(Request $r){
        if($this->ucu()){
            $uuid = $this->genUUID();
            $quiz = DB::table('quiz_sesi')->select('id_quiz','open')->where('uuid', $r->id_quiz)->first();
            $id_quiz = $quiz->id_quiz;

            if($quiz->open==1){
                $respon = array('status'=>false,'message'=>'Status Sesi Tes Masih Terbuka/Open!');
                return response()->json($respon);
            }

            $record = array(                                              
                "id_quiz"=>(int)($id_quiz),
                "id_kegiatan"=>(int)($r->id_kegiatan),
                "uuid"=>$uuid,
            );

            DB::table('quiz_sesi_mapping_smk')->insert($record);
            $respon = array('status'=>true,'message'=>'Data Pilihan SMK Berhasil Ditambahkan!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }

    function delete_mapping_smk(Request $r){
        if($this->ucu()){
            $uuid = $r->uuid;
            DB::table('quiz_sesi_mapping_smk')->where('uuid', $uuid)->delete();
            $respon = array('status'=>true,'message'=>'Data Pilihan SMK Berhasil Dihapus!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }

}
