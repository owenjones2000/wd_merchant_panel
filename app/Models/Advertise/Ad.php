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
        if(!$this->status){
            if($this->is_upload_completed){
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
        foreach ($this['type']['need_asset_type'] as $need_asset_type_id){
            if(!$this['assets']->contains('type_id', $need_asset_type_id)){
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