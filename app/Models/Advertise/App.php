<?php
namespace App\Models\Advertise;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class App extends Model
{
    use SoftDeletes;

    protected $table = 'a_app';

    protected $fillable = ['name', 'bundle_id', 'os', 'status', 'main_user_id'];
}