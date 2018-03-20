<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Navigation;
use App\Page;
use App\Property;
use App\MediaFile;

class NavigationController extends Controller
{
    // Show menu create/edit form
    public function index(){
    	return view('admin.navigation.index', [
    		'page_title' => 'Navigation Setup',
    		'hide_search' => true,
    		'header_nav_items' => Navigation::where([ 'useful_link' => 0 ])->where(['parent_id' => null ])->get(),
    		'footer_nav_items' => Navigation::where([ 'useful_link' => 1 ])->where(['parent_id' => null ])->get(),
            'url_browser_lookup' => $this->getLinks(),
    	]);
    }

    // Save
    public function save(Request $request){

    	// Decode data
    	$decoded_data = json_decode($request->navigation_data);

    	// Construct data
    	$header_data = $decoded_data->header;
    	$footer_data = $decoded_data->footer;

    	// Test header data and store in array of good data
    	$good_header_data = [];
    	foreach ($header_data as $header_link) {

    		// If any children..
    		if( !empty( $header_link->children ) ){

	    		// Check children data
		    	foreach ($header_link->children as $key => $header_link_child) {

		    		// Ensure label is given
		    		if( empty( $header_link_child->label ) )
		    			$errors[] = "'{$header_link_child->label}' is not a valid label";

		    		// Ensure URL is valid
		    		if( !preg_match( "/^((https?:\/\/)?([\w\d-_]+)\.([\w\d-_\.]+)\/?\??([^#\n\r]*)?#?([^\n\r]*)|((?:\/?\w)+)|(?:\/))/", $header_link_child->url ) )
		    			$errors[] = "'{$header_link_child->url}' is not a valid URL address";
		    	}
		    }

    		// Ensure label is given
    		if( empty( $header_link->label ) )
    			$errors[] = "'{$header_link->label}' is not a valid label";

    		// Ensure URL is valid
    		if( !preg_match( "/^((https?:\/\/)?([\w\d-_]+)\.([\w\d-_\.]+)\/?\??([^#\n\r]*)?#?([^\n\r]*)|((?:\/?\w)+)|(?:\/))/", $header_link->url ) )
    			$errors[] = "'{$header_link->url}' is not a valid URL address";

    		// Ensure no errors before saving to DB
    		if( empty( $errors ) )
    			$good_header_data[] = $header_link;
    	}

    	// Test footer data and store in array of good data
    	$good_footer_data = [];
    	foreach ($footer_data as $footer_link) {

    		// Ensure label is given
    		if( empty( $footer_link->label ) )
    			$errors[] = "'{$footer_link->label}' is not a valid label";

    		// Ensure URL is valid
    		if( !preg_match( "/^((https?:\/\/)?([\w\d-_]+)\.([\w\d-_\.]+)\/?\??([^#\n\r]*)?#?([^\n\r]*)|((?:\/?\w)+)|(?:\/))/", $footer_link->url ) )
    			$errors[] = "'{$footer_link->url}' is not a valid URL address";

    		// Ensure no errors before saving to DB
    		if( empty( $errors ) )
    			$good_footer_data[] = $footer_link;
    	}

    	unset($header_link);
    	unset($footer_link);

    	// If no errors, save to DB
    	if( empty( $errors ) ) {
    		
    		// Delete all current nav items
    		Navigation::where([['id', '>', 0 ]])->delete();

    		// Loop through and save data
    		foreach ($good_header_data as $header_link) {

    			// Save parent to DB
    			$new_nav_item = Navigation::create([
    				'parent_id' => null,
    				'sort_order' => $header_link->sort_order,
    				'label' => $header_link->label,
    				'url' => $header_link->url,
    				'new_window' => $header_link->new_window,
    				'styling' => $header_link->css_styling,
    				'class' => $header_link->css_class,
    				'useful_link' => 0,
    			]);

    			// If any children, save those next
    			if( !empty( $header_link->children ) )
    			{
    				// Loop through them
    				foreach ($header_link->children as $child_header_link) {

    					// Create child nav item
    					Navigation::create([
		    				'parent_id' => $new_nav_item->id,
		    				'sort_order' => $child_header_link->sort_order,
		    				'label' => $child_header_link->label,
		    				'url' => $child_header_link->url,
		    				'new_window' => $child_header_link->new_window,
		    				'styling' => $child_header_link->css_styling,
		    				'class' => $child_header_link->css_class,
		    				'useful_link' => 0,
		    			]);
    				}
    			}
    		}

    		// Loop through and save data
    		foreach ($good_footer_data as $footer_link) {

    			// Save parent to DB
    			$new_nav_item = Navigation::create([
    				'parent_id' => null,
    				'sort_order' => $footer_link->sort_order,
    				'label' => $footer_link->label,
    				'url' => $footer_link->url,
    				'new_window' => $footer_link->new_window,
    				'styling' => $footer_link->css_styling,
    				'class' => $footer_link->css_class,
    				'useful_link' => 1,
    			]);
    		}
    		
    		// Saving done
    		\Session::flash('success', 'Saved navigation settings');
    	}

    	// Else, errors found
    	else
    		\Session::flash('error', "<ul><li>" . implode( "</li><li>", $errors ) . "</li></ul>");

    	// Redirect back to nav settings
    	return redirect()->back();
    }

    /**
    * Returns data for different types of content including media files, pages and properties
    *   - This is for the autocomplete dropdown on the URL browser
    */
    public function getLinks(){

        // Collected data
        $data = [];
        
        // If type is media only (or null), get media files
        foreach (@MediaFile::orderBy('title', 'asc')->get() as $file) {
            if( !isset($data['media']) )
                $data['media'] = [['value' => "File: {$file->title}", 'data' => ltrim($file->path, "/")]];
            else
                $data['media'][] = ['value' => "File: {$file->title}", 'data' => ltrim($file->path, "/")];
        }
        
        // If type is pages only (or null), get media files
        foreach (@Page::orderBy('title', 'asc')->get() as $page) {
            if( !isset($data['pages']) )
                $data['pages'] = [['value' => "Page: {$page->title}", 'data' => $page->slug]];
            else
                $data['pages'][] = ['value' => "Page: {$page->title}", 'data' => $page->slug];
        }
        
        // If type is properties only (or null), get media files
        foreach (@Property::orderBy('name', 'asc')->get() as $property) {
            if( !isset($data['properties']) )
                $data['properties'] = [['value' => "Property: {$property->name}", 'data' => \URL::action('PropertiesController@show', ['property' => $property->id ])]];
            else
                $data['properties'][] = ['value' => "Property: {$property->name}", 'data' => \URL::action('PropertiesController@show', ['property' => $property->id ])];
        }

        // Return type or all data
        return $data;

    }
}
