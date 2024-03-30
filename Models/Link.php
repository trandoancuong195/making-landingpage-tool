<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\Belong;

class Link extends Model
{
  use HasFactory, Belong;

  protected $guarded  = ['id'];

  static function slug($str)
  {
    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
    $str = preg_replace("/(đ)/", "d", $str);
    $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
    $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
    $str = preg_replace("/(Đ)/", "D", $str);
    return  Str::slug(str_replace("&*#39;", "", $str), '-');
  }

  public function updateConversionCode($params = [])
  {
    if (!empty($params['facebook_id'])) {
      $this->track_facebook = $params['facebook_id'];
    }
    if (!empty($params['google_analytics_id'])) {
      $this->track_google_analytics = $params['google_analytics_id'];
    }
    if (!empty($params['tiktok_id'])) {
      $this->track_tiktok = $params['tiktok_id'];
    }
    if (!empty($params['google_ads_id'])) {
      $this->track_google_ads = $params['google_ads_id'];
    }
    $this->save();
  }
  public static function publish($slug, $model, $prefix = null, $uid = null)
  {
    return static::create([
      'pretty'      =>  $slug,
      'belong_type' =>  get_class($model),
      'belong_id'   =>  $model->id,
      'auth_id'     =>  $uid,
      'prefix'      =>  $prefix
    ]);
  }

  public function points()
  {
    return $this->hasMany(TrackingPoint::class);
  }

  public function orders()
  {
    return $this->hasMany(Order::class);
  }

  static function boot()
  {
    parent::boot();
    static::creating(function ($model) {
      $model->short  = uniqid();
      // if (static::wherePretty($model->pretty)->exists())
      //   $model->pretty .= '.' . $model->short;
    });
  }
}
