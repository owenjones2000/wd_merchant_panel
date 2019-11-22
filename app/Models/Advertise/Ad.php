<?php
namespace App\Models\Advertise;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ad extends Model
{
    use SoftDeletes;

    protected $table = 'a_ad';

    protected $fillable = ['name', 'status', 'type_id', 'campaign_id'];

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

            if(isset($params['asset'])){
                $asset_id_list = array_column($params['asset'], 'type', 'id');
                $ad->assets()
                    ->whereNotIn('id', array_keys($asset_id_list))
                    ->where('ad_id', $ad['id'])
                    ->update([
                        'ad_id' => null
                    ]);
                Asset::query()
                    ->whereIn('id', array_keys($asset_id_list))
                    ->whereNull('ad_id')
                    ->update([
                        'ad_id' => $ad['id']
                    ]);
            }

            return $ad;
        }, 3);
        return $ad;
    }

    /**
     * 广告活动
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function campaign(){
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }

    /**
     * 素材
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assets(){
        return $this->hasMany(Asset::class, 'ad_id', 'id');
    }
}