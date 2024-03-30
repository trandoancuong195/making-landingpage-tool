<?php

namespace App\Traits;

use App\Models\Summary;

trait HasTrack
{
    static function trackSum()
    {

        static::deleted(function ($model) {
            Summary::track($model, 'deleted');
        });

        static::updated(function ($model) {
            Summary::track($model, 'updated');
        });

        static::created(function ($model) {
            Summary::track($model, 'created');
        });
    }
}
