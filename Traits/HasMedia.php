<?php

namespace App\Traits;

use App\Models\Media;

trait HasMedia
{
    public function medias()
    {
        return $this->morphMany(Media::class, 'belong');
    }

    public function avatar()
    {
        return  $this->belongsTo(Media::class);
    }

    public function storeMedia($media, $prefix = null)
    {
        return Media::up($media, $this, $prefix);
    }


    public function storeAvatar($media,$prefix=null)
    {
        if($prefix){
            if ($this->avatar()->wherePrefix($prefix)->first()) {
                $this->avatar()->wherePrefix($prefix)->first()->change($media);
            } else {
                $this->update([
                    'avatar_id' =>  $this->storeMedia($media,$prefix)->id,
                ]);
            }
        }else{
            if ($this->avatar) {
                $this->avatar->change($media);
            } else {
                $this->update([
                    'avatar_id' =>  $this->storeMedia($media)->id,
                ]);
            }
        }
    }

    
    public function storeMediaBase64($media, $prefix = null)
    {
        return Media::upBase64($media,'avatar', $this, $prefix);
    }


    public function storeAvatarBase64($media, $prefix = null)
    {
        if($prefix == 'mobile'){
            $this->update([
                'avatar_id' =>  $this->storeMediaBase64($media,$prefix)->id,
            ]);
        }else{
            $this->update([
                'avatar_desktop_id' =>  $this->storeMediaBase64($media,$prefix)->id,
            ]);
        }
        
    }


    public function dropMedias($prefix=null) 
    {
        foreach ($this->medias as $media) {
            if(!empty($prefix)){
                if($media->prefix == $prefix)
                    $media->delete();
            }else
                $media->delete();
        }
    }
}
