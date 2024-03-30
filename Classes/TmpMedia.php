<?php

namespace App\Classes;

use App\Models\Media;

class TmpMedia
{
    static $name =  '__tmpmedia';


    static function prefix()
    {
        return Journey::current()->tracking->code;
    }
    public static function get()
    {
        return Media::wherePrefix(static::prefix())->get();
    }

    public static function up($media)
    {
        return Media::up($media, null, static::prefix());
    }

    public static function move($model, $prefix = null)
    {
      
        return Media::wherePrefix(static::prefix())->update([
            'belong_type' => get_class($model),
            'belong_id' =>  $model->id,
            'prefix'    =>  $prefix
        ]);
    }

    public static function clear()
    {
        return Media::wherePrefix(static::prefix())->delete();
    }
}
