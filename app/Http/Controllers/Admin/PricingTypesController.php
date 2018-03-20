<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PricingType;

class PricingTypesController extends Controller
{
	public function index(Request $request){
		// Get page from $_GET
		$page = ( isset( $_GET['page'] ) && is_numeric( $_GET['page'] ) )? $_GET['page'] : 1;

		// Get limit on results to fetch
		$limit = \Config::get('pss.items_per_page', 15 );

		// Fetch media files
		$pricing_types = PricingType::where([[ 'name', 'LIKE', "%{$request->search}%" ]])
			->orderBy('created_at', 'desc')
			->paginate($limit);

		// Count the media files
		$total_pricing_types = PricingType::count();
		$pages = (ceil($total_pricing_types/$limit) > 1)? ceil($total_pricing_types/$limit) : 1;

		// If we're on an invalid page number, redirect
		if( $page > $pages )
			return redirect()->action('Admin\PricingTypesController@index', [ 'page' => $pages ] );

		// Index
		return view('admin.pricing-types.index', [
			'page_title' => 'Pricing Types',
			'pricing_types' => $pricing_types,
			'limit' => $limit,
		]);
	}

	public function edit(Request $request, PricingType $pricing_type){
		return view('admin.pricing-types.form', [
			'pricing_type' => $pricing_type,
			'page_title' => '<a href="' . action('Admin\PricingTypesController@index') . '">Pricing Types</a> <span class=\'fa fa-angle-right\'></span> Editing &quot;' . $pricing_type->name . '&quot;',
			'is_new' => false,
			'hide_search' => true,
		]);
	}

	public function new(Request $request){
		
		$pricing_type = new PricingType;

		return view('admin.pricing-types.form', [
			'pricing_type' => $pricing_type,
			'page_title' => '<a href="' . action('Admin\PricingTypesController@index') . '">Pricing Types</a> <span class=\'fa fa-angle-right\'></span> New Pricing Type',
			'is_new' => true,
			'hide_search' => true,
		]);
	}

	public function update(Request $request, PricingType $pricing_type){
		// If doesn't exist, redirect back to index
		if( is_null( $pricing_type ) )
			return redirect()->action('Admin\PricingTypesController@index');
		
		// Else, continue
		$this->processData($request, [
			'pricing_type.name' => [
				'string', Rule::unique('pricing_types', 'name')->ignore($pricing_type->id)
			]
		]);

		// Update fields
		if( $pricing_type->update($request->pricing_type) )
		{
			// Set flash
			\Session::flash('success', "Updated pricing type: {$pricing_type->name}");

			// Redirect back to index
			return redirect()->back();
		}
		else{
			// Set flash
			\Session::flash('warning', "Could not save the pricing type, try again later");

			// Redirect back to index
			return redirect()->back();
		}

	}

	public function create(Request $request){

		// Validate data
		$this->processData($request, [
			'pricing_type.name' => 'string|unique:pricing_types,name'
		]);

		// If all good, save to DB
		$pricing_type = new PricingType($request->pricing_type);
		$pricing_type->save();

		// Set flash
		\Session::flash('success', "Created new pricing type: {$pricing_type->name}");

		// Redirect back to index
		return redirect()->action('Admin\PricingTypesController@index');

	}

	/**
	* Validates data for pricing types
	*/
	private function processData($request, $custom_rules=[]){

		// Start validation
		return $this->validate($request, array_merge([
			'pricing_type.name' => 'required',
		], $custom_rules));
	}

	public function delete(Request $request, PricingType $pricing_type){

		// If not exist, redirect to index
		if( is_null( $pricing_type ) )
			return redirect()->action('Admin\PricingTypesController@index');

		// Delete it!
		$pricing_type_name = $pricing_type->name;
		$pricing_type->delete();

		// Delete flash message
		\Session::flash('deleted', "Deleted pricing type: {$pricing_type_name}");

		// Redirect back to index
		return redirect()->action('Admin\PricingTypesController@index');

	}
}
