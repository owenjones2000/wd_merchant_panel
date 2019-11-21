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

    protected $table = 'a_campaign';

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

            if(isset($params['track_by_country']) && isset($params['track'])){
                $campaign->trackUrls()->delete();
                if($params['track_by_country']){
                    $track_list = [];
                    foreach($params['track'] as $track){
                        if(!empty($track['country'])
                            && (!empty($track['impression']) || !empty($track['click']))) {
                            $track_list[] = new TrackUrl([
                                'impression' => $track['impression'],
                                'click' => $track['click'],
                                'country_id' => $track['country'],
                            ]);
                        }
                    }
                    $campaign->trackUrls()->saveMany($track_list);
                }else{
                    if(!empty($track['impression']) || !empty($track['click'])) {
                        $campaign->trackUrls()->save(new TrackUrl([
                            'impression' => $params['track'][0]['impression'] ?? '',
                            'click' => $params['track'][0]['click'] ?? '',
                            'country_id' => 0,
                        ]));
                    }
                }
            }

            if(isset($params['budget_by_country']) && isset($params['budget'])){
                $campaign->dailyBudgets()->delete();
                if($params['budget_by_country']){
                    $budget_list = [];
                    foreach($params['budget'] as $budget){
                        if(!empty($budget['country']) && !empty($budget['amount'])) {
                            $budget_list[] = new DailyBudget([
                                'amount' => $budget['amount'],
                                'country_id' => $budget['country'],
                            ]);
                        }
                    }
                    $campaign->dailyBudgets()->saveMany($budget_list);
                }else{
                    if(empty($params['bid'][0]['amount'])) {
                        $campaign->dailyBudgets()->save(new DailyBudget([
                            'amount' => $params['budget'][0]['amount'] ?? 0,
                            'country_id' => 0,
                        ]));
                    }
                }
            }

            if(isset($params['bid_by_country']) && isset($params['bid'])){
                $campaign->bids()->delete();
                if($params['bid_by_country']){
                    $bid_list = [];
                    foreach($params['bid'] as $bid){
                        if(!empty($bid['country']) && !empty($bid['amount'])) {
                            $bid_list[] = new Bid([
                                'amount' => $bid['amount'],
                                'country_id' => $bid['country'],
                            ]);
                        }
                    }
                    $campaign->bids()->saveMany($bid_list);
                }else{
                    if(empty($params['bid'][0]['amount'])){
                        $campaign->bids()->save(new Bid([
                            'amount' => $params['bid'][0]['amount'] ?? 0,
                            'country_id' => 0,
                        ]));
                    }
                }
            }

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
        return $this->belongsToMany(Country::class, 'a_campaign_country',
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