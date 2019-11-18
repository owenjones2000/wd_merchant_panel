<?php
namespace App\Models\Advertise;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ad extends Model
{
    use SoftDeletes;

    protected $table = 'ad';

    protected $fillable = ['name', 'status', 'campaign_id'];

    /**
     * 构造Ad
     * @param User $user
     * @param $params
     * @return mixed
     */
    public static function Make($user, $params){
        $ad = \DB::transaction(function () use($user, $params) {
            if (empty($params['id'])) {
                $ad = new self();
                $ad['campaign_id'] = $params['campaign_id'];
            } else {
                $ad = self::query()->where([
                    'id' => $params['id'],
                ])->firstOrFail();
            }
            $ad->fill($params);
            $ad->saveOrFail();
//            if(empty($params['countries'])){
//                $ad->countries()->sync([]);
//            }else{
//                $country_id_list = is_array($params['countries']) ?
//                    $params['countries'] : explode(',', $params['countries']);
//                $countries = Country::query()
//                    ->whereIn('id', $country_id_list)
//                    ->pluck('id');
//                $ad->countries()->sync($countries);
//            }
//
//            if(isset($params['track_by_country']) && isset($params['track'])){
//                $ad->trackUrls()->delete();
//                if($params['track_by_country']){
//                    $track_list = [];
//                    foreach($params['track'] as $track){
//                        if(!empty($track['country'])
//                            && (!empty($track['impression']) || !empty($track['click']))) {
//                            $track_list[] = new TrackUrl([
//                                'impression' => $track['impression'],
//                                'click' => $track['click'],
//                                'country_id' => $track['country'],
//                            ]);
//                        }
//                    }
//                    $ad->trackUrls()->saveMany($track_list);
//                }else{
//                    if(!empty($track['impression']) || !empty($track['click'])) {
//                        $ad->trackUrls()->save(new TrackUrl([
//                            'impression' => $params['track'][0]['impression'] ?? '',
//                            'click' => $params['track'][0]['click'] ?? '',
//                            'country_id' => 0,
//                        ]));
//                    }
//                }
//            }
//
//            if(isset($params['budget_by_country']) && isset($params['budget'])){
//                $ad->dailyBudgets()->delete();
//                if($params['budget_by_country']){
//                    $budget_list = [];
//                    foreach($params['budget'] as $budget){
//                        if(!empty($budget['country']) && !empty($budget['amount'])) {
//                            $budget_list[] = new DailyBudget([
//                                'amount' => $budget['amount'],
//                                'country_id' => $budget['country'],
//                            ]);
//                        }
//                    }
//                    $ad->dailyBudgets()->saveMany($budget_list);
//                }else{
//                    if(empty($params['bid'][0]['amount'])) {
//                        $ad->dailyBudgets()->save(new DailyBudget([
//                            'amount' => $params['budget'][0]['amount'] ?? 0,
//                            'country_id' => 0,
//                        ]));
//                    }
//                }
//            }
//
//            if(isset($params['bid_by_country']) && isset($params['bid'])){
//                $ad->bids()->delete();
//                if($params['bid_by_country']){
//                    $bid_list = [];
//                    foreach($params['bid'] as $bid){
//                        if(!empty($bid['country']) && !empty($bid['amount'])) {
//                            $bid_list[] = new Bid([
//                                'amount' => $bid['amount'],
//                                'country_id' => $bid['country'],
//                            ]);
//                        }
//                    }
//                    $ad->bids()->saveMany($bid_list);
//                }else{
//                    if(empty($params['bid'][0]['amount'])){
//                        $ad->bids()->save(new Bid([
//                            'amount' => $params['bid'][0]['amount'] ?? 0,
//                            'country_id' => 0,
//                        ]));
//                    }
//                }
//            }

            return $ad;
        }, 3);
        return $ad;
    }
}