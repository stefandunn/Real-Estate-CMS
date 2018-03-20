<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Navigation extends Model
{
    // Set table name
    protected $table = 'navigation';

    // Guard ID
    protected $guarded = ['id'];

    // Get parent
    public function parent()
    {
        return $this->belongsTo('App\Navigation', 'parent_id');
    }

    // Get children
    public function children()
    {
        return $this->hasMany('App\Navigation', 'parent_id');
    }

}
