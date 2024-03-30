<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CityDistrict;

class CityProvince extends Model
{
    use HasFactory;
    protected $table = 'city_province';
    protected $guarded  = ['id'];

    public function cityDistrict()
    {
        return  $this->hasMany(CityDistrict::class,'city_id','city_id');
    }
}
