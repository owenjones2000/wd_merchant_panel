<?php
namespace App\Models\Advertise;

use App\Scopes\TenantScope;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class App extends Model
{
    use SoftDeletes;

    protected $table = 'a_app';
    protected $appends = ['track'];

    protected $fillable = ['name', 'bundle_id', 'os', 'track_platform_id', 'track_code', 'status', 'main_user_id'];

    /**
     * 构造Campaign
     * @param User $user
     * @param $params
     * @return mixed
     */
    public static function Make($user, $params){
        $apps = DB::transaction(function () use($user, $params) {
            $main_user_id = $user->getMainId();
            if (empty($params['id'])) {
                $apps = new self();
                $apps->main_user_id = $main_user_id;
            } else {
                $apps = self::query()->where([
                    'id' => $params['id'],
                    'main_user_id' => $main_user_id
                ])->firstOrFail();
            }
            $apps->fill($params);
            $apps->saveOrFail();

            return $apps;
        }, 3);
        return $apps;
    }
    
    public function getTrackAttribute(){
        return TrackPlatform::get($this['track_platform_id']);
    }

    /**
     *  模型的 「启动」 方法.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new TenantScope(Auth::user()->getMainId()));
    }
}