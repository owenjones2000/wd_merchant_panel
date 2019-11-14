<?php
namespace App\Models\Advertise;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class App extends Model
{
    use SoftDeletes;

    protected $table = 'app';

    protected $fillable = ['name', 'bundle_id', 'os', 'status'];
}