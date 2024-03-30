<?php

namespace App\Models;

use App\Traits\Belong;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Summary extends Model
{
    use HasFactory, Belong;

   
    public static function track($model, $action)
    {
        if($model->hasAttributes('prefix')){
            $sum = $model::wherePrefix($model->prefix)->count();
        }else{
            $sum = $model::count(); 
        }

        return static::create([
            'belong_type' =>  get_class($model),
            'belong_id'   =>  $model->id,
            'prefix'      =>  $model->prefix,
            'action'      =>  $action,  
            'sum'         =>  $sum  
        ]);
    }
}
