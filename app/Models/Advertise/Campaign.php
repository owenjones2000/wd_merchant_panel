<?php
namespace App\Models\Advertise;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
            if(empty($params['regions'])){
                $campaign->regions()->sync([]);
            }else{
                $region_code_list = is_array($params['regions']) ?
                    $params['regions'] : explode(',', $params['regions']);
                $regions = Region::query()
                    ->whereIn('code', $region_code_list)
                    ->pluck('code');
                $campaign->regions()->sync($regions);
            }

            if(isset($params['track_by_region']) && isset($params['track'])){
                $campaign->trackUrls()->delete();
                if($params['track_by_region']){
                    $track_list = [];
                    foreach($params['track'] as $track_info){
                        if(!empty($track_info['region_code'])
                            && (!empty($track_info['impression']) || !empty($track_info['click']))) {
                            $track_list[] = new TrackUrl([
                                'impression' => $track_info['impression'],
                                'click' => $track_info['click'],
                                'country' => $track_info['region_code'],
                            ]);
                        }
                    }
                    $campaign->trackUrls()->saveMany($track_list);
                }else{
                    if(!empty($track[0]['impression']) || !empty($track[0]['click'])) {
                        $campaign->trackUrls()->save(new TrackUrl([
                            'impression' => $params['track'][0]['impression'] ?? '',
                            'click' => $params['track'][0]['click'] ?? '',
                            'country' => 'ALL',
                        ]));
                    }
                }
            }

            if(isset($params['budget_by_region']) && isset($params['budget'])){
                $campaign->budgets()->delete();
                if($params['budget_by_region']){
                    $budget_list = [];
                    foreach($params['budget'] as $budget_info){
                        if(!empty($budget_info['region_code']) && !empty($budget_info['amount'])) {
                            $budget_list[] = new CampaignBudget([
                                'amount' => $budget_info['amount'],
                                'country' => $budget_info['region_code'],
                            ]);
                        }
                    }
                    $campaign->budgets()->saveMany($budget_list);
                }else{
                    if(empty($params['budget'][0]['amount'])) {
                        $campaign->budgets()->save(new CampaignBudget([
                            'amount' => $params['budget'][0]['amount'] ?? 0,
                            'country' => 'ALL',
                        ]));
                    }
                }
            }

            if(isset($params['bid_by_region']) && isset($params['bid'])){
                $campaign->bids()->delete();
                if($params['bid_by_region']){
                    $bid_list = [];
                    foreach($params['bid'] as $bid_info){
                        if(!empty($bid_info['region_code']) && !empty($bid_info['amount'])) {
                            $bid_list[] = new CampaignBid([
                                'amount' => $bid_info['amount'],
                                'country' => $bid_info['region_code'],
                            ]);
                        }
                    }
                    $campaign->bids()->saveMany($bid_list);
                }else{
                    if(empty($params['bid'][0]['amount'])){
                        $campaign->bids()->save(new CampaignBid([
                            'amount' => $params['bid'][0]['amount'] ?? 0,
                            'country' => 'ALL',
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
    public function regions(){
        return $this->belongsToMany(Region::class, 'a_campaign_country',
            'campaign_id','country', 'id', 'code');
    }

    /**
     * 三方跟踪链接
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trackUrls(){
        return $this->hasMany(TrackUrl::class, 'campaign_id', 'id');
    }

    /**
     * 预算
     */
    public function budgets(){
        return $this->hasMany(CampaignBudget::class, 'campaign_id', 'id');
    }

    /**
     * 出价
     *
     */
    public function bids(){
        return $this->hasMany(CampaignBid::class, 'campaign_id', 'id');
    }
}