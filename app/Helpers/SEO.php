<?php

global $all_seo_data;

/*
 * Alias of getValue from Settings model
**/
function getSEOField($key, $fallback=null){

	// Use global all settings variable
	global $all_seo_data;

	// If the settings has already been retrieved, get from variable
	if( is_array( $all_seo_data ) && array_key_exists( $key, $all_seo_data ) )
		return $all_seo_data[ $key ];

	// Else, fetch it
	else {
		// If nothing in $all_settingts, create new array with first element the retrieved setting value
		if( !is_array( $all_seo_data ) )
			$all_seo_data = [ $key => \App\SEOData::getValue($key, $fallback) ];

		// Else, set a new key with new value retrieved
		else
			$all_seo_data[$key] = \App\SEOData::getValue($key, $fallback);

		// Returned value
		return $all_seo_data[ $key ];
	}
}