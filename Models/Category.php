<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTree;
use App\Traits\HasMedia;
use App\Models\LandingPage;

class Category extends Model
{
  use HasFactory, HasTree, HasMedia;
  protected $guarded  = ['id'];
  protected $touches  = ['avatar'];
  protected $slug     = 'name';


  public function landingpages(){

    return $this->hasMany(LandingPage::class);

  }
  public function posts(){

    return $this->hasMany(Post::class);

  }

  public function users()
  {
    # code...
    return $this->belongsTo(User::class);
  }

  public function parentCategory()
  {
    # code...
    return $this->belongsTo(Category::class,'parent_id');
  }

  public static function findName($name)
  {
    return Category::whereName($name)->first();
  }

  public static function scopePull($query, $code, $prefix = null)
  {
    return  $query->where('code', 'like', "$code%")->wherePrefix($prefix);
  }
  public function scopeSearch($query, $search)
  {
    if (isset($search->name)) {
      $query->where('name', 'like', "%$search->name%");
    }
    if(!empty($search->categories)){
      $query->whereIn('id', $search->categories);
    }
    return $query;
  }

  public function scopeSo($query)
  {
    return $query->orderByDesc('priority')->orderByDesc('created_at');
  }

  public function extensions(){
    return Category::select('prefix')->distinct()->whereParentId($this->id)->get();
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
