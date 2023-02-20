<?php 
function get_skor_predikat( $skor, $field_check,$field_result, $table )
{
    $result = '';
    $cek = DB::table($table)->select($field_result)->where($field_check, $skor)->first();
    if($cek){
        //return $cek;
        $result = $cek->$field_result;
        return $result;
    }
    return "";
}

function skoring_replace_($string){
	return str_replace("_", " ", $string);
}

function valid_json_string($string){
    //$data_jawaban = "'".trim(($string))."'";
    $data = str_replace('\\',"",$string);
    $data = str_replace('"[',"[",$data);
    $data = str_replace(']"',"]",$data);
    return $data;
}