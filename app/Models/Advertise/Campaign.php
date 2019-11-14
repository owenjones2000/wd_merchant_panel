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

    /**
     * 所属应用
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function app(){
        return $this->belongsTo(App::class, 'app_id', 'id');
    }

    /**
     * 投放国家
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function countries(){
        return $this->belongsToMany(Country::class, 'campaign_country',
            'campaign_id','country_id');
    }

    /**
     * 三方跟踪链接
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trackUrls(){
        return $this->hasMany(TrackUrl::class, 'campaign_id', 'id');
    }

    /**
     * 日预算
     */
    public function dailyBudgets(){
        return $this->morphMany(DailyBudget::class, 'budgeting');
    }

    /**
     * 出价
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function bids(){
        return $this->morphMany(Bid::class, 'bidding');
    }
}