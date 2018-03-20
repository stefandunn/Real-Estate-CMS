<?php


/**
* Get page details using URL to generate slug.
*	- Key: if provided, will return an attribute matching the key, or a fallback value if key does not exist
*	- Fallback: The fallback value if nothing found or key does not exist
*	- An override value for the page's URL
*/
function getPageDetailsByURL($key=null, $fallback=null, $url=null){

	// If URL is null, set to current request URL
	$url = (is_null($url) )? \Request::url() : $url;

	// Get slug
	$slug = str_replace( \URL::to('/'), '', $url );

	// Attempt to find page via slug
	$page = \App\Page::where(['slug' => $slug])->first();

	// If found, return status
	if( !is_null( $page ) )
	{

		// If we want to return a particular attribute, and it does exist, then return it
		if( !is_null($key) && isset($page->{$key}) )
			return $page->{$key};
		// If we want to return a particular attribute, and it doesn't exist, return fallback
		elseif( !is_null($key) && !isset($page->{$key}) )
			return $fallback;
		// Else, return the page
		else
			return $page;
	}

	// If no page found, return null
	else
		return $fallback;
}

/**
* Get page details via slug.
*	- Key: if provided, will return an attribute matching the key, or a fallback value if key does not exist
*	- Fallback: The fallback value if nothing found or key does not exist
*	- An override value for the page's URL
*/
function getPageDetailsBySlug($slug, $key=null, $fallback=null){

	// Attempt to find page via slug
	$page = \App\Page::where(['slug' => $slug])->first();

	// If found, return status
	if( !is_null( $page ) )
	{

		// If we want to return a particular attribute, and it does exist, then return it
		if( !is_null($key) && isset($page->{$key}) )
			return $page->{$key};
		// If we want to return a particular attribute, and it doesn't exist, return fallback
		elseif( !is_null($key) && !isset($page->{$key}) )
			return $fallback;
		// Else, return the page
		else
			return $page;
	}

	// If no page found, return null
	else
		return $fallback;
}