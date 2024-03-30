<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityDistrict extends Model
{
    use HasFactory;
    protected $table = 'city_district';
    protected $guarded  = ['id'];

    public function cityWard()
    {
        return  $this->hasMany(CityWard::class,'district_id','district_id');
    }
    public function cityProvince()
    {
        return  $this->belongsTo(CityProvince::class,'city_id','city_id');
    }
}
