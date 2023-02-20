<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;
use Auth;
use Image;

class LandingPageController extends Controller
{
    function index(){
    	 $pagetitle = "Beranda";
    	 return view('landing.index', compact('pagetitle'));
    }

    
}
 