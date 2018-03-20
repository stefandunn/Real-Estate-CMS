<?php

/*
|--------------------------------------------------------------------------
| Sitemap Routes
|--------------------------------------------------------------------------
|
|
*/

Route::group(['prefix' => 'sitemap'], function(){

	// Sitemap index
	Route::get('/', 'SitemapController@index');

	// Pages
	Route::get('{name}/{render_type?}', 'SitemapController@renderSitemap');

});