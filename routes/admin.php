<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
|
*/

Route::group(['prefix' => 'dashboard', 'middleware' => ['web']], function () {

	Route::group( [ 'middleware' => ['admin.antiauth'] ], function(){
		// Login
		Route::get('login', 'Admin\AccountController@login');
		Route::post('login', 'Admin\AccountController@doLogin');

		// Reset Password Actions
		Route::get('reset-password/token/{token}', 'Admin\AccountController@resetPasswordByToken'); // Gets the token via email and shows form to reset password
		Route::post('reset-password/do', 'Admin\AccountController@doReset'); // Does the resetting
		Route::get('reset-password', 'Admin\AccountController@resetPassword'); // Reset password page via login page
		Route::post('reset-password', 'Admin\AccountController@requestResetPassword'); // Request password reset email
	});

	Route::group( [ 'middleware' => 'admin.auth' ], function(){

		// Admin dashboard
		Route::get('/', 'Admin\DashboardController@index');

		// Logout
		Route::get('logout', 'Admin\AccountController@logout');

		// Account settings
		Route::get('account/settings', 'Admin\AccountController@settings');
		Route::patch('account/settings', 'Admin\AccountController@updateSettings');


		// Navigation
		Route::get('navigation', 'Admin\NavigationController@index');
		Route::post('navigation', 'Admin\NavigationController@save');


		// Media library
		Route::get('media', 'Admin\MediaController@index');
		Route::post('media/upload', 'Admin\MediaController@upload');
		Route::get('media/edit/{media_file}', 'Admin\MediaController@edit')->where(['media_file' => '\d+']);
		Route::patch('media/update/{media_file}', 'Admin\MediaController@update')->where(['media_file' => '\d+']);
		Route::get('media/delete/{media_file}', 'Admin\MediaController@delete')->where(['media_file' => '\d+']);
		Route::get('media/regenerate-images/{media_file}', 'Admin\MediaController@regenerateImages')->where(['media_file' => '\d+']);
		Route::post('media/regenerate-images/', 'Admin\MediaController@bulkRegenerateImages');
		Route::post('media/delete/', 'Admin\MediaController@bulkDelete');


		// Properties
		Route::get('properties', 'Admin\PropertiesController@index');
		Route::get('properties/new', 'Admin\PropertiesController@new');
		Route::post('properties/new', 'Admin\PropertiesController@create');
		Route::get('properties/edit/{property}', 'Admin\PropertiesController@edit')->where(['property' => '\d+']);
		Route::get('properties/report/{property}', 'Admin\PropertiesController@generateReport')->where(['property' => '\d+']);
		Route::patch('properties/update/{property}', 'Admin\PropertiesController@update')->where(['property' => '\d+']);
		Route::get('properties/delete/{property}', 'Admin\PropertiesController@delete')->where(['property' => '\d+']);
		Route::get('properties/files/{property}', 'Admin\PropertiesController@files')->where(['property' => '\d+']);
		Route::post('properties/update-files/{property}', 'Admin\PropertiesController@updateFiles')->where(['property' => '\d+']);

		// Property Types
		Route::get('property-types', 'Admin\PropertyTypesController@index');
		Route::get('property-types/new', 'Admin\PropertyTypesController@new');
		Route::post('property-types/new', 'Admin\PropertyTypesController@create');
		Route::get('property-types/edit/{property_type}', 'Admin\PropertyTypesController@edit')->where(['property_type' => '\d+']);
		Route::get('property-types/report/{property_type}', 'Admin\PropertyTypesController@generateReport')->where(['property_type' => '\d+']);
		Route::patch('property-types/update/{property_type}', 'Admin\PropertyTypesController@update')->where(['property_type' => '\d+']);
		Route::get('property-types/delete/{property_type}', 'Admin\PropertyTypesController@delete')->where(['property_type' => '\d+']);

		// Purchase Types
		Route::get('purchase-types', 'Admin\PurchaseTypesController@index');
		Route::get('purchase-types/new', 'Admin\PurchaseTypesController@new');
		Route::post('purchase-types/new', 'Admin\PurchaseTypesController@create');
		Route::get('purchase-types/edit/{purchase_type}', 'Admin\PurchaseTypesController@edit')->where(['purchase_type' => '\d+']);
		Route::get('purchase-types/report/{purchase_type}', 'Admin\PurchaseTypesController@generateReport')->where(['purchase_type' => '\d+']);
		Route::patch('purchase-types/update/{purchase_type}', 'Admin\PurchaseTypesController@update')->where(['purchase_type' => '\d+']);
		Route::get('purchase-types/delete/{purchase_type}', 'Admin\PurchaseTypesController@delete')->where(['purchase_type' => '\d+']);

		// Purchase Types
		Route::get('pricing-types', 'Admin\PricingTypesController@index');
		Route::get('pricing-types/new', 'Admin\PricingTypesController@new');
		Route::post('pricing-types/new', 'Admin\PricingTypesController@create');
		Route::get('pricing-types/edit/{pricing_type}', 'Admin\PricingTypesController@edit')->where(['pricing_type' => '\d+']);
		Route::get('pricing-types/report/{pricing_type}', 'Admin\PricingTypesController@generateReport')->where(['pricing_type' => '\d+']);
		Route::patch('pricing-types/update/{pricing_type}', 'Admin\PricingTypesController@update')->where(['pricing_type' => '\d+']);
		Route::get('pricing-types/delete/{pricing_type}', 'Admin\PricingTypesController@delete')->where(['pricing_type' => '\d+']);


		// Newsletter subscribers
		Route::get('subscribers', 'Admin\SubscribersController@index');
		Route::get('subscribers/export', 'Admin\SubscribersController@export');
		Route::post('subscribers/export', 'Admin\SubscribersController@doExport');


		// Page content
		Route::get('pages', 'Admin\PagesController@index');
		Route::get('pages/new', 'Admin\PagesController@new');
		Route::post('pages/new', 'Admin\PagesController@create');
		Route::get('pages/edit/{page}', 'Admin\PagesController@edit')->where(['page' => '\d+']);
		Route::patch('pages/update/{page}', 'Admin\PagesController@update')->where(['page' => '\d+']);
		Route::get('pages/delete/{page}', 'Admin\PagesController@delete')->where(['page' => '\d+']);


		// SEO content
		Route::get('seo-data', 'Admin\SEOController@index');
		Route::get('seo-data/new', 'Admin\SEOController@new');
		Route::post('seo-data/new', 'Admin\SEOController@create');
		Route::get('seo-data/edit/{seo_data}', 'Admin\SEOController@edit')->where(['seo_data' => '\d+']);
		Route::patch('seo-data/update/{seo_data}', 'Admin\SEOController@update')->where(['seo_data' => '\d+']);
		Route::get('seo-data/delete/{seo_data}', 'Admin\SEOController@delete')->where(['seo_data' => '\d+']);


		// Users
		Route::get('users', 'Admin\UsersController@index');
		Route::get('users/new', 'Admin\UsersController@new');
		Route::post('users/new', 'Admin\UsersController@create');
		Route::get('users/edit/{user}', 'Admin\UsersController@edit');
		Route::patch('users/edit/{user}', 'Admin\UsersController@update');
		Route::get('users/delete/{user}', 'Admin\UsersController@delete');


		// Settings
		Route::get('settings', 'Admin\SettingsController@index');
		Route::post('settings/update', 'Admin\SettingsController@update');


		// Activity Logs
		Route::get('logs', 'Admin\ActivityController@index');
		Route::get('logs/clear', 'Admin\ActivityController@clear');

	});

});