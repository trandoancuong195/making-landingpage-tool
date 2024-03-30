<?php

namespace App\Models;

use App\Models\Post;
use App\Models\User;
use App\Traits\HasLink;
use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Post
{
  use HasFactory,SoftDeletes,HasLink,HasMedia;
  public function getPriceFormatAttribute()
  {
    return number_format($this->price);
  }
  public function getPriceDiscountFormatAttribute()
  {
    return number_format($this->price);
  }

  public function getSummaryAttribute(){
    return empty($this->description) ? Str::limit(strip_tags($this->content), 240 ) : $this->description;
  }

  public function supplier()
  {
    return  $this->belongsTo(Contact::class);
  }
  public function customer()
  {
    return  $this->belongsTo(User::class,'auth_id');
  }

  public function recommend()
  {
    return static::root()->whereCategoryId($this->category_id)->where('id', '<>', $this->id)->inventory();
  }
  public function scopeSearch($query, $search)
  {
    if (isset($search->name)) {
      $query->where('name', 'like', "%$search->name%");
    }
    if (isset($search->auth_id)) {
      $query->where('auth_id', '=', $search->auth_id);
    }
    if (isset($search->categories)) {
      $query->whereIn('category_id', $search->categories);
    }
    return $query;
  }

  public function ext()
  {
    $ext =  $this->replicate()->fill([
      'parent_id' => $this->id,
    ]);
    return $ext;
  }

  public function sell()
  {
    $sold =  $this->replicate()->fill([
      'parent_id' => $this->id,
      'sold_at'   => now(),
      'prefix'    => 'sold'
    ]);
    $sold->save();
    return $sold;

  }


  public function scopeSold($query)
  {
    return  $query->whereNotNull('sold_at');
  }

  public function scopeInventory($query)
  {
    return  $query->whereNull('sold_at');
  }

}
