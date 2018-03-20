<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PropertyFile extends Model
{
    // Protect all but ID
    protected $guarded = ['id'];

    /**
    * Get image (via image_id)
    */
    public function image(){
    	return $this->belongsTo('\App\MediaFile', 'image_id');
    }
    /**
    * Get Property (via property_id)
    */
    public function property(){
    	return $this->belongsTo('\App\Property');
    }
}
