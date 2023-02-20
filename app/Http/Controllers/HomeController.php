<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Datatables;
use Crypt;
use Auth;
use Image;

class HomeController extends Controller
{
    function index(){
    	 $pagetitle = "Beranda";
    	 return view('home.index', compact('pagetitle'));
    }

    
}
 