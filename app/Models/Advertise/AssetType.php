<?php
namespace App\Models\Advertise;

class AssetType
{
    const Landscape_Short = 1;
    const Landscape_Long = 2;
    const Portrait_Short = 3;
    const Portrait_Long = 4;
    const Landscape_Interstitial_Image = 5;
    const Portrait_Interstitial_Image = 6;
    const Html = 7;

    public static function get($asset_type_id){
        return self::$list[$asset_type_id];
    }

    public static $list = [
        self::Landscape_Short => [
            'name' => 'Landscape Short Video'
        ],
        self::Landscape_Long => [
            'name' => 'Landscape Long Video'
        ],
        self::Portrait_Short => [
            'name' => 'Portrait Short Video'
        ],
        self::Portrait_Long => [
            'name' => 'Portrait Long Video'
        ],
        self::Landscape_Interstitial_Image => [
            'name' => 'Landscape Interstitial Image'
        ],
        self::Portrait_Interstitial_Image => [
            'name' => 'Portrait Interstitial Image'
        ],
        self::Html => [
            'name' => 'HTML File'
        ],
    ];
}