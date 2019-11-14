<?php

namespace App\Models\Advertise;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bid extends Model
{
    use SoftDeletes;

    protected $table = 'bid';

    protected $fillable = ['type', 'value', 'country_id'];

    public function bidding(){
        return $this->morphTo();
    }
}
