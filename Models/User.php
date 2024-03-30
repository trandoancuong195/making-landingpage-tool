<?php

namespace App\Models;

use App\Traits\HasLink;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasParent;


class User extends Authenticatable
{
  use HasFactory, Notifiable,HasParent,HasLink;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name',
    'email',
    'password',
    'is_admin',
    'has_role',
    'phone',
    'login_at',
    'firebase_id',
    'firebase_token',
    'firebase_by',
    'referral_id',
    'referral_code'
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  public function getRoleAttribute()
  {
    if ($this->is_admin) {
      return empty($this->has_role) ? 'admin' :  $this->has_role;
    }
    return 'guest';
  }

  public function transactions()
  {
    return $this->hasMany(Transaction::class);
  }

  public function contact(){
    return $this->hasOne(Contact::class,'account_id');
  }

  public function wallet()
  {
    return Transaction::total($this);
  }

  public function withdraw($total,$note = null)
  {
    return Transaction::out($this, $total,$note ,'pending-withdraw');
  }

  public function income($total,$note,$prefix = null)
  {
    return Transaction::in($this, $total,$note,'closed', $prefix);
  }
  public function medias()
    {
        return $this->morphMany(Media::class, 'belong');
    }
}
