<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyDocument extends Model
{
    /**
    * Get document (via file_id)
    */
    public function document(){
    	return $this->belongsTo('\App\MediaFile', 'file_id');
    }
    /**
    * Get Property (via property_id)
    */
    public function property(){
    	return $this->belongsTo('\App\Property');
    }
}
