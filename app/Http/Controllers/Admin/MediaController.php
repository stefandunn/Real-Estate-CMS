<?php

namespace App\Http\Controllers\Admin;

use App\MediaFile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MediaController extends Controller
{
    // Index view for library
    public function index(Request $request){

        // Get view type
        $view = ((isset($_GET['view']) && $_GET['view'] == 'list') || !isset($_GET['view']))? 'list' : 'grid';

        // Get page from $_GET
        $page = ( isset( $_GET['page'] ) && is_numeric( $_GET['page'] ) )? $_GET['page'] : 1;

        // Get limit on results to fetch
        $limit = \Config::get('pss.items_per_page', 15 );

        // Add AND WHERE condition for search results (eventually chained to mime_type if neccessary)
        $media_files = MediaFile::where(function ($query) use ($request){

            $query->orWhere(function ($query) use ($request){
                $query->where([[ 'caption', 'LIKE', "%{$request->search}%" ]]);
                $query->whereNotNull('caption');
            });

            $query->orWhere(function ($query) use ($request){
                $query->where([[ 'alt', 'LIKE', "%{$request->search}%" ]]);
                $query->whereNotNull('alt');
            });

            $query->orWhere(function ($query) use ($request){
                $query->where([[ 'title', 'LIKE', "%{$request->search}%" ]]);
                $query->whereNotNull('title');
            });
        });

        // If mime_type restriction specified, use it as additional AND where statement
        if( isset($_GET['mime_type']) && $_GET['mime_type'] != '*' )
            $media_files = $media_files->where([['mime_type', 'LIKE', $_GET['mime_type'] . '%' ]]);

        // Order and paginate the query
        $media_files = $media_files->orderBy('created_at', 'desc')
            ->paginate($limit);

        // dd( $media_files->toSql() );

        // Count the media files
        $total_media_files = MediaFile::count();
        $pages = (ceil($total_media_files/$limit) > 1)? ceil($total_media_files/$limit) : 1;

        // If we're on an invalid page number, redirect
        if( $page > $pages )
            return redirect()->action('Admin\MediaController@index', [ 'page' => $pages ] );

        // If AJAX request
        if($request->ajax())
            return $media_files;
        else
        {
        	return view('admin.library.index', [
        		'page_title' => 'Media Library',
                'media_files' => $media_files,
                'limit' => $limit,
                'view' => $view,
        	]);
        }
    }

    public function upload(Request $request){

    	// Validate input
    	$this->validate( $request, [
    		'uploaded_file' => [ 'max:' . \Config::get('pss.max_upload_size', 10000), 'required', 'file' ]
    	], [
    		'max' => 'File must be a maximum size of ' . \Config::get('pss.max_upload_size', 10000)/1000 . 'MBytes',
    	] );

    	// Change the filename
    	$file_info = pathinfo($request->uploaded_file->getClientOriginalName());
    	$new_filename = $file_info['filename'] . '-' . time() . "." . $file_info['extension'];

    	// Move to upload folder
    	$path = $request->uploaded_file->storeAs(date( 'Y' ) . '/' . strtolower( date( 'F' ) ), $new_filename , 'uploads');

        // Get image size
        $image_size = ( strstr($request->uploaded_file->getMimeType(), "image") )? getimagesize( public_path() . '/uploads/' . $path ) : [null, null];

    	// Create a new media file instance
    	$media_file = MediaFile::create([
    		'path' => '/uploads/' . $path,
    		'title' => $file_info['filename'],
    		'mime_type' => $request->uploaded_file->getMimeType(),
            'natural_width' => $image_size[0],
            'natural_height' => $image_size[1],
    	]);

    	// Save
    	if( !is_null( $media_file ) )
    	{
            // Create resized images
            $media_file->resizeImages();

    		return[
    			'media_file' => $media_file->attributesToArray(),
    			'edit_url' => action( 'Admin\MediaController@edit', ['media_file' => $media_file->id] ),
                'thumbnail_url' => $media_file->getThumbnailPath(),
    		];
    	}
    	else
    		return [ 'error' => "Could not save file to database" ];

    	return [
    		'path' => $path
    	];
    }

    public function edit( Request $request, MediaFile $media_file ){
        return view( 'admin.library.edit', [
            'media_file' => $media_file,
            'hide_search' => true,
            'page_title' => '<a href="' . action('Admin\MediaController@index') . '">Media Library</a> <span class=\'fa fa-angle-right\'></span> Editing &quot;' . $media_file->title . '&quot;'
        ] );
    }

    public function update(Request $request, MediaFile $media_file){
        
        // Update the media file
        if( $media_file->update( array_filter( $request->all()['media_file'] ) ) )
        {
            // Set flash message
            \Session::flash('success', 'Successfully saved');

            // Redierect back
            return redirect()->back();
        }

    }

    public function delete(Request $request, MediaFile $media_file){

        // Store title for flash message
        $media_title = $media_file->title;

        /* Check if can delete */

        //Check if associated with property feature image
        if(!is_null( $media_file->propertyUsingAsFeatureImage()->first() ))
        {
            $property = $media_file->propertyUsingAsFeatureImage()->first();
            \Session::flash('error', "Could not delete &quot;{$media_file->title}&quot; because it's being used as a feature image for property &quot;{$property->name}&quot;" );
            return redirect()->back();
        }

        // Check if associated with any property images
        if(!is_null( $media_file->propertiesUsingAsImageGallery()->count() > 0 )){

            // Get all properties
            $properties = $media_file->propertiesUsingAsImageGallery()->get();

            // If more than one property associated, prevent delete and redirect
            if( $properties->count() > 0 )
            {

                // Loop through properties and get name
                $property_list = "";
                foreach ($properties as $property)
                    $property_list .= "&quot;{$property->name}&quot;, ";
                $property_list = trim($property_list, ", ");

                // Set message
                \Session::flash('error', "Could not delete &quot;{$media_file->title}&quot; because it's being used on the following properties as part of the image gallery: {$property_list}");

                // Return to form
                return redirect()->back();

            }
        }

        // Delete document downloads
        $media_file->downloads()->delete();

        // Delete
        if( $media_file->delete() )
        {
            // Set flash
            \Session::flash('deleted', 'Deleted media file: ' . $media_title);

            // Redircet back to media library
            return redirect()->action('Admin\MediaController@index');
        }
    }

    /**
    * Bulk regenerate inages
    */
    public function bulkRegenerateImages(Request $request){

        // Get media IDs
        $media_ids = json_decode( $request->medias_file_ids );

        // If empty
        if( count( $media_ids ) == 0 )
            return [
                'error' => true,
                'messages' => "No media files were selected"
            ];
        else {
            // Loop through and regenerate
            foreach ($media_ids as $media_id) {

                // Get media file
                $media_file = MediaFile::find($media_id);

                // Ensure it exists
                if( is_null( $media_file ) )
                    return [
                        'error' => true,
                        'messages' => "Media file with ID: {$media_id} could not be found"
                    ];
                else
                    $media_file->resizeImages();
            }

            return[
                'success' => true
            ];
        }
    }

    /**
    * Bulk delete images
    */
    public function bulkDelete(Request $request){

        // Get media IDs
        $media_ids = json_decode( $request->medias_file_ids );

        // If empty
        if( count( $media_ids ) == 0 )
            return [
                'error' => true,
                'messages' => "No media files were selected"
            ];
        else
        {
            // Delete Media IDs
            $deleted_ids = [];

            // Loop through each one
            foreach ($media_ids as $media_id) {

                // Get media file
                $media_file = MediaFile::find($media_id);

                // Ensure it exists
                if( is_null( $media_file ) )
                    return [
                        'error' => true,
                        'messages' => "Media file with ID: {$media_id} could not be found",
                        'completed_deletes' => $deleted_ids,
                    ];
                else {

                    // The ID
                    $this_media_id = $media_file->id;

                    // New messages array to catch errors
                    $messages = [];

                    // Check to see if we can delete
                    if(!is_null( $media_file->propertyUsingAsFeatureImage()->first() ))
                    {
                        // Get property name and set error message in array
                        $property = $media_file->propertyUsingAsFeatureImage()->first();
                        $messages[] = "Could not delete &quot;{$media_file->title}&quot; because it's being used as a feature image for property &quot;{$property->name}&quot;";
                    }

                    // Check if associated with any property images
                    if(!is_null( $media_file->propertiesUsingAsImageGallery()->count() > 0 )){

                        // Get all properties
                        $properties = $media_file->propertiesUsingAsImageGallery()->get();

                        // If more than one property associated, prevent delete and redirect
                        if( $properties->count() > 0 )
                        {
                            // Loop through properties and get name
                            $property_list = "";
                            foreach ($properties as $property)
                                $property_list .= "&quot;{$property->name}&quot;, ";
                            $property_list = trim($property_list, ", ");

                            // Set message
                            $messages[] = "Could not delete &quot;{$media_file->title}&quot; because it's being used on the following properties as part of the image gallery: {$property_list}";

                        }
                    }

                    // If no error messages, that's good
                    if( empty( $messages ) )
                    {
                        // Delete document downloads
                        $media_file->downloads()->delete();

                        // Delete media file
                        $media_file->delete();

                        // Add to deleted IDs
                        $deleted_ids[] = $this_media_id;
                    }
                    else
                        return [
                            'error' => true,
                            'messages' => $messages,
                            'completed_deletes' => $deleted_ids
                        ];
                }

            }

            // Return success
            return [
                'success' => true,
                'completed_deletes' => $deleted_ids,
            ];
        }
    }

    /**
    * Regenerate a media file's response images and thumbnail
    */
    public function regenerateImages(Request $request, MediaFile $media_file){
        if( !is_null( $media_file ) )
        {
            // Resize images
            $media_file->resizeImages();

            // Set flash message
            \Session::flash('success', "Successfully regenerated responsive images and thumbnail for &quot;{$media_file->title}&quot;");

            return redirect()->back();
        }
        else
            return redirect()->action('Admin\MediaController@index');
    }
}
