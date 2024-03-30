<?php

namespace App\Models;

use App\Traits\HasCode;
use App\Traits\HasLink;
use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasParent;

class Post extends  Model
{
  use HasFactory,
    HasParent,
    HasCode,
    HasMedia,
    HasLink;


  protected $guarded  = ['id'];
  //protected $touches  = ['avatar'];
  //protected $slug     = 'name';

  public function category()
  {
    return  $this->belongsTo(Category::class);
  }

  public function scopeSearch($query, $search)
  {

    if (isset($search->name)) {
      $query->where('name', 'like', "%$search->name%");
    }
    if (isset($search->code)) {
      $query->where('code', '=', $search->code);
    }
    if (isset($search->cate_id)) {
      $query->where('category_id', '=', $search->cate_id);
    }
    if (!empty($search->is) && $search->is != 'post') {
      $query->whereCategoryId(0);
    } else {
      $query->where('category_id', '<>', 0);
      if (isset($search->categories)) {
        $query->whereIn('category_id', $search->categories);
      }
    }

    return $query;
  }

  public function scopeCategory($query, $category)
  {
    $arr  = $category->path();
    $arr[] = $category->id;
    $query->whereIn('category_id', $arr);
    return $query;
  }

  public function scopeSo($query)
  {
    return $query->orderByDesc('priority')->orderByDesc('created_at');
  }

  static function boot()
  {
    parent::boot();
    static::uniqCode();
    static::deleting(function ($model) {
      $model->dropMedias();
    });
  }
}
