<?php

namespace App\Models\Advertise;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrackUrl extends Model
{
    use SoftDeletes;

    protected $table = 'a_track_url';

    protected $fillable = ['impression', 'click', 'country_id'];

    /**
     * 指定国家
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country(){
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
