<?php
namespace App\Models\Advertise;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\Constraint\Count;

class Campaign extends Model
{
    use SoftDeletes;

    protected $table = 'campaign';

    protected $fillable = ['name', 'status', 'app_id', 'main_user_id'];

    public function app(){
        return $this->belongsTo(App::class, 'app_id', 'id');
    }

    public function countries(){
        return $this->belongsToMany(Country::class, 'campaign_country',
            'campaign_id','country_id');
    }

    public function bids(){
        return $this->morphMany(Bid::class, 'bidding');
    }

    public function dailyBudgets(){
        return $this->morphMany(DailyBudget::class, 'budgeting');
    }
}