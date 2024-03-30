<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Traits\Belong;
use Illuminate\Support\Facades\Auth;

class Media extends Model
{
    use HasFactory, Belong;
    protected $guarded = ['id'];

    static function disk()
    {
        return Storage::disk('local');
    }

    public function change($media)
    {
        $name =  uniqid() . '.' . $media->getClientOriginalExtension();
        $path =  $media->storeAs('media', $name);
        if ($path) {
            static::disk()->delete($this->name);
            $this->update([
                'name'  => $name,
                'path'  => $path
            ]);
        }
    }

    public static function upBase64($base64,$folder, $model = null, $prefix = null)
    {
        list($extension, $content) = explode(';', $base64);
        preg_match('/.([0-9]+) /', microtime(), $m);
        $fileName =  uniqid() . '.png';
        $content = explode(',', $content)[1];
        $storage = static::disk();
        $checkDirectory = $storage->exists($folder);
        if (!$checkDirectory) {
            $storage->makeDirectory($folder);
        }
        $storage->put($folder.'/'.$fileName, base64_decode($content));
        $data    =  [
            'name'         =>  $fileName,
            'path'         =>  $folder.'/'.$fileName,
            'prefix'       =>  $prefix,
        ];
        if($model){
            $data =  array_merge($data,[
                'belong_type'  =>  get_class($model),
                'belong_id'    =>  $model->id,
            ]);
        }
        return static::create($data);
    }

    public static function up($media, $model = null, $prefix = null)
    {
        $name =  uniqid() . '.' . $media->getClientOriginalExtension();
        $path =  $media->storeAs('media', $name);
        if ($path) {
            $data    =  [
                'name'         =>  $name,
                'path'         =>  $path,
                'prefix'       =>  $prefix,
                'created_by'   => Auth::id() ?? 0
            ];
            if($model){
                $data =  array_merge($data,[
                    'belong_type'  =>  get_class($model),
                    'belong_id'    =>  $model->id,
                ]);
            }
            return static::create($data);
        }
    }
    static function boot()
    {
        parent::boot();
        static::deleting(function ($model) {
            static::disk()->delete($model->path);
        });
    }
}
