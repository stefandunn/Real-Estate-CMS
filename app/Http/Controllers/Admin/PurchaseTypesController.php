<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PurchaseType;

class PurchaseTypesController extends Controller
{
    public function index(Request $request){
		// Get page from $_GET
		$page = ( isset( $_GET['page'] ) && is_numeric( $_GET['page'] ) )? $_GET['page'] : 1;

		// Get limit on results to fetch
		$limit = \Config::get('pss.items_per_page', 15 );

		// Fetch media files
		$purchase_types = PurchaseType::where([[ 'name', 'LIKE', "%{$request->search}%" ]])
			->orderBy('created_at', 'desc')
			->paginate($limit);

		// Count the media files
		$total_purchase_types = PurchaseType::count();
		$pages = (ceil($total_purchase_types/$limit) > 1)? ceil($total_purchase_types/$limit) : 1;

		// If we're on an invalid page number, redirect
		if( $page > $pages )
			return redirect()->action('Admin\PurchaseTypesController@index', [ 'page' => $pages ] );

		// Index
		return view('admin.purchase-types.index', [
			'page_title' => 'Property Types',
			'purchase_types' => $purchase_types,
			'limit' => $limit,
		]);
	}

	public function edit(Request $request, PurchaseType $purchase_type){
		return view('admin.purchase-types.form', [
			'purchase_type' => $purchase_type,
			'page_title' => '<a href="' . action('Admin\PurchaseTypesController@index') . '">Property Types</a> <span class=\'fa fa-angle-right\'></span> Editing &quot;' . $purchase_type->name . '&quot;',
			'is_new' => false,
			'hide_search' => true,
		]);
	}

	public function new(Request $request){
		
		$purchase_type = new PurchaseType;

		return view('admin.purchase-types.form', [
			'purchase_type' => $purchase_type,
			'page_title' => '<a href="' . action('Admin\PurchaseTypesController@index') . '">Property Types</a> <span class=\'fa fa-angle-right\'></span> New Purchase Type',
			'is_new' => true,
			'hide_search' => true,
		]);
	}

	public function update(Request $request, PurchaseType $purchase_type){
		// If doesn't exist, redirect back to index
		if( is_null( $purchase_type ) )
			return redirect()->action('Admin\PurchaseTypesController@index');
		
		// Else, continue
		$this->processData($request, [
			'purchase_type.name' => [
				'string', Rule::unique('purchase_types', 'name')->ignore($purchase_type->id)
			]
		]);

		// Update fields
		if( $purchase_type->update($request->purchase_type) )
		{
			// Set flash
			\Session::flash('success', "Updated purchase type: {$purchase_type->name}");

			// Redirect back to index
			return redirect()->back();
		}
		else{
			// Set flash
			\Session::flash('warning', "Could not save the purchase type, try again later");

			// Redirect back to index
			return redirect()->back();
		}

	}

	public function create(Request $request){

		// Validate data
		$this->processData($request, [
			'purchase_type.name' => 'string|unique:purchase_types,name'
		]);

		// If all good, save to DB
		$purchase_type = new PurchaseType($request->purchase_type);
		$purchase_type->save();

		// Set flash
		\Session::flash('success', "Created new purchase type: {$purchase_type->name}");

		// Redirect back to index
		return redirect()->action('Admin\PurchaseTypesController@index');

	}

	/**
	* Validates data for purchase types
	*/
	private function processData($request, $custom_rules=[]){

		// Start validation
		return $this->validate($request, array_merge([
			'purchase_type.name' => 'required',
		], $custom_rules));
	}

	public function delete(Request $request, PurchaseType $purchase_type){

		// If not exist, redirect to index
		if( is_null( $purchase_type ) )
			return redirect()->action('Admin\PurchaseTypesController@index');

		// Delete it!
		$purchase_type_name = $purchase_type->name;
		$purchase_type->delete();

		// Delete flash message
		\Session::flash('deleted', "Deleted purchase type: {$purchase_type_name}");

		// Redirect back to index
		return redirect()->action('Admin\PurchaseTypesController@index');

	}
}
