<?php
namespace App\Models\Advertise;

class TrackPlatform
{
    const AppsFlyer = 1;
    const Adjust = 2;

    public static function get($type_id){
        if(isset(self::$list[$type_id])) {
            return self::$list[$type_id];
        }
        return null;
    }

    public static $list = [
        self::AppsFlyer => [
            'id' => self::AppsFlyer,
            'name' => 'AppsFlyer',
        ],
        self::Adjust => [
            'id' => self::Adjust,
            'name' => 'Adjust',
        ],
    ];
}