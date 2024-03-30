<?php

namespace App\Models;

use App\Traits\HasLink;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Category;
use App\Models\Media;
use App\Traits\HasMedia;

class LandingPage extends Model
{
    use HasFactory,SoftDeletes,HasLink,HasMedia;
    protected $table = 'landingpages';
    protected $guarded  = ['id'];

    public function category()
    {
        return  $this->belongsTo(Category::class);
    }

    public function avatarMobile()
    {
        return  $this->belongsTo(Media::class,'avatar_id');
    }


    public function avatarDesktop()
    {
        return  $this->belongsTo(Media::class,'avatar_desktop_id');
    }

    public function scopeSearch($query, $search)
    {
        if (isset($search->id)) {
            $query->where('id', '=', $search->id);
        }
        if (isset($search->token)) {
            $query->where('token', '=', $search->token);
        }
        if (isset($search->created_by)) {
            $query->where('created_by', '=', $search->created_by);
        }
        if (isset($search->prefix)) {
            $query->where('prefix', '=', $search->prefix);
        }
        if (isset($search->device)) {
            $query->where('device', '=', $search->device);
        }
        if (isset($search->prefix)) {
            $query->wherePrefix($search->prefix);
        }
        if (isset($search->name)) {
            $query->where('name', 'like', "%$search->name%");
        }
        if (isset($search->category_id)) {
            $query->whereCategoryId($search->category_id);
        }
        if(!empty($search->status)){
            $query->where('status', '=', $search->status);
        }
        if(!empty($search->enddate) && !empty($search->startdate)){
            $from = date($search->startdate);
            $to = date('Y-m-d',strtotime($search->enddate. ' + 1 days'));
            $query->whereBetween('created_at', [$from, $to]);
        }

        return $query;
    }
    public function scopeSort($query, $search)
    {
        //filter
        //sort
        if(!empty($search->sort) && !empty($search->sort_by) && $search->sort_by !== 0){
            switch ($search->sort) {
                case 'time':
                    # code...
                    $query->orderBy('created_at',$search->sort_by);
                    break;
                // case 'total_view':
                //     # code...
                //     $query->orderBy('total_view',$search->sort_by);
                //     break;
                // case 'total_click':
                //     # code...
                //     $query->orderBy('total_click',$search->sort_by);
                //     break;
                // case 'total_convert':
                //     # code...
                //     $query->orderBy('total_convert',$search->sort_by);
                //     break;

                default:
                    # code...
                    break;
            }

        }

        return $query;
    }
    public function dropmedia($device='mobilde')
    {
        $this->dropMedias($device);

    }
    public function user_create(){
        return  $this->belongsTo(User::class,'created_by');
    }
    public function user_update(){
        return  $this->belongsTo(User::class,'updated_by');
    }


}
