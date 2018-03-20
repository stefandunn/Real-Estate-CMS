<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Intervention\Image\ImageManager as Image;
use Tinify\Tinify;

class MediaFile extends Model
{
    use \App\Traits\ActivityLogTrait;
    
    // Set table name
    protected $table = 'media';

    // Allow mass assignment to all but ID
    protected $guarded = [ 'id' ];

    /*
    * Creates a tag for an media file object
    */
    public function toTag($size='desktop', $attributes=[]){
    	
    	// If media file is an image
    	if( strstr($this->mime_type, "image") || $size == 'thumbnail' )
    		return \Html::image( $this->pathToURL($size), $this->alt, $attributes );
    	else
    		return "";
    }

    /*
	 * Make a URL for the image based on path
	 */
    public function pathToURL($size='desktop'){
        // If requesting thumbnail, use method $this->getThumbnailPath() as doucments require special thumbnails
        if($size == 'thumbnail')
            return $this->getThumbnailPath();
    	return \URL::to('/') . $this->{"{$size}_path"};
    }

    /*
    * Make a HTML image tag with a media file's ID
    */
    public static function tagFromID($id, $size='desktop', $attributes=[]){

    	// Attempt to get image from DB
    	$image = self::find($id);

    	// If found and is an image, return tag
    	if( !empty( $image ) )
    		return $image->toTag($size, $attributes);
    	// Else, return nothing
    	else
    		return "";
    }

    /*
     * Resizes images to suit other devices to reduce the filesize of served images for different devices
    */
    public function resizeImages($device=null){

        // IGNORE IF NOT AN IMAGE OR IF A APNG (animated grahics)
        if(!strstr($this->mime_type, "image/") || in_array($this->mime_type, ['image/apng']))
            return true;

        // Get API keys from
        \Tinify\setKey(\Config::get('pss.tinypng_api_key', '-Xz2u0IsPgjMV7Ddexad-ojYOv6hcfSR'));

        // Defaults for resize dimensions, can be overritten in config/pss-settings.php
        $resize_dimensions = [
            'desktop'   => [1440, 1440],
            'tablet'    => [960, 960],
            'mobile'    => [640, 640],
            'thumbnail' => [128, 128],
        ];

        // Override merge
        $resize_dimensions = array_merge($resize_dimensions, \Config::get('pss.max_resize_dimensions', []));

        // Path info
        $abs_path = public_path() . $this->path;
        $pathinfo = pathinfo( $abs_path );
        $extension = $pathinfo['extension'];
        $file_name = $pathinfo['filename'];
        $path = $pathinfo['dirname'];

        // Instance of Intervention image resizer
        $image_resizer = (new Image(['driver' => 'imagick']))->make($abs_path);

        // Detect the orientation of the image
        $orientation = ( $image_resizer->width() > $image_resizer->height() )? 'landscape' : 'portrait';

        // Resize for devices, if function arguments left blank, defaults to NULL and all resizes are performed.
        foreach (['desktop', 'tablet', 'mobile'] as $device_name) {
            if ($device == $device_name || is_null($device))
            {
                // Create new path
                ${"new_{$device_name}_path"} = "{$path}/{$file_name}-{$device_name}.{$extension}";

                /* Resize based on orientation for aspect-ratio constraints */
                // If orientation is portrait, constrain the aspect-ration to height
                if( $orientation == 'portrait' )
                    $resized_image = $image_resizer->resize( null,  $resize_dimensions[$device_name][1], function ($constraint){
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                // If orientation is landscape, constrain the aspect-ratio to width
                else
                    $resized_image = $image_resizer->resize( $resize_dimensions[$device_name][0], null, function ($constraint){
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                // Save image
                $resized_image->save(${"new_{$device_name}_path"}, \Config::get('pss.image_save_quality', 80));

                // Use Tinify to shrink the image
                if(!in_array($this->mime_type, ['image/gif', 'image/apng']))
                    try{
                        \Tinify\fromFile(${"new_{$device_name}_path"})->toFile(${"new_{$device_name}_path"});
                    } catch( \Tinify\AccountException $e){
                        logWarning( "Quota reached for TinyPNG API" );
                    }
            }
        }

        // If GIF, use Intervention Image library
        if(in_array($this->mime_type, ['image/gif']))
        {

            // Create thumbnail path
            $thumbnail_path = "{$path}/{$file_name}-thumbnail.{$extension}";

            /* Resize based on orientation for aspect-ratio constraints */
            // If orientation is portrait, constrain the aspect-ration to height
            if( $orientation == 'portrait' )
                $resized_thumbnail_image = $image_resizer->resize( null,  $resize_dimensions['thumbnail'][1], function ($constraint){
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

            // If orientation is landscape, constrain the aspect-ratio to width
            else
                $resized_thumbnail_image = $image_resizer->resize( $resize_dimensions['thumbnail'][0], null, function ($constraint){
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

            // Create cropped image using thumbnail dimensions
            $resized_thumbnail_image->crop($resize_dimensions['thumbnail'][0], $resize_dimensions['thumbnail'][1])->save($thumbnail_path);
        }
        else
        {
            // Create thumbnail path
            $thumbnail_path = "{$path}/{$file_name}-thumbnail.{$extension}";

            /* Resize source for thumbnail */
            // If orientation is portrait, constrain the aspect-ration to height
            if( $orientation == 'portrait' )
                $resized_thumbnail_image = $image_resizer->resize( null,  $resize_dimensions['mobile'][1], function ($constraint){
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

            // If orientation is landscape, constrain the aspect-ratio to width
            else
                $resized_thumbnail_image = $image_resizer->resize( $resize_dimensions['mobile'][0], null, function ($constraint){
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

            // Temporarily save to thumbnail path
            $resized_thumbnail_image->save($thumbnail_path);

            // // Create thumbnail via Tinify (more intelligent) and overrite the temporary file
            try{
                \Tinify\fromFile($thumbnail_path)->resize([
                    'method' => 'cover',
                    'width' => $resize_dimensions['thumbnail'][0],
                    'height' => $resize_dimensions['thumbnail'][1],
                ])->toFile($thumbnail_path);
            } catch (\Tinify\AccountException $e){
                logWarning( "Quota reached for TinyPNG API" );

                // Create thumbnail traditionally
                (new Image(['driver' => 'imagick']))->make($thumbnail_path)->crop($resize_dimensions['thumbnail'][0], $resize_dimensions['thumbnail'][1])->save($thumbnail_path);
            }
        }


        // Free memory
        $resized_image->destroy();


        // Update this model's instance with new paths
        $this->update([
            'desktop_path' => ( isset( $new_desktop_path ) )? str_replace( public_path(), "", $new_desktop_path ) : null,
            'tablet_path' => ( isset( $new_tablet_path ) )? str_replace( public_path(), "", $new_tablet_path ) : null,
            'mobile_path' => ( isset( $new_mobile_path ) )? str_replace( public_path(), "", $new_mobile_path ) : null,
            'thumbnail_path' => ( isset( $thumbnail_path ) )? str_replace( public_path(), "", $thumbnail_path ) : null,
        ]);

        // Return the path
        return true;
    }

    /*
     * Get thumbnail image
    */
    public function getThumbnailPath(){

        // Check if thumbnail path exists on this model instance from DB
        if( !is_null( $this->thumbnail_path ) && file_exists( public_path() . $this->thumbnail_path ) )
            return $this->thumbnail_path;
        else
        {
            // Get extension
            $extension = pathinfo($this->path)['extension'];

            // Check if icon found for extension
            if( file_exists( public_path() . "/images/theme/icons/document-types/{$extension}.png" ) )
                return "/images/theme/icons/document-types/{$extension}.png";
            else
                return "/images/theme/icons/document-types/generic.png";
        }
    }

    /**
    * Get downloads for document (via document_id)
    */
    public function downloads(){
        return $this->hasMany('\App\DocumentDownload', 'document_id');
    }

    /**
    * Feature image of property (via feature_image_id)
    */
    public function propertyUsingAsFeatureImage(){
        return $this->hasOne('\App\Property', 'feature_image_id');
    }

    /**
    * Get properties that have this image associated with it
    */
    public function propertiesUsingAsImageGallery(){
        return $this->belongsToMany('App\Property', 'property_images', 'image_id', 'property_id');
    }
}
