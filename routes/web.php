<?php
/*
|--------------------------------------------------------------------------
| Home Routes
|--------------------------------------------------------------------------
|
*/
Route::group(['namespace'=>'Home'],function (){
    //登录、注销
    Route::get('login','LoginController@showLoginForm')->name('home.loginForm');
    Route::post('login','LoginController@login')->name('home.login');
    Route::get('logout','LoginController@logout')->name('home.logout');

});


/*
|--------------------------------------------------------------------------
| Home Routes
|--------------------------------------------------------------------------
|
| 后台需要授权的路由 homes
|
*/
Route::group(['namespace'=>'Home','middleware'=>'auth'],function (){
    //后台布局
    Route::get('/','IndexController@layout')->name('home.layout');
    //后台首页
    Route::get('/index','IndexController@index')->name('home.index');
    Route::get('/index1','IndexController@index1')->name('home.index1');
    Route::get('/index2','IndexController@index2')->name('home.index2');
    //图标
    Route::get('icons','IndexController@icons')->name('home.icons');
});

//系统管理
Route::group(['namespace'=>'Home','prefix'=>'home','middleware'=>['auth','operation.log','permission:system.manage']],function (){
    //数据表格接口
    Route::get('data','IndexController@data')->name('home.data')->middleware('permission:system.role|system.user|system.permission');

    //用户管理
    Route::group(['middleware'=>['permission:system.user']],function (){
        Route::get('user','UserController@index')->name('home.user');
        //添加
        Route::get('user/create','UserController@create')->name('home.user.create')->middleware('permission:system.user.create');
        Route::post('user/store','UserController@store')->name('home.user.store')->middleware('permission:system.user.create');

        //编辑
        Route::get('user/{id}/edit','UserController@edit')->name('home.user.edit')->middleware('permission:system.user.edit');
        Route::post('user/{id}/update','UserController@update')->name('home.user.update')->middleware('permission:system.user.edit');
        //删除
        Route::delete('user/destroy','UserController@destroy')->name('home.user.destroy')->middleware('permission:system.user.destroy');

        //分配角色
//        Route::get('user/{id}/role','UserController@role')->name('home.user.role')->middleware('permission:system.user.role');
//        Route::put('user/{id}/assignRole','UserController@assignRole')->name('home.user.assignRole')->middleware('permission:system.user.role');
        //分配权限
        Route::get('user/{id}/permission','UserController@permission')->name('home.user.permission')->middleware('permission:system.user.permission');
        Route::put('user/{id}/assignPermission','UserController@assignPermission')->name('home.user.assignPermission')->middleware('permission:system.user.permission');
    });

    //角色管理
//    Route::group(['middleware'=>'permission:system.role'],function (){
//        Route::get('role','RoleController@index')->name('home.role');
//        //添加
//        Route::get('role/create','RoleController@create')->name('home.role.create')->middleware('permission:system.role.create');
//        Route::post('role/store','RoleController@store')->name('home.role.store')->middleware('permission:system.role.create');
//        //编辑
//        Route::get('role/{id}/edit','RoleController@edit')->name('home.role.edit')->middleware('permission:system.role.edit');
//        Route::put('role/{id}/update','RoleController@update')->name('home.role.update')->middleware('permission:system.role.edit');
//        //删除
//        Route::delete('role/destroy','RoleController@destroy')->name('home.role.destroy')->middleware('permission:system.role.destroy');
//        //分配权限
//        Route::get('role/{id}/permission','RoleController@permission')->name('home.role.permission')->middleware('permission:system.role.permission');
//        Route::put('role/{id}/assignPermission','RoleController@assignPermission')->name('home.role.assignPermission')->middleware('permission:system.role.permission');
//    });
    //权限管理
//    Route::group(['middleware'=>'permission:system.permission'],function (){
//        Route::get('permission','PermissionController@index')->name('home.permission');
//        //添加
//        Route::get('permission/create','PermissionController@create')->name('home.permission.create')->middleware('permission:system.permission.create');
//        Route::post('permission/store','PermissionController@store')->name('home.permission.store')->middleware('permission:system.permission.create');
//        //编辑
//        Route::get('permission/{id}/edit','PermissionController@edit')->name('home.permission.edit')->middleware('permission:system.permission.edit');
//        Route::put('permission/{id}/update','PermissionController@update')->name('home.permission.update')->middleware('permission:system.permission.edit');
//        //删除
//        Route::delete('permission/destroy','PermissionController@destroy')->name('home.permission.destroy')->middleware('permission:system.permission.destroy');
//    });
    //登录日志管理
//    Route::group(['middleware' => 'permission:system.login_log'], function () {
//        Route::get('login_log', 'LoginLogController@index')->name('home.login_log');
//        Route::delete('login_log/destroy', 'LoginLogController@destroy')->name('home.login_log.destroy')->middleware('permission:system.login_log.destroy');
//    });
    //操作日志管理
//    Route::group(['middleware' => 'permission:system.operation_log'], function () {
//        Route::get('operation_log', 'OperationLogController@index')->name('home.operation_log');
//        Route::delete('operation_log/destroy', 'OperationLogController@destroy')->name('home.operation_log.destroy')->middleware('permission:system.operation_log.destroy');
//    });

});

//投放管理
Route::group(['namespace'=>'Advertise','prefix'=>'advertise','middleware'=>['auth','operation.log','permission:advertise.manage']],function (){

    // 控制台
    Route::group(['prefix'=>'dashboard', 'middleware' => 'permission:advertise.manage'], function () {
        Route::get('data', 'DashBoardController@data')->name('advertise.dashboard.data');
        Route::get('overview', 'DashBoardController@view')->name('advertise.dashboard.view');
    });

    // 应用管理
    Route::group(['prefix'=>'app', 'middleware' => 'permission:advertise.app'], function () {
        Route::get('data', 'AppController@data')->name('advertise.app.data');
        Route::get('list', 'AppController@index')->name('advertise.app');
        //编辑
        Route::get('{id?}', 'AppController@edit')->name('advertise.app.edit')->middleware('permission:advertise.app.edit')
            ->where('id', '\d+');
        Route::post('{id?}', 'AppController@save')->name('advertise.app.save')->middleware('permission:advertise.app.edit')
            ->where('id', '\d+');
        Route::post('{id}/enable', 'AppController@enable')->name('advertise.app.enable')->middleware('permission:advertise.app.edit');
        Route::post('{id}/disable', 'AppController@disable')->name('advertise.app.disable')->middleware('permission:advertise.app.edit');
        Route::post('icon', 'AppController@uplodeIcon')->name('advertise.app.icon')->middleware('permission:advertise.app.edit');

        Route::post('{app_id}/channel/{channel_id}/enable', 'ChannelController@enable')->name('advertise.campaign.channel.enable')->middleware('permission:advertise.campaign.edit');
        Route::post('{app_id}/channel/{channel_id}/disable', 'ChannelController@disable')->name('advertise.campaign.channel.disable')->middleware('permission:advertise.campaign.edit');


        //删除
//        Route::delete('destroy', 'AppController@destroy')->name('advertise.app.destroy')->middleware('permission:advertise.app.destroy');
    });

    // 活动管理
    Route::group(['prefix'=>'campaign', 'middleware' => 'permission:advertise.campaign'], function () {
        Route::get('data', 'CampaignController@data')->name('advertise.campaign.data');
        Route::get('list', 'CampaignController@list')->name('advertise.campaign');
        //编辑
        Route::get('{id?}', 'CampaignController@edit')->name('advertise.campaign.edit')->middleware('permission:advertise.campaign.edit');
        Route::post('{id?}', 'CampaignController@save')->name('advertise.campaign.save')->middleware('permission:advertise.campaign.edit');
        Route::post('{id}/enable', 'CampaignController@enable')->name('advertise.campaign.enable')->middleware('permission:advertise.campaign.edit');
        Route::post('{id}/disable', 'CampaignController@disable')->name('advertise.campaign.disable')->middleware('permission:advertise.campaign.edit');
        //删除
//        Route::delete('destroy', 'CampaignController@destroy')->name('advertise.campaign.destroy')->middleware('permission:advertise.campaign.destroy');

        // 广告
        Route::group(['prefix'=>'{campaign_id}/ad', 'middleware' => 'permission:advertise.campaign.ad'], function () {
            Route::get('data', 'AdController@data')->name('advertise.campaign.ad.data');
            Route::get('list', 'AdController@list')->name('advertise.campaign.ad');
            //编辑
            Route::get('{id?}', 'AdController@edit')->name('advertise.campaign.ad.edit')->middleware('permission:advertise.campaign.ad.edit');
            Route::post('{id?}', 'AdController@save')->name('advertise.campaign.ad.save')->middleware('permission:advertise.campaign.ad.edit');
            Route::post('{id}/enable', 'AdController@enable')->name('advertise.ad.enable')->middleware('permission:advertise.campaign.ad.edit');
            Route::post('{id}/disable', 'AdController@disable')->name('advertise.ad.disable')->middleware('permission:advertise.campaign.ad.edit');
            //删除
//            Route::delete('destroy', 'AdController@destroy')->name('advertise.campaign.ad.destroy')->middleware('permission:advertise.campaign.ad.destroy');
        });

        // 子渠道
        Route::group(['prefix'=>'{campaign_id}/channel', 'middleware' => 'permission:advertise.campaign'], function () {
            Route::get('data', 'ChannelController@data')->name('advertise.campaign.channel.data');
            Route::get('list', 'ChannelController@list')->name('advertise.campaign.channel');
        });

        // 区域
        Route::group(['prefix'=>'{campaign_id}/region', 'middleware' => 'permission:advertise.campaign'], function () {
            Route::get('data', 'RegionController@data')->name('advertise.campaign.region.data');
            Route::get('list', 'RegionController@list')->name('advertise.campaign.region');
        });
    });

    //文件
    Route::post('Asset', 'AssetController@processMediaFiles')->name('advertise.asset.process'); // 素材
});
