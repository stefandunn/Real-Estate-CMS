<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentDownload extends Model
{
    /**
    * Get the document
    */
    public function document()
    {
    	return $this->belongsTo('\App\MediaFile', 'document_id');
    }
}
