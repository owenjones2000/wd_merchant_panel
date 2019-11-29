<?php
namespace App\Models\Advertise;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ad extends Model
{
    use SoftDeletes;

    protected $table = 'a_ad';

    protected $fillable = ['name', 'status', 'type_id', 'campaign_id'];

    protected $appends = ['type'];

    /**
     * 启用
     * @throws \Throwable
     */
    public function enable(){
        if(!$this->status){
            $this->status = true;
            $this->saveOrFail();
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
     * 素材
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assets(){
        return $this->hasMany(Asset::class, 'ad_id', 'id');
    }
}