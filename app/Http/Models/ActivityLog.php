<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    // Guard ID
    protected $guarded = ['id'];


    // Convert changes into array
    public function changesToArray(){

    	// Attempt to json decode
    	$changes_arr = json_decode($this->after, true);

    	// If no json error
    	if( json_last_error() == JSON_ERROR_NONE )
    		return $changes_arr;
    	else
    		return [];
    }

    // Convert original into array
    public function originalToArray(){

    	// Attempt to json decode
    	$changes_arr = json_decode($this->before, true);

    	// If no json error
    	if( json_last_error() == JSON_ERROR_NONE )
    		return $changes_arr;
    	else
    		return [];
    }


    public function user(){
    	return $this->belongsTo('App\User');
    }
}
