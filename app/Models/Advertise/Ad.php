<?php
namespace App\Models\Advertise;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ad extends Model
{
    use SoftDeletes;

    protected $table = 'a_ad';

    protected $fillable = ['name', 'type_id', 'campaign_id'];

    protected $appends = ['type', 'is_upload_completed'];

    /**
     * 启用
     * @throws \Throwable
     */
    public function enable(){
        if($this->is_admin_disable){
            throw new \Exception('This ad has been disabled by the administrator.');
        }
        if(!$this->status){
            if($this->is_upload_completed){
                $this->is_cold = true;
                $this->status = true;
                $this->saveOrFail();
            } else {
                throw new \Exception('Lack of assets.');
            }
        }
    }

    /**
     * 停用
     * @throws \Throwable
     */
    public function disable(){
        if($this->status){
            $this->status = false;
            $this->saveOrFail();
        }
    }

    /**
     * 广告活动
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function campaign(){
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }

    /**
     * 投放国家
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function regions(){
        return $this->belongsToMany(Region::class, 'a_ad_country',
            'ad_id','country', 'id', 'code');
    }

    /**
     * 广告类型
     * @return AdType
     */
    public function getTypeAttribute(){
        return AdType::get($this->type_id);
    }

    /**
     * 素材是否满足
     * @return bool
     */
    public function getIsUploadCompletedAttribute(){
        if (isset($this['type']['need_asset_type']) && is_array($this['type']['need_asset_type'])) {
            foreach ($this['type']['need_asset_type'] as $need_asset_type) {
                if (is_array($need_asset_type)) {
                    foreach ($need_asset_type as $need_asset_type_item) {
                        if($this['assets']->contains('type_id', $need_asset_type_item)){
                            continue 2;
                        }
                    }
                } else {
                    if($this['assets']->contains('type_id', $need_asset_type)){
                        continue ;
                    }
                }
                return false;
            }
        }
        return true;
    }

    /**
     * 素材
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assets(){
        return $this->hasMany(Asset::class, 'ad_id', 'id');
    }
}
