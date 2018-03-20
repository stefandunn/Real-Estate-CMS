<?php

global $all_settings;

/*
 * Alias of getValue from Settings model
**/
function getSetting($key, $fallback=null){

	// Use global all settings variable
	global $all_settings;

	// If the settings has already been retrieved, get from variable
	if( is_array( $all_settings ) && array_key_exists( $key, $all_settings ) )
		return $all_settings[ $key ];

	// Else, fetch it
	else {
		// If nothing in $all_settingts, create new array with first element the retrieved setting value
		if( !is_array( $all_settings ) )
			$all_settings = [ $key => \App\Settings::getValue($key, $fallback) ];

		// Else, set a new key with new value retrieved
		else
			$all_settings[$key] = \App\Settings::getValue($key, $fallback);

		// Returned value
		return $all_settings[ $key ];
	}
}

 // Get flash messages
global $flash_messages;
function getFlashMessages( $laravel_errors = null ){
	
	global $flash_messages;

	// Return if already exists
	if( !is_array( $flash_messages ) )
		$flash_messages = [];
	else
		return $flash_messages;

	// Loop through flash messages
	foreach(\Session::all() as $key => $message){
	    if(is_string($message) && $message !== \Session::token())
	        $flash_messages[$key] = $message;
	}

	// Loop through errors
	if( !is_null( $laravel_errors ) && count( $laravel_errors ) > 0 )
	{
		foreach ($laravel_errors->all() as  $error)
			$flash_messages['error'] = $error;
	}

	// Share flash messages
	return $flash_messages;
}