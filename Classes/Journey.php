<?php

namespace App\Classes;

use App\Models\Tracking;
use App\Models\TrackingPoint;
use Jenssegers\Agent\Agent;

class Journey
{
    protected static $name =  '__journey';

    public static function start()
    {
        if (empty(session(static::$name))) {
            $agent =  new Agent();
            static::update([
                'tracking'  => Tracking::create([
                    'code' => uniqid('track_'),
                    'ip'   => request()->ip(),
                    'platform'  => $agent->platform(),
                    'browser'   => $agent->browser(),
                    'device'    => $agent->device(),
                    'robot'     => $agent->robot()
                ]),
                'agent'     => $agent
            ]);
        }
    }
    static function update($data = [])
    {
        session()->put(static::$name, $data);
        session()->save();
    }
    public static function current()
    {
        return (object)session(static::$name);
    }

    public static function change($data)
    {

        $current = session(static::$name);
        static::update(array_merge($current, $data));
    }

    public static function tracking()
    {
        return static::current()->tracking;
    }

    public static function url()
    {
        return url()->current();;
    }


    public static function prefix($prefix = null)
    {
        return TrackingPoint::whereTrackingId(static::tracking()->id)
            ->whereUrl(static::url())
            ->wherePrefix($prefix);
    }

    public static function sign($data)
    {
        return TrackingPoint::create(
            array_merge([
                'tracking_id' => static::tracking()->id,
                'url'   =>  static::url()
            ], $data)
        );
    }

}
