<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;

class Order extends Model
{
    protected $guarded = ['id'];

    //static
    public function customer()
    {
        return  $this->belongsTo(Contact::class);
    }

    public function ref()
    {
        return  $this->belongsTo(User::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query;
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function item()
    {
        return $this->hasOne(OrderItem::class);
    }

    static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->code))
                $model->code = uniqid();
        });

      
    }
}
