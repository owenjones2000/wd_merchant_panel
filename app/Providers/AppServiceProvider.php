<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //左侧菜单
        view()->composer('layout.layout',function($view){
            $menus = \App\Models\Permission::with([
                'childs'=>function($query){$query->with('icon')->orderBy('sort','asc');}
                ,'icon'])->where('parent_id',0)->orderBy('sort','desc')->get();
//            $unreadMessage = \App\Models\Message::where('read',1)->where('accept_uuid',auth()->user()->uuid)->count();
            $view->with('menus',$menus);
//            $view->with('unreadMessage',$unreadMessage);
        });

        DB::listen(function ($query) {
             Log::info($query->sql);
            // $query->bindings
            // $query->time
        });
    }
}
