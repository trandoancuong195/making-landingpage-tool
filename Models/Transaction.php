<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasCode;

class Transaction extends Model
{

    use HasFactory,
        HasCode;

    protected $guarded  = ['id'];

    public function account()
    {
        return  $this->belongsTo(User::class);
    }

    public function closed($prefix = null)
    {
        if ($this->status != 'closed') {
            $closed =  $this->replicate()->fill([
                'parent_id' => $this->id,
                'status'    => 'closed',
                'prefix'    => $prefix ? $prefix : $this->prefix
            ]);
            $closed->save();
            return $closed;
        }
    }

    public static function in(User $user, $total, $note = null, $state = 'closed', $prefix = null)
    {
        return static::create(
            [
                'prefix'    =>  $prefix,
                'in'    =>  $total,
                'note'  =>  $note,
                'account_id'    => $user->id,
                'status'    =>  $state
            ]
        );
    }

    public static function out(User $user, $total, $note = null, $state = 'closed', $prefix = null)
    {
        return static::create(
            [
                'prefix'    =>  $prefix,
                'out'    =>  $total,
                'note'  =>  $note,
                'account_id'    => $user->id,
                'status'    =>  $state
            ]
        );
    }

    public static function total(User $user)
    {
        $in = static::sum($user, 'in');
        $out = static::sum($user, 'out');
        return ($in - $out);
    }

    public static function sum(User $user, $col = 'in')
    {
        return static::whereAccountId($user->id)->whereStatus('closed')->sum($col);
    }
    static function boot()
    {
        parent::boot();
        static::uniqCode();
    }
}
