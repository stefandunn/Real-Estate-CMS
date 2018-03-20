<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    // Set table name
    protected $table = 'settings';

    // Guard the ID, allow mass assignment of the other parts
    protected $guarded = ['id'];

    // Get a setting's value via key
    public static function getValue($key, $fallback = null){
    	
    	// Get settings record via key
    	$setting = self::where( ['key' => $key] )->first();

    	// If found, return value, otherwise return fallback
    	if(!is_null($setting))
    		return $setting->value;
    	else
    	{
    		// Log a warning to say the setting was not found
    		logWarning( "Setting with key, '{$key}' was requested but not found" );

    		// Return fallback
    		return $fallback;
    	}
    }
}
