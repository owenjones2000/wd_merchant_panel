<?php
namespace App\Models\Advertise;

use App\Traits\MultiTable;
use Illuminate\Database\Eloquent\Model;

class AdvertiseKpi extends Model
{
    use MultiTable;

    protected $table = 'z_sub_tasks';

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