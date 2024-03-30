<?php

namespace App\Classes;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class Cart
{
  protected static $name =  '__ShoppingCart';
  public static function add($id, $info = [])
  {

    $cart = static::cart();
    $quantity = isset($info['quantity']) ? $info['quantity'] : 1;

    $key  = '_' . $id;
    if (array_key_exists($key, $cart)) {
      $cart[$key]->quantity   += $quantity;
    } else {
      $product  = Product::find($id);
      $i  = new \stdClass();
      $i->quantity = $quantity;
      $i->item     = $product;
      $i->price    = $product->price;
      $cart[$key]  = $i;
    }
    if (isset($info['option'])) {
      $cart[$key]->options = $info['option'];
    }
    static::update($cart);
    return static::cart();
  }


  public static function update($items)
  {
    session([static::$name => $items]);
  }

  public static function count()
  {
    return count(static::cart());
  }

  public static function total()
  {
    $total = 0;
    foreach (static::cart() as $item) {
      $total +=  $item->price * $item->quantity;
    }
    return $total;
  }

  static function cal()
  {
    $total = 0;
    $commission = 0;
   
    foreach (static::cart() as $i) {
 
      $total +=  $i->price * $i->quantity;
      $commission += $i->item->dropship_bonus ? $total * $i->item->dropship_bonus / 100 : 0;
    }
    return ['total' => $total, 'ref_commission' => $commission];
  }


  public static function remove($id)
  {
    $items = static::cart();
    unset($items[$id]);
    static::update($items);
  }

  public static function clear()
  {
    session()->forget(static::$name);
  }

  public static function cart()
  {
    return session()->has(static::$name) ?  session(static::$name) : [];
  }

  public static function submit($info = [])
  {
    if (!static::count()) return;

    $info   = array_merge($info, static::cal());
    $order  = Order::create($info);

    foreach (Cart::cart() as $i) {
      OrderItem::sell($order, $i->item,  [
        'quantity' =>  $i->quantity,
        'price'    =>  $i->price,
        'appends'  =>  isset($i->appends) ? isset($i->appends) : null
      ]);
    }

    static::clear();
    return $order;
  }
}
