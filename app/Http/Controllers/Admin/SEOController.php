<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\SEOData;

class SEOController extends Controller
{
    public function index(Request $request){
        // Get page from $_GET
        $page = ( isset( $_GET['page'] ) && is_numeric( $_GET['page'] ) )? $_GET['page'] : 1;

        // Get limit on results to fetch
        $limit = \Config::get('pss.items_per_page', 15 );

        // Fetch media files
        $all_seo_data = SEOData::where([[ 'url', 'LIKE', "%{$request->search}%" ]])
            ->orWhere([[ 'title', 'LIKE', "%{$request->search}%" ]])
            ->orderByRaw("`url` = '/' DESC, `url` ASC")->paginate($limit);

        // Count the media files
        $total_seo_data = SEOData::count();
        $pages = (ceil($total_seo_data/$limit) > 1)? ceil($total_seo_data/$limit) : 1;

        // If we're on an invalid page number, redirect
        if( $page > $pages )
            return redirect()->action('Admin\SEOController@index', [ 'page' => $pages ] );

        // Index
        return view('admin.seo.index', [
            'page_title' => 'SEO Data',
            'seo_data' => $all_seo_data,
            'limit' => $limit,
        ]);
    }

    public function edit(Request $request, SEOData $seo_data){
        return view('admin.seo.form', [
            'seo_data' => $seo_data,
            'page_title' => '<a href="' . action('Admin\SEOController@index') . '">SEO Data</a> <span class=\'fa fa-angle-right\'></span> Editing &quot;' . $seo_data->title . '&quot;',
            'is_new' => false,
            'hide_search' => true,
        ]);
    }

    public function new(Request $request){
        
        $seo_data = new SEOData;

        return view('admin.seo.form', [
            'seo_data' => $seo_data,
            'page_title' => '<a href="' . action('Admin\SEOController@index') . '">SEO Data</a> <span class=\'fa fa-angle-right\'></span> New SEOData',
            'is_new' => true,
            'hide_search' => true,
        ]);
    }

    public function update(Request $request, SEOData $seo_data){

        // If doesn't exist, redirect back to index
        if( is_null( $seo_data ) )
            return redirect()->action('Admin\SEOController@index');
        
        // Else, continue
        $this->processData($request, [
            'seo_data.url' => [
                'string', 'max:255', Rule::unique('seo_data', 'url')->ignore($seo_data->id)
            ]
        ]);

        // Update fields
        if( $seo_data->update($request->seo_data) )
        {
            // Set flash
            \Session::flash('success', "Updated SEO Data: {$seo_data->title}");

            // Redirect back to index
            return redirect()->back();
        }
        else{
            // Set flash
            \Session::flash('warning', "Could not save the SEO Data, try again later");

            // Redirect back to index
            return redirect()->back();
        }

    }

    public function create(Request $request){

        // Validate data
        $this->processData($request, [
            'seo_data.url' => 'string|max:255|unique:seo_data,url'
        ]);

        // If all good, save to DB
        $seo_data = new SEOData($request->seo_data);
        $seo_data->save();

        // Set flash
        \Session::flash('success', "Created new SEO Data: {$seo_data->title}");

        // Redirect back to index
        return redirect()->action('Admin\SEOController@index');

    }

    /**
    * Validates data for seo_data
    */
    private function processData($request, $custom_rules=[]){

        // Start validation
        return $this->validate($request, array_merge([
            'seo_data.title' => 'required|string|max:255',
            'seo_data.url' => 'string|max:255',
            'seo_data.description' => 'string|max:255',

            'seo_data.og_title' => 'string|max:255',
            'seo_data.og_url' => 'string|max:255',
            'seo_data.og_description' => 'string|max:255',

            'seo_data.og_image_id' => 'exists:media,id',
            'seo_data.twitter_image_id' => 'exists:media,id',
        ], $custom_rules));
    }

    public function delete(Request $request, SEOData $seo_data){

        // If not exist, redirect to index
        if( is_null( $seo_data ) )
            return redirect()->action('Admin\SEOController@index');

        // Delete it!
        $seo_data_name = $seo_data->title;
        $seo_data->delete();

        // Delete flash message
        \Session::flash('deleted', "Deleted SEO Data: {$seo_data_name}");

        // Redirect back to index
        return redirect()->action('Admin\SEOController@index');

    }
}
