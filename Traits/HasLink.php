<?php

namespace App\Traits;

use App\Models\Link;

trait HasLink
{

    function link()
    {
        return $this->morphOne(Link::class, 'belong');
    }

    function dropLink()
    {
        if($this->link) $this->link->delete();
    }

    public function pushlish($slug,$prefix=null,$uid = null)
    {
        return Link::publish($slug,$this,$prefix,$uid);
    }
}
