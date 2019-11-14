<?php
namespace App\Models\Advertise;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use SoftDeletes;

    protected $table = 'campaign';

    protected $fillable = ['name', 'status', 'app_id', 'main_user_id'];

    public function app(){
        return $this->belongsTo(App::class, 'app_id', 'id');
    }
}