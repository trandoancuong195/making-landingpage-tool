<?php

namespace App\Traits;

trait Belong
{
  public function belong()
  {
    return $this->morphTo();
  }

  public function setBelongAttribute($value)
  {
    $this->attributes['belong_type'] =  get_class($value);
    $this->attributes['belong_id']   = $value->id;
  }

  public function scopeManyOf($query,$belong){
      return  $query->whereBelongId($belong->id)->whereBelongType(get_class($belong));
  }
}
