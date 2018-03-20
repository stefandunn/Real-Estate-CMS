<?php

namespace App\Http\Controllers\Admin;

use App\Settings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    // Index page
    public function index(Request $request){

    	// Show settings page
    	return view('admin.settings.index', [
    		'page_title' => 'Website Settings',
    		'hide_search' => true,
    	]);
    }

    // Update action
    public function update(Request $request){

    	// Validate input
    	$this->validate($request, [
    		'settings.*' => 'string',
    		'settings.title' => 'required|string|max:255',
    		'settings.logo_id' => 'required|integer|exists:media,id',
    		'settings.facebook_link' => 'string|max:255',
    		'settings.twitter_link' => 'string|max:255',
    		'settings.main_contact_email' => 'string|email|max:255',
    		'settings.main_contact_main_contact_number' => [ 'string', 'max:255', 'regex:(\+?[\d\s]+)' ],
    	]);

    	// Process settings
    	foreach (@$request->settings as $key => $value)
    	{
    		// Find, then update an existing setting if exists
    		$found_setting = Settings::where(['key' => $key])->first();

    		// IF found, update
    		if( !is_null( $found_setting ) )
    			$found_setting->update(['value' => $value]);

    		// Else, not found, create one
    		else
    			Settings::create(['key' => $key, 'value' => $value]);
    	}

    	// Set flash
    	\Session::flash('success', 'Successfully updated website settings');

    	return redirect()->back();
    }
}
