<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App;
use \URL;

use App\Property;
use App\Page;
use App\MediaFile;

class SitemapController extends Controller
{
    // Public index
    public function index(Request $request)
    {
    	// Create new sitemap object
	    $sitemap = App::make("sitemap");

	    // add sitemaps (loc, lastmod (optional))
	    $sitemap->addSitemap(URL::action('SitemapController@renderSitemap', [ 'name' => 'pages' ]));
	    $sitemap->addSitemap(URL::action('SitemapController@renderSitemap', [ 'name' => 'properties' ]));
	    $sitemap->addSitemap(URL::action('SitemapController@renderSitemap', [ 'name' => 'media' ]));

	    return $sitemap->render('sitemapindex');
	}

	public function renderSitemap(Request $request, $name, $render_type = 'xml'){
		return $this->$name($request, $render_type);
	}

	private function pages(Request $request, $render_type = 'xml'){
		
		// Create new sitemap object
	    $sitemap = App::make("sitemap");

	    // Add items to the sitemap (url, date, priority, freq)
	    foreach (Page::all() as $page)
	        $sitemap->add( URL::to('/') . '/' . $page->slug, $page->updated_at, 10, 1000, [], $page->title);

	    // Render your sitemap
	    return $sitemap->render( $render_type );
	}

	private function properties(Request $request, $render_type = 'xml'){
		
		// Create new sitemap object
	    $sitemap = App::make("sitemap");

	    // Add items to the sitemap (url, date, priority, freq)
	    foreach (Property::all() as $property)
	        $sitemap->add( URL::action('PropertiesController@show', ['id' => $property->id ]), $property->updated_at, 10, 1000);

	    // Render your sitemap
	    return $sitemap->render( $render_type );
	}

	private function media(Request $request, $render_type = 'xml'){
		// Create new sitemap object
	    $sitemap = App::make("sitemap");

	    foreach (MediaFile::all() as $media_file)
	        $sitemap->add( URL::to('/') . $media_file->path, $media_file->updated_at, 10, 1000 );

	    // Render your sitemap
	    return $sitemap->render( $render_type );
	}
}
