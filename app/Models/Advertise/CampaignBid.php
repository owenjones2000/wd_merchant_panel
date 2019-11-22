<?php

namespace App\Models\Advertise;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignBid extends Model
{
    use SoftDeletes;

    protected $table = 'a_campaign_bid';

    protected $fillable = ['type', 'amount', 'country_code'];

    /**
     * 指定国家
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country(){
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }
}
