<?php

namespace App\Traits;

trait HasCode
{
  static function uniqCode()
  {
    static::saving(function ($model) {
      if (empty($model->code)) {
        $model->code  = empty($model->prefix) ?  uniqid() : uniqid($model->prefix . "_");
      } else  if ($model->isDirty('code')) {
        if (static::whereCode($model->code)->exists())
          $model->code .= uniqid();
      }
    });
  }

  /*
  public static function findCode($code)
  {
    return static::whereCode($code)->first();
  }
  */
}
