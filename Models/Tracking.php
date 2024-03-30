<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TrackingPoint;

class Tracking extends Model
{
    use HasFactory;
    protected $guarded  = ['id'];

    public function points()
    {
        return $this->hasMany(TrackingPoint::class);
    }

}
