<?php

namespace App\Models\Advertise;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bid extends Model
{
    use SoftDeletes;

    protected $table = 'a_bid';

    protected $fillable = ['type', 'amount', 'country_id'];

    public function bidding(){
        return $this->morphTo();
    }

    /**
     * 指定国家
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country(){
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
