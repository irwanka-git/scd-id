<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Storage;
use Illuminate\Support\Facades\Storage as StorageLaravel;

class QuizConverter
{
    //
    static function genUUID(){
        list($usec, $sec) = explode(" ", microtime());
        $time = ((float)$usec + (float)$sec);
        $time = str_replace(".", "-", $time);
        $panjang = strlen($time);
        $sisa = substr($time, -1*($panjang-5));
        return sha1(rand(10,99).rand(0,9).substr($time, 0,5).rand(0,9).rand(0,9).$sisa);
    }

    static function get_data_from_api($path){
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

    static function convert_quiz_json($token){

        $list_session = QuizConverter::get_data_from_api("/get-info-session/".$token);
        $soal = array();
        foreach($list_session as $r){
            $path_soal = $r->soal;
            $data_soal = QuizConverter::get_data_from_api($path_soal);
            foreach($data_soal as $d){
                array_push($soal, $d);
            }
        }

        //file normal
        $uuid = QuizConverter::genUUID();
        $filename = date("Ymd").$uuid.".json";
        $data = ['session'=>$list_session, 'soal'=>$soal];
                
        StorageLaravel::put($filename, json_encode($data));

        $credentials = app_path('firebase-key.json');
        $factory = (new Factory)->withServiceAccount($credentials);
        $storage = $factory->createStorage();
        $defaultBucket = $storage->getBucket();
        $file_content = StorageLaravel::get($filename);
        $uploadResult = $defaultBucket->upload($file_content,['name'=>$filename, 'predefinedAcl' => 'publicRead']);

        $url_plaintext = "https://storage.googleapis.com/sicerdas-indonesia.appspot.com/".$filename;
        $result = ['url_plaintext'=>$url_plaintext];
        return json_decode(json_encode($result));
    }

    static function encryptContent($value){
        $key = '1245714587458888'; //combination of 16 character
        $iv = 'e16ce888a20dadb8'; //combination of 16 character
        $method = 'aes-128-cbc';
        $encryptedString = openssl_encrypt($value, $method,
            $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($encryptedString);
    }

    static function decryptContent($value){
        $key = '1245714587458888'; //combination of 16 character
        $iv = 'e16ce888a20dadb8'; //combination of 16 character
        $method = 'aes-128-cbc';
        $base64 = base64_decode($value);
        $decryptedString = openssl_decrypt($base64, $method,
            $key, OPENSSL_RAW_DATA, $iv);
        return $decryptedString;
    }

}
