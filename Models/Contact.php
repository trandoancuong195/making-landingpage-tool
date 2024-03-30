<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTree;

class Contact extends Model
{
    use HasFactory, HasTree;
    protected $guarded  = ['id'];

    public function city()
    {
        return  $this->belongsTo(LocationCity::class,'city_id');
    }

    public function category()
    {
        return  $this->belongsTo(Category::class);
    }

    public function scopeSearch($query, $search)
    {

        if (isset($search->name)) {
            $query->where('name', 'like', "%$search->name%");
        }
        if (isset($search->categories)) {
            $query->whereIn('category_id', $search->categories);
        }

        return $query;
    }

    public function scopeCategory($query, $category)
    {
        $arr  = $category->path();
        $arr[] = $category->id;
        $query->whereIn('category_id', $arr);
        return $query;
    }

    public function scopeSo($query)
    {
        return $query->orderByDesc('priority')->orderByDesc('created_at');
    }
}
