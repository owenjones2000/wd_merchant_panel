<?php

namespace App\Models\Advertise;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyBudget extends Model
{
    use SoftDeletes;

    protected $table = 'daily_budget';

    protected $fillable = ['value', 'country_id'];

    public function budgeting(){
        return $this->morphTo();
    }
}
