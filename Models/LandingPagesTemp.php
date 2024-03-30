<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandingPagesTemp extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'landingpages_temp';
    protected $guarded  = ['id'];

    public function scopeSearch($query, $search)
    {
        if (isset($search->token)) {
            $query->where('token', '=', $search->token);
        }
        if (isset($search->landingpage_id)) {
            $query->where('landingpage_id', '=', $search->landingpage_id);
        }
        if (isset($search->user_id)) {
            $query->where('user_id', '=', $search->user_id);
        }
        if (isset($search->device)) {
            $query->where('device', '=', $search->device);
        }

        return $query;
    }
}
