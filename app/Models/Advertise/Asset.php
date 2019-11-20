<?php
namespace App\Models\Advertise;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes;

    protected $table = 'a_asset';

    protected $fillable = ['file_path', 'type_id', 'width', 'height', 'duration', 'ad_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ad(){
        return $this->belongsTo(Ad::class, 'ad_id', 'id');
    }
}