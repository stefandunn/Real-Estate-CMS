<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    // Show page
    public function show($page_slug){
    	
    	// Attempt to find the page
    	$page = \App\Page::where(['slug' => $page_slug ])->first();

    	// If not found, envoke 404
    	if( is_null( $page ) )
    		\App::abort(404);

        // IF found, and in preview, ensure we're logged in

        if( ($page->status == 0 && empty(\Request::query('preview'))) || ($page->status == 0 && \Auth::user() == null) )
            \App::abort(404);            

    	// Attermpt to find page template for this page only
    	if( file_exists(\Config::get('view.paths')[0] . '/front/pages/specific/' . str_replace("/", "_", $page_slug) . '.blade.php') )
			$view_template = 'front.pages.specific.' . str_replace("/", "_", $page_slug);
		else
			$view_template = 'front.pages.default';
    	
    	return view($view_template, [
    		'page' => $page,
    	]);
    }
}
