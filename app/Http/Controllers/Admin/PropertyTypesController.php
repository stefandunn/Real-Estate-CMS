<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PropertyType;

class PropertyTypesController extends Controller
{
    public function index(Request $request){
		// Get page from $_GET
		$page = ( isset( $_GET['page'] ) && is_numeric( $_GET['page'] ) )? $_GET['page'] : 1;

		// Get limit on results to fetch
		$limit = \Config::get('pss.items_per_page', 15 );

		// Fetch media files
		$property_types = PropertyType::where([[ 'name', 'LIKE', "%{$request->search}%" ]])
			->orderBy('created_at', 'desc')
			->paginate($limit);

		// Count the media files
		$total_property_types = PropertyType::count();
		$pages = (ceil($total_property_types/$limit) > 1)? ceil($total_property_types/$limit) : 1;

		// If we're on an invalid page number, redirect
		if( $page > $pages )
			return redirect()->action('Admin\PropertyTypesController@index', [ 'page' => $pages ] );

		// Index
		return view('admin.property-types.index', [
			'page_title' => 'Property Types',
			'property_types' => $property_types,
			'limit' => $limit,
		]);
	}

	public function edit(Request $request, PropertyType $property_type){
		return view('admin.property-types.form', [
			'property_type' => $property_type,
			'page_title' => '<a href="' . action('Admin\PropertyTypesController@index') . '">Property Types</a> <span class=\'fa fa-angle-right\'></span> Editing &quot;' . $property_type->name . '&quot;',
			'is_new' => false,
			'hide_search' => true,
		]);
	}

	public function new(Request $request){
		
		$property_type = new PropertyType;

		return view('admin.property-types.form', [
			'property_type' => $property_type,
			'page_title' => '<a href="' . action('Admin\PropertyTypesController@index') . '">Property Types</a> <span class=\'fa fa-angle-right\'></span> New Property Type',
			'is_new' => true,
			'hide_search' => true,
		]);
	}

	public function update(Request $request, PropertyType $property_type){
		// If doesn't exist, redirect back to index
		if( is_null( $property_type ) )
			return redirect()->action('Admin\PropertyTypesController@index');
		
		// Else, continue
		$this->processData($request, [
			'property_type.name' => [
				'string', Rule::unique('property_types', 'name')->ignore($property_type->id)
			]
		]);

		// Update fields
		if( $property_type->update($request->property_type) )
		{
			// Set flash
			\Session::flash('success', "Updated property type: {$property_type->name}");

			// Redirect back to index
			return redirect()->back();
		}
		else{
			// Set flash
			\Session::flash('warning', "Could not save the property type, try again later");

			// Redirect back to index
			return redirect()->back();
		}

	}

	public function create(Request $request){

		// Validate data
		$this->processData($request, [
			'property_type.name' => 'string|unique:property_types,name'
		]);

		// If all good, save to DB
		$property_type = new PropertyType($request->property_type);
		$property_type->save();

		// Set flash
		\Session::flash('success', "Created new property type: {$property_type->name}");

		// Redirect back to index
		return redirect()->action('Admin\PropertyTypesController@index');

	}

	/**
	* Validates data for property types
	*/
	private function processData($request, $custom_rules=[]){

		// Start validation
		return $this->validate($request, array_merge([
			'property_type.name' => 'required',
		], $custom_rules));
	}

	public function delete(Request $request, PropertyType $property_type){

		// If not exist, redirect to index
		if( is_null( $property_type ) )
			return redirect()->action('Admin\PropertyTypesController@index');

		// Delete it!
		$property_type_name = $property_type->name;
		$property_type->delete();

		// Delete flash message
		\Session::flash('deleted', "Deleted property type: {$property_type_name}");

		// Redirect back to index
		return redirect()->action('Admin\PropertyTypesController@index');

	}
}
