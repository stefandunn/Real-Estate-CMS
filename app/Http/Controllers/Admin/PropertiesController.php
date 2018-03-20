<?php

namespace App\Http\Controllers\Admin;

use \App\Property;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class PropertiesController extends Controller
{
	public function index(Request $request){
	    // Get page from $_GET
	    $page = ( isset( $_GET['page'] ) && is_numeric( $_GET['page'] ) )? $_GET['page'] : 1;

	    // Get limit on results to fetch
	    $limit = \Config::get('pss.items_per_page', 15 );

	    // Fetch media files
	    $properties = Property::with('featureImage')
	        ->where([[ 'name', 'LIKE', "%{$request->search}%" ]])
	        ->orWhere([[ 'reference_code', 'LIKE', "%{$request->search}%" ]])
	        ->orWhere([[ 'address_line_1', 'LIKE', "%{$request->search}%" ]])
	        ->orWhere([[ 'town', 'LIKE', "%{$request->search}%" ]])
	        ->orWhere([[ 'postcode', 'LIKE', "%{$request->search}%" ]])
	        ->orWhere([[ 'tags', 'LIKE', "%{$request->search}%" ]])
	        ->orWhere([[ 'contact_email', 'LIKE', "%{$request->search}%" ]])
	        ->orWhere([[ 'contact_number', 'LIKE', "%{$request->search}%" ]])
	    	->orderBy('created_at', 'desc')
	    	->paginate($limit);

	    // Count the media files
	    $total_properties = Property::count();
        $pages = (ceil($total_properties/$limit) > 1)? ceil($total_properties/$limit) : 1;

	    // If we're on an invalid page number, redirect
	    if( $page > $pages )
	        return redirect()->action('Admin\PropertiesController@index', [ 'page' => $pages ] );

	    // Index
    	return view('admin.properties.index', [
			'page_title' => 'Properties',
			'properties' => $properties,
			'limit' => $limit,
        ]);
	}

	public function edit(Request $request, Property $property){
		return view('admin.properties.form', [
			'property' => $property,
            'page_title' => '<a href="' . action('Admin\PropertiesController@index') . '">Properties</a> <span class=\'fa fa-angle-right\'></span> Editing &quot;' . $property->name . '&quot;',
            'is_new' => false,
            'hide_search' => true,
		]);
	}

	public function new(Request $request){
		
		$property = new Property;

		return view('admin.properties.form', [
			'property' => $property,
            'page_title' => '<a href="' . action('Admin\PropertiesController@index') . '">Properties</a> <span class=\'fa fa-angle-right\'></span> New Property',
            'is_new' => true,
            'hide_search' => true,
		]);
	}

	public function update(Request $request, Property $property){
		// If doesn't exist, redirect back to index
		if( is_null( $property ) )
			return redirect()->action('Admin\PropertiesController@index');
		
		// Else, continue
		$this->processData($request, [
			'property.reference_code' => [
				'string', 'size:6', Rule::unique('properties', 'reference_code')->ignore($property->id)
			]
		]);

		// Update fields
		if( $property->update($request->property) )
		{
			// Set flash
			\Session::flash('success', "Updated property: {$property->name}");

			// Redirect back to index
			return redirect()->back();
		}
		else{
			// Set flash
			\Session::flash('warning', "Could not save the property, try again later");

			// Redirect back to index
			return redirect()->back();
		}

	}

	public function create(Request $request){

		// Validate data
		$this->processData($request, [
			'property.reference_code' => 'string|size:6|unique:properties,reference_code'
		]);

		// If all good, save to DB
		$property = new Property($request->property);
		$property->save();

		// Set flash
		\Session::flash('success', "Created new property: {$property->name}");

		// Redirect to files page
		return redirect()->action('Admin\PropertiesController@files', ['property' => $property->id]);

	}

	/**
	* Validates data for properties
	*/
	private function processData($request, $custom_rules=[]){

		// Start validation
		return $this->validate($request, array_merge([
			'property.name' => 'required',
			'property.price' => 'numeric|max:99999999999',
			'property.property_type_id' => 'required|exists:property_types,id',
			'property.purchase_type_id' => 'required|exists:purchase_types,id',
			'property.pricing_type_id' => 'required|exists:pricing_types,id',
			'property.feature_image_id' => 'required|exists:media,id',
			'property.snippet' => 'required|string',
			'property.description' => 'string',
			'property.overview' => 'string',
			'property.contact_email' =>'email|string|max:255',
			'property.contact_number' =>'string|max:255',
			'property.latitude' => 'string|max:20',
			'property.longitude' => 'string|max:20',
			'property.short_address' => 'string|max:255',
			'property.postcode' => 'string|max:10',
			'property.town' => 'string|max:255',
			'property.address_line_1' => 'string|max:255',
			'property.address_line_2' => 'string|max:255',
			'property.tags' => 'string',
			'property.square_footage' => 'numeric|max:99999999999'
		], $custom_rules));
	}

	public function delete(Request $request, Property $property){

		// If not exist, redirect to index
		if( is_null( $property ) )
			return redirect()->action('Admin\PropertiesController@index');

		// Delete it!
		$property_name = $property->name;
		$property->delete();

		// Delete flash message
		\Session::flash('deleted', "Deleted property: {$property_name}");

		// Redirect back to index
		return redirect()->action('Admin\PropertiesController@index');

	}


	public function files(Request $request, Property $property){
		return view( 'admin.properties.files', [
			'page_title' => '<a href="' . action('Admin\PropertiesController@index') . '">Properties</a> <span class=\'fa fa-angle-right\'></span> &quot;<a href="' . action('Admin\PropertiesController@edit', ['property' => $property->id] ) . '">' . $property->name . '</a>&quot; <span class=\'fa fa-angle-right\'></span> Files',
			'property' => $property
		]);
	}

	public function updateFiles(Request $request, Property $property){
		
		// Validate data
		$this->validate( $request, [
			'image.*' => 'required|exists:media,id',
			'documents.*' => 'required|exists:media,id',
 		]);

 		// Get all current files for property
 		$files = $property->fileLinks;

 		// Loop through request
 		$key = 0;
 		foreach ($request->get('images') as $value) {

 			// If existing files goes to this many idnexes, update
 			if( array_key_exists($key, $files) )
 			{

	 			// Update entry
	 			$files[$key]->update([
	 				'file_id' => $value,
	 				'type' => 'image'
	 			]);

	 			// Remove from process
	 			$files->forget($key);
	 		}
	 		else
	 			// Craete new entry
	 			\App\PropertyFile::create([
	 				'property_id' => $property->id,
	 				'file_id' => $value,
	 				'type' => 'image'
	 			]);

	 		$key++;
 		}

 		// Loop through request
 		foreach ($request->get('documents') as $value) {

 			// If existing files goes to this many idnexes, update
 			if( array_key_exists($key, $files) )
 			{

	 			// Update entry
	 			$files[$key]->update([
	 				'file_id' => $value,
	 				'type' => 'document'
	 			]);

	 			// Remove from process
	 			$files->forget($key);
	 		}
	 		else
	 			// Craete new entry
	 			\App\PropertyFile::create([
	 				'property_id' => $property->id,
	 				'file_id' => $value,
	 				'type' => 'document'
	 			]);

	 		$key++;
 		}

 		// Any left-over files, delete
 		$files->each(function ($item, $key){
 			$item->delete();
 		});

		// Flash
		\Session::flash('success', 'Updated documents for property, &quot;' . $property->name . '&quot;');

		// Redirect back to page
		return redirect()->back();

	}
}
