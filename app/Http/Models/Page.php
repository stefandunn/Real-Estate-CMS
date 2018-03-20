<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
	use \App\Traits\NullSubmissionTrait;

    // Guard ID
    protected $guarded = ['id'];

    // Get parent
    public function parent()
    {
        return $this->belongsTo('App\Page', 'parent_id');
    }

    // Get children
    public function children()
    {
        return $this->hasMany('App\Page', 'parent_id');
    }

    // Get feature image
    public function featureImage(){
    	return $this->belongsTo('\App\MediaFile', 'feature_image_id');
    }
}
