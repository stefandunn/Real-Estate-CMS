<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Load sitemap route
require "sitemap.php";

// Load up admin routes;
require "admin.php";

Route::group( ['middleware' => ['web'] ], function(){

	// Homepage
	Route::get('/', 'HomeController@index' )->name('home');

	// Properties
	Route::get('/properties', 'PropertiesController@index')->name('properties.index');
	Route::get('/properties/{property}', 'PropertiesController@show')->name('properties.show');

	// Catch all for page slugs
	Route::get("/{page_slug}", 'PagesController@show')->where([ 'page_slug' => '^(?!(?:dashboard|api)).+$'])->name('page.show');


});