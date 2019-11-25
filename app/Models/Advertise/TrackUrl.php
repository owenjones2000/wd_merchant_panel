<?php

namespace App\Models\Advertise;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrackUrl extends Model
{
    use SoftDeletes;

    protected $table = 'a_campaign_track_url';

    protected $fillable = ['impression', 'click', 'region_id'];

    /**
     * 指定国家
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function region(){
        return $this->belongsTo(Region::class, 'country', 'id');
    }
}
