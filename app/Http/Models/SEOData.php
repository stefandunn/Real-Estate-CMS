<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SEOData extends Model
{
    use \App\Traits\NullSubmissionTrait;

    // Set table name
    protected $table = 'seo_data';

    // Protect all but ID
    protected $guarded = ['id'];

    // Get a setting's value via key
    public static function getValue($key, $fallback = null){
    	
    	// Get settings record via key
    	$setting = self::where( [ 'url' => \Request::path() ] )->first();

    	// If found, return value, otherwise return fallback
    	if(!empty($setting->{$key}))
    		return $setting->{$key};
		
		// Else return fallback
    	else
			return $fallback;
    }
}
