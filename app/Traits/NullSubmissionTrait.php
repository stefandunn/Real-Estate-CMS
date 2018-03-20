<?php

namespace App\Traits;

/**
 * Class ActivityLogTrait
 *
 * @package App\Traits\ActivityLog
 */
trait NullSubmissionTrait
{
    public static function bootNullSubmissionTrait() {
        
        static::creating(function($model){
            foreach ($model->attributes as $key => $value) {
                $model->{$key} = (0 !== $value && "0" !== $value && empty($value)) ? null : $value;
            }
        });

        static::updating(function($model){
            foreach ($model->attributes as $key => $value) {
                $model->{$key} = (0 !== $value && "0" !== $value && empty($value)) ? null : $value;
            }
        });

    }
}