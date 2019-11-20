<?php

namespace App\Models\Advertise;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyBudget extends Model
{
    use SoftDeletes;

    protected $table = 'a_daily_budget';

    protected $fillable = ['amount', 'country_id'];

    public function budgeting(){
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
