<?php
namespace App\Models\Advertise;

use Illuminate\Database\Eloquent\Model;

class AdvertiseKpi extends Model
{
    protected $table = 'zz_ad_country_tasks';

    /**
     * 广告活动
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function campaign(){
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }

    /**
     * 广告
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ad(){
        return $this->belongsTo(Ad::class, 'ad_id', 'id');
    }
}