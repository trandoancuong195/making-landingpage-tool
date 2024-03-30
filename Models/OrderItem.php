<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded = ['id'];

    public function product()
    {
        return  $this->belongsTo(Product::class);
    }

    public function order()
    {
        return  $this->belongsTo(Order::class);
    }

    public static function sell(Order $order,Product $product,$data = []){
        $sold = $product->sell();
       
        $data = array_merge($data, [
            'order_id'   =>  $order->id,
            'product_id' =>  $sold->id,
        ]);
      
        return static::create($data);
    }
}
