<?php
namespace App\Models\Advertise;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Constraint\Count;

class Campaign extends Model
{
    use SoftDeletes;

    protected $table = 'campaign';

    protected $fillable = ['name', 'status', 'app_id', 'main_user_id'];

    /**
     * 构造Campaign
     * @param User $user
     * @param $params
     * @return mixed
     */
    public static function Make($user, $params){
        $campaign = DB::transaction(function () use($user, $params) {
            $main_user_id = $user->getMainId();
            if (empty($params['id'])) {
                $campaign = new self();
                $campaign->main_user_id = $main_user_id;
            } else {
                $campaign = self::query()->where([
                    'id' => $params['id'],
                    'main_user_id' => $main_user_id
                ])->firstOrFail();
            }
            $campaign->fill($params);
            $campaign->saveOrFail();
            if(empty($params['countries'])){
                $campaign->countries()->sync([]);
            }else{
                $country_id_list = is_array($params['countries']) ?
                    $params['countries'] : explode(',', $params['countries']);
                $countries = Country::query()
                    ->whereIn('id', $country_id_list)
                    ->pluck('id');
                $campaign->countries()->sync($countries);
            }

//            if(empty($params['refers'])){
//                $campaign->refers()->sync([]);
//            }else{
//                $refer_campaign_id_list = is_array($params['refers']) ?
//                    $params['refers'] : explode(',', $params['refers']);
//                $refers = Material::query()
//                    ->whereIn('id', $refer_campaign_id_list)
//                    ->where('type', $campaign->type)
//                    ->where('app_id', $campaign['app_id'])
//                    ->pluck('id');
//                $campaign->refers()->sync($refers);
//            }
//
//            if($campaign->type == Material::Material_Type_Video
//                && !empty($params['videos']) && is_array($params['videos'])) {
//                $campaign->updateVideos($user, $params['videos']);
//            }
//            if($campaign->type == Material::Material_Type_Text
//                && !empty($params['texts']) && is_array($params['texts'])) {
//                $campaign->updateTexts($user, $params['texts']);
//            }

            return $campaign;
        }, 3);
        return $campaign;
    }

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