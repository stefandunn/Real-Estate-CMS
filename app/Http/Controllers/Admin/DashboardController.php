<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    //
    public function index(){
    	return view( 'admin.dashboard.index', [
    		'downloads' => \App\DocumentDownload::select( \DB::raw( "SUM(`downloads`) AS `total_downloads`" ) )->first()->total_downloads,
    		'subscribers' => \App\NewsletterSubscriber::count(),
    		'media_files' => \App\MediaFile::count(),
            'properties' => \App\Property::count(),
            'activity_logs' => \App\ActivityLog::orderBy('created_at', 'desc')->limit(5)->get(),
    		'page_title' => 'Dashboard',
            'hide_search' => true,
    	] );
    }
}
