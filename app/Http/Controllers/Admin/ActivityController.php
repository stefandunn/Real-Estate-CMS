<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ActivityLog;

class ActivityController extends Controller
{

    public function index(Request $request)
    {
    	// Get page from $_GET
	    $page = ( isset( $_GET['page'] ) && is_numeric( $_GET['page'] ) )? $_GET['page'] : 1;

	    // Get limit on results to fetch
	    $limit = \Config::get('pss.items_per_page', 15 );

	    // Fetch media files
	    $activity_logs = ActivityLog::with('user')
	        ->join('users', 'activity_logs.user_id', '=', "users.id")
	        ->where([[ 'users.name', 'LIKE', "%{$request->search}%" ]])
	        ->orWhere([[ 'action', 'LIKE', "%{$request->search}%" ]])
	        ->orWhere([[ 'model', 'LIKE', "%{$request->search}%" ]])
	        ->orderBy('activity_logs.created_at', 'desc')
	    	->paginate($limit);

	    // Count the media files
	    $total_activity_logs = ActivityLog::count();
        $pages = (ceil($total_activity_logs/$limit) > 1)? ceil($total_activity_logs/$limit) : 1;

	    // If we're on an invalid page number, redirect
	    if( $page > $pages )
	        return redirect()->action('Admin\activity_logsController@index', [ 'page' => $pages ] );

	    // Index
    	return view('admin.activity.index', [
			'page_title' => 'Activity Logs',
			'activity_logs' => $activity_logs,
			'limit' => $limit,
            'action_colours' => [
            	'update' => 'rgba(243,156,18,.2)',
            	'login' => 'rgba(243,156,18,.2)',
            	'reset password' => 'rgba(243,156,18,.2)',
            	'delete' => 'rgba(231,76,60,.2)',
            	'logout' => 'rgba(231,76,60,.2)',
            	'failed to reset password' => 'rgba(231,76,60,.2)',
            	'create' => 'rgba(38,185,154,.2)',
            	'request password reset' => 'rgba(38,185,154,.2)',
            ]
        ]);
    }

    public function clear(Request $request){

    	// Delete all logs
    	ActivityLog::getQuery()->delete();

    	// Set flash
    	\Session::flash('success', 'Sucecssfully cleared all activity logs');

    	// Redirect back
    	return redirect()->back();
    }
}
