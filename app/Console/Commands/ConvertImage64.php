<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class ConvertImage64 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert:image';

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
     *
     * @return mixed
     */
    public function handle()
    {
        //
        loadHelper('function');
        $gambar1 = DB::table('soal_kognitif')->where('pertanyaan_gambar','<>','')->get();
        foreach($gambar1 as $g){
            $file = $g->pertanyaan_gambar;
            $path = public_path().'/gambar/'.$file;
            $data = file_get_contents($path);
            $image_base64 = base64_encode($data);
            $type = pathinfo($path, PATHINFO_EXTENSION);

            DB::table('gambar')->where('filename', $g->pertanyaan_gambar)->delete();
            DB::table('gambar')->insert(['filename'=>$g->pertanyaan_gambar, 
                    'type'=>$type, 
                    'image_base64'=>$image_base64]);
        }

    }

    function convert_image_base_64($file){
         //loadHelper('function');
         if(file_exists(public_path().'/gambar/'.$file)){

            $path = public_path().'/gambar/'.$file;
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            //$image_src = convert_image_base64(public_path().'/gambar/'.$file);
            return "<img width='100%' src='".$base64. "'>";
         }else{
            return null;
         }
    }
}
