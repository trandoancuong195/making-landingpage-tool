<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasParent;

class TrackingPoint extends Model
{
    use HasFactory, HasParent;
    protected $guarded  = ['id'];

    public function link(){
        return $this->belongsTo(Link::class);
    }
}
