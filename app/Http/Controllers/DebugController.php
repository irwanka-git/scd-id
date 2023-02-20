<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;
use Auth;
use Image;

class DebugController extends Controller
{
    function convert_image_base_64($file){
    	 loadHelper('function');
    	 if(file_exists(public_path().'/gambar/'.$file)){
    	 	$image_src = convert_image_base64(public_path().'/gambar/'.$file);
    	 	return "<img src='".$image_src. "'>";

    	 }else{
    	 	return null;
    	 }
    }
    
}