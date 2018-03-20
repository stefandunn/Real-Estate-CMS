<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    // Homepage index
    public function index(){
    	return view( 'front.home.index' );
    }
}
