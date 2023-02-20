<?php 
function get_enum_values( $table, $field )
{
    // $cek_data = DB::select( "SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'" );
    $cek_data = DB::select( "select enum_values FROM list_enum WHERE table_name = '{$table}' and column_name = '{$field}'" );
    // $type = $cek_data[0]->Type;
    // preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
    $enum = explode(",", $cek_data[0]->enum_values);
    return $enum;
}

function get_list_enum_values($table, $field){
	$enum = get_enum_values($table,$field);
	$list_value = [];
	foreach($enum as $r){
		array_push( $list_value, ['value'=>trim($r), 'text'=>trim($r)]);
	}
	$list_value = json_decode(json_encode($list_value));
	return $list_value;
}

function arr_to_list($arr){
	$list_value = [];
	foreach($arr as $r){
		array_push( $list_value, ['value'=>$r, 'text'=>$r]);
	}
	$list_value = json_decode(json_encode($list_value));
	return $list_value;
}

function toDateDisplay2($date){
	if (strlen($date)!=10){
		$date = substr($date,0,10);
	}
	$date = explode("-", $date);
	return $date[2]."/".$date[1]."/".$date[0];
}


function tgl_indo($data){
	if (strlen($data)<10 ){
		return "";
	}

	if (strlen($data)!=10){
		$data = substr($data,0,10);
	}
	 
	$bulan = array("0"=>"","1"=>'Januari', "2"=>'Februari', "3"=>'Maret', "4"=>"April", "5"=>'Mei', "6"=>'Juni', "7"=>'Juli', "8"=>
		'Agustus', "9"=>'September', "10"=>'Oktober', "11"=>'November',"12"=>'Desember');
	$tgl = explode("-",$data);

	if(count($tgl)==3){
		return $tgl[2]." ".$bulan[(int)$tgl[1]]." ".$tgl[0];
	}else{
		return "";
	}
}

function tgl_indo_singkat($data){
	if (strlen($data)<10 ){
		return "";
	}

	if (strlen($data)!=10){
		$data = substr($data,0,10);
	}
	 
	$bulan = array("0"=>"","1"=>'Jan', "2"=>'Feb', "3"=>'Mar', "4"=>"Apr", "5"=>'Mei', "6"=>'Jun', "7"=>'Jul', "8"=>
		'Agu', "9"=>'Sep', "10"=>'Okt', "11"=>'Nov',"12"=>'Des');
	$tgl = explode("-",$data);

	if(count($tgl)==3){
		return $tgl[2]." ".$bulan[(int)$tgl[1]]." ".$tgl[0];
	}else{
		return "";
	}
}

function tgl_indo_lengkap($data){
	if (strlen($data)<10 ){
		return "";
	}

	if (strlen($data)!=10){
		$data = substr($data,0,10);
	}
	$day = date('D', strtotime($data));
	$dayList = array(
	    'Sun' => 'Minggu',
	    'Mon' => 'Senin',
	    'Tue' => 'Selasa',
	    'Wed' => 'Rabu',
	    'Thu' => 'Kamis',
	    'Fri' => 'Jumat',
	    'Sat' => 'Sabtu'
	);
	$bulan = array("0"=>"","1"=>'Januari', "2"=>'Februari', "3"=>'Maret', "4"=>"April", "5"=>'Mei', "6"=>'Juni', "7"=>'Juli', "8"=>
		'Agustus', "9"=>'September', "10"=>'Oktober', "11"=>'November',"12"=>'Desember');
	$tgl = explode("-",$data);

	if(count($tgl)==3){
		return $dayList[$day].", ".$tgl[2]." ".$bulan[(int)$tgl[1]]." ".$tgl[0];
	}else{
		return "";
	}
}

function get_nama_metode_tes($model){
	$array_ref = array(
					'PG'=>'Pilihan',
					'RT'=>'Rating',
					'TOP'=>'Rangking',
					'PP'=>'Prioritas'
				);
	if (isset($array_ref[$model])){
		return $array_ref[$model];
	}else{
		return "";
	}
}

