<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PropertiesController extends Controller
{
    // Show index
    public function index(Request $request){
    	
    	// Search terms form request
    	$search_terms = $request->get('search');

    	// If search terms given, filter results
    	if(!empty($search_terms))
    	{
    		// Prope
    	}

    }
}
