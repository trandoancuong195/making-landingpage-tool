<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSchedule extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function create_user()
    {
        return $this->belongsTo(User::class,'created_by');
    }
    public function update_user()
    {
        return $this->belongsTo(User::class,'updated_by');
    }
}
