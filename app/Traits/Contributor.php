<?php
namespace App\Traits;

trait Contributor {
    public static function bootContributor() {
        
        static::creating(function ($model) {
            if(!$model->isDirty('created_by')) {
                if(auth()->user()) {
                    $model->created_by = auth()->user()->id;
                } else {
                    $model->created_by = 1;
                }
            }
            if(!$model->isDirty('created_at')) {
                $model->created_at = now();
            }
            $model->updated_at = NULL;
        });

        static::updating(function ($model) {
            
            if(!$model->isDirty('updated_by')) {
                if(auth()->user()) {
                    $model->updated_by = auth()->user()->id;
                } else {
                    $model->updated_by = 1;
                }
            }
            //TODO updated_at is auto fill
        });
    }
}
?>