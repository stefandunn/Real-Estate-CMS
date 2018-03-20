<?php

namespace App\Traits;

use Doctrine\Common\Inflector\Inflector;

/**
 * Class ActivityLogTrait
 *
 * @package App\Traits\ActivityLog
 */
trait ActivityLogTrait
{
    public static function bootActivityLogTrait() {

        static::created(function ($model) {
            \App\ActivityLog::create([
                'user_id'   => \Auth::id(),
                'record_id' => $model->id,
                'before'    => '',
                'after'     => json_encode($model->getDirty()),
                'action'    => 'create',
                'model'     => self::convertModelToSingular(get_class($model))
            ]);
        });

        static::updating(function ($model) {
            \App\ActivityLog::create([
                'user_id'   => \Auth::id(),
                'record_id' => $model->id,
                'before'    => json_encode($model->getOriginal()),
                'after'     => json_encode($model->getDirty()),
                'action'    => 'update',
                'model'     => self::convertModelToSingular(get_class($model))
            ]);
        });

        static::deleting(function ($model) {
            \App\ActivityLog::create([
                'user_id'   => \Auth::id(),
                'record_id' => $model->id,
                'before'    => json_encode($model->getOriginal()),
                'after'     => json_encode(array_filter(array_diff_key(['deleted_at'=>null], $model->getDirty()))),
                'action'    => 'delete',
                'model'     => self::convertModelToSingular(get_class($model))
            ]);
        });
    }

    public static function convertModelToSingular($model_class)
    {
        return trim(preg_replace_callback("/App\\\\([A-Z]{1,}[a-z]+)/", function( $matches ){
            
            // Get match
            return ( $matches[1] == 'Media')? $matches[1] . ' ' : Inflector::singularize($matches[1]) . ' ';

        }, $model_class ));
    }
}