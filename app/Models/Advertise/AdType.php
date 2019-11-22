<?php
namespace App\Models\Advertise;

class AdType
{
    const Video_Landscape_Short = 1;
    const Video_Landscape_Long = 2;
    const Video_Portrait_Short = 3;
    const Video_Portrait_Long = 4;

    public static function get($type_id){
        if(isset(self::$list[$type_id])) {
            return self::$list[$type_id];
        }
        return null;
    }

    public static $list = [
        self::Video_Landscape_Short => [
            'id' => self::Video_Landscape_Short,
            'name' => 'Landscape - Under 15s',
            'need_asset_type' => [AssetType::Landscape_Short, AssetType::Landscape_Interstitial_Image]
        ],
        self::Video_Landscape_Long => [
            'id' => self::Video_Landscape_Long,
            'name' => 'Landscape - Over 15s',
            'need_asset_type' => [AssetType::Landscape_Long, AssetType::Landscape_Interstitial_Image]
        ],
        self::Video_Portrait_Short => [
            'id' => self::Video_Portrait_Short,
            'name' => 'Portrait - Under 15s',
            'need_asset_type' => [AssetType::Portrait_Short, AssetType::Portrait_Interstitial_Image]
        ],
        self::Video_Portrait_Long => [
            'id' => self::Video_Portrait_Long,
            'name' => 'Portrait - Over 15s',
            'need_asset_type' => [AssetType::Portrait_Long, AssetType::Portrait_Interstitial_Image]
        ],
    ];
}