<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Page;

class PagesController extends Controller
{
    public function index(Request $request){
        // Get page from $_GET
        $page = ( isset( $_GET['page'] ) && is_numeric( $_GET['page'] ) )? $_GET['page'] : 1;

        // Get limit on results to fetch
        $limit = \Config::get('pss.items_per_page', 15 );

        // Fetch media files
        $all_pages = Page::with('featureImage')
            ->where([[ 'title', 'LIKE', "%{$request->search}%" ]])
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        // Count the media files
        $total_pages = Page::count();
        $pages = (ceil($total_pages/$limit) > 1)? ceil($total_pages/$limit) : 1;

        // If we're on an invalid page number, redirect
        if( $page > $pages )
            return redirect()->action('Admin\PagesController@index', [ 'page' => $pages ] );

        // Index
        return view('admin.pages.index', [
            'page_title' => 'Pages',
            'pages' => $all_pages,
            'limit' => $limit,
        ]);
    }

    public function edit(Request $request, Page $page){
        return view('admin.pages.form', [
            'page' => $page,
            'page_title' => '<a href="' . action('Admin\PagesController@index') . '">Pages</a> <span class=\'fa fa-angle-right\'></span> Editing &quot;' . $page->title . '&quot;',
            'is_new' => false,
            'hide_search' => true,
        ]);
    }

    public function new(Request $request){
        
        $page = new Page;

        return view('admin.pages.form', [
            'page' => $page,
            'page_title' => '<a href="' . action('Admin\PagesController@index') . '">Pages</a> <span class=\'fa fa-angle-right\'></span> New Page',
            'is_new' => true,
            'hide_search' => true,
        ]);
    }

    public function update(Request $request, Page $page){

        // If doesn't exist, redirect back to index
        if( is_null( $page ) )
            return redirect()->action('Admin\PagesController@index');
        
        // Else, continue
        $this->processData($request, [
            'page.slug' => [
                'string', 'max:255', Rule::unique('pages', 'slug')->ignore($page->id)
            ]
        ]);

        // Update fields
        if( $page->update($request->page) )
        {
            // Set flash
            \Session::flash('success', "Updated page: {$page->title}");

            // Redirect back to index
            return redirect()->back();
        }
        else{
            // Set flash
            \Session::flash('warning', "Could not save the page, try again later");

            // Redirect back to index
            return redirect()->back();
        }

    }

    public function create(Request $request){

        // Validate data
        $this->processData($request, [
            'page.slug' => 'string|max:255|unique:pages,slug'
        ]);

        // If all good, save to DB
        $page = new Page($request->page);
        $page->save();

        // Set flash
        \Session::flash('success', "Created new page: {$page->title}");

        // Redirect back to index
        return redirect()->action('Admin\PagesController@index');

    }

    /**
    * Validates data for pages
    */
    private function processData($request, $custom_rules=[]){

        // Start validation
        return $this->validate($request, array_merge([
            'page.title' => 'required|max:255',
            'page.status' => [ 'numeric', 'regex:/[01]/' ],
            'page.feature_image_id' => 'numeric|exists:media,id',
            'page.parent_id' => 'numeric',
        ], $custom_rules));
    }

    public function delete(Request $request, Page $page){

        // If not exist, redirect to index
        if( is_null( $page ) )
            return redirect()->action('Admin\PagesController@index');

        // Delete it!
        $page_name = $page->title;
        $page->delete();

        // Delete flash message
        \Session::flash('deleted', "Deleted page: {$page_name}");

        // Redirect back to index
        return redirect()->action('Admin\PagesController@index');

    }
}
