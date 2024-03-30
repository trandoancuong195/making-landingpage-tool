<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityWard extends Model
{
    use HasFactory;
    protected $table = 'city_ward';
    protected $guarded  = ['id'];

    public function cityDistrict()
    {
        return  $this->belongsTo(CityDistrict::class,'district_id','district_id');
    }
}
