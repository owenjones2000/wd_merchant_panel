<?php
namespace App\Models\Advertise;

use Illuminate\Database\Eloquent\Model;

class AdvertiseKpi extends Model
{
    protected $table = 'zz_ad_country_tasks';

    public function campaign(){
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }
}