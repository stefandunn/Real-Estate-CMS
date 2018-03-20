<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NewsletterSubscriber;

class SubscribersController extends Controller
{
    // Index page
    public function index(Request $request){

    	// Get page from $_GET
	    $page = ( isset( $_GET['page'] ) && is_numeric( $_GET['page'] ) )? $_GET['page'] : 1;

	    // Get limit on results to fetch
	    $limit = \Config::get('pss.items_per_page', 15 )*2; // Show twice as many as normal, as there's not much data

	    // Fetch media files
	    $subscribers = NewsletterSubscriber::orderBy('created_at', 'desc')
	    	->where([[ 'first_name', 'LIKE', "%{$request->search}%" ]])
	        ->orWhere([[ 'last_name', 'LIKE', "%{$request->search}%" ]])
	        ->orWhere([[ 'email', 'LIKE', "%{$request->search}%" ]])
	    	->paginate($limit);

	    // Count the media files
	    $total_subscribers = NewsletterSubscriber::count();
        $pages = (ceil($total_subscribers/$limit) > 1)? ceil($total_subscribers/$limit) : 1;

	    // If we're on an invalid page number, redirect
	    if( $page > $pages )
	        return redirect()->action('Admin\SubscribersController@index', [ 'page' => $pages ] );

	    // Index
    	return view('admin.subscribers.index', [
			'page_title' => 'Subscribers',
			'subscribers' => $subscribers,
			'limit' => $limit,
        ]);
    }

    public function export(Request $request)
    {
    	if( !empty($request->export_function) )
    	{
	    	// Will validate and return a list of results if successfully validated
	    	$results = $this->getResults($request);

	    	// If requested to export, do so
	    	if( strstr($request->export_function, 'Export') )
	    	{
	    		// Change headers so that the file is downloaded rather than displayed
				header('Content-Type: text/csv; charset=utf-8');
				header('Content-Disposition: attachment; filename=export-data.csv');

				// create a file pointer connected to the output stream
				$output = fopen('php://output', 'w');

				// Output the column headings
				fputcsv($output, \Schema::getColumnListing((new NewsletterSubscriber)->getTable()));

				// Loop over records
				foreach ($results as $result)
					fputcsv($output, $result->attributesToArray());

				fclose($output);
				exit;
	    	}
	    }

    	// Return the view
    	return view('admin.subscribers.export', [
    		'page_title' => '<a href="' . action('Admin\SubscribersController@index') . '">Subscribers</a> <span class=\'fa fa-angle-right\'></span> Export',
    		'hide_search' => true,
    		'preview_results' => (!empty($results))? $results : null,
    		'export_options' => (!empty($request->export))? $request->export : null,
	    ]);
    }

    private function getResults($request){
    	
    	// Validate
    	$this->validate($request, [
    		'export_function' => ['regex:/(Export\s(?:All|Filtered))|(Preview\sResults)/'],
    		'export.first_name' => "string|max:255",
    		'export.first_name_type' => ['string', 'regex:/(loose|exact)/'],
    		'export.last_name' => "string|max:255",
    		'export.last_name_type' => ['string', 'regex:/(loose|exact)/'],
    		'export.email' => "string|max:255",
    		'export.email_type' => ['string', 'regex:/(loose|exact)/'],
    		'export.from_date' => "string",
    		'export.to_date' => "string",
    	]);

    	// Create export query
    	$query = NewsletterSubscriber::orderBy('first_name', 'asc')->orderBy('last_name', 'asc');

    	// Get export filters
    	$export_filters = ( !empty($request->export) )? $request->export : null;

    	/* Perform filters */

    	// First name
    	if( !empty($export_filters['first_name']) )
    		$query = ( !empty($export_filters['first_name_type']) && $export_filters['first_name_type'] == 'loose')? $query->where([['first_name', 'LIKE', '%'.$export_filters['first_name'].'%']]) : $query->where([['first_name', '=', $export_filters['first_name']]]);
    	
    	// Last name
    	if( !empty($export_filters['last_name']) )
    		$query = ( !empty($export_filters['last_name_type']) && $export_filters['last_name_type'] == 'loose')? $query->where([['last_name', 'LIKE', '%'.$export_filters['last_name'].'%']]) : $query->where([['last_name', '=', $export_filters['last_name']]]);
    	
    	// Email
    	if( !empty($export_filters['email']) )
    		$query = ( !empty($export_filters['email_type']) && $export_filters['email_type'] == 'loose')? $query->where([['email', 'LIKE', '%'.$export_filters['email'].'%']]) : $query->where([['email', '=', $export_filters['email']]]);
    	
    	// From date
    	if( !empty($export_filters['from_date']) )
    		$query = $query->where([['created_at', '>=', date('Y-m-d H:i:s', strtotime($export_filters['from_date'] . "00:00:00")) ]]);
    	

    	// To date
    	if( !empty($export_filters['to_date']) )
    		$query = $query->where([['created_at', '<=', date('Y-m-d H:i:s', strtotime($export_filters['to_date'] . "23:59:59")) ]]);

    	// Execute query
    	return $query->get();

    }
}
