<?php

namespace App\Models\Advertise;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrackUrl extends Model
{
    use SoftDeletes;

    protected $table = 'track_url';

    protected $fillable = ['impression', 'click', 'country_id'];

}
