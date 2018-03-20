<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use \App\Traits\NullSubmissionTrait;
    use \App\Traits\ActivityLogTrait;
    
    // Set table name
    protected $table = 'properties';

    // Allow mass assignment to all but ID
    protected $guarded = ['id'];

    // Get feature image
    public function featureImage(){
    	return $this->belongsTo('\App\MediaFile', 'feature_image_id');
    }

    // Get price type
    public function priceType(){
    	return $this->belongsTo('\App\PricingType', 'pricing_type_id');
    }

    // Get purchase type
    public function purchaseType(){
    	return $this->belongsTo('\App\PurchaseType', 'purchase_type_id');
    }

    // Get purchase type
    public function type(){
    	return $this->belongsTo('\App\PropertyType', 'property_type_id');
    }

    // Get file links
    public function fileLinks(){
        return $this->hasMany('App\PropertyFile', 'property_id');
    }

    // Get files
    public function files(){
        return $this->belongsToMany('App\MediaFile', 'property_files', 'property_id', 'file_id');
    }

    // Get images
    public function images(){
        return $this->belongsToMany('App\MediaFile', 'property_files', 'property_id', 'file_id')->where(['property_files.type' => 'image']);
    }

    // Get documents
    public function documents(){
        return $this->belongsToMany('App\MediaFile', 'property_files', 'property_id', 'file_id')->where(['property_files.type' => 'document']);
    }
}