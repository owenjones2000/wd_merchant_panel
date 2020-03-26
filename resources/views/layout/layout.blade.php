

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{config('app.name')}}</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="/static/admin/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/admin/layuiadmin/style/admin.css" media="all">
    <link rel="shortcut icon" href="favicon.icn"/>
</head>
<body class="layui-layout-body">
<div id="LAY_app">
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <!-- 头部区域 -->
            <ul class="layui-nav layui-layout-left">
                <li class="layui-nav-item layadmin-flexible" lay-unselect>
                    <a href="javascript:;" layadmin-event="flexible" title="侧边伸缩">
                        <i class="layui-icon layui-icon-shrink-right" id="LAY_app_flexible"></i>
                    </a>
                </li>
                <!--li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="" target="_blank" title="前台">
                        <i class="layui-icon layui-icon-website"></i>
                    </a>
                </li-->
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;" layadmin-event="refresh" title="刷新">
                        <i class="layui-icon layui-icon-refresh-3"></i>
                        <cite>Refresh</cite>
                    </a>
                </li>
              {{--  <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <input type="text" placeholder="搜索..." autocomplete="off" class="layui-input layui-input-search" layadmin-event="serach" lay-action="template/search.html?keywords=">
                </li>--}}
            </ul>
            <ul class="layui-nav layui-layout-right" lay-filter="layadmin-layout-right">
                {{--
                <li class="layui-nav-item" lay-unselect>
                    <a lay-href="{{route('home.message.mine')}}" layadmin-event="message" lay-text="消息中心">
                        <i class="layui-icon layui-icon-notice"></i>
                        <cite>消息</cite>
                        <!-- 如果有新消息，则显示小圆点 -->
                        @if($unreadMessage)
                        <span class="layui-badge-dot"></span>
                        @endif
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="javascript:;" layadmin-event="theme">
                        <i class="layui-icon layui-icon-theme"></i>
                        <cite>主题</cite>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="javascript:;" layadmin-event="note">
                        <i class="layui-icon layui-icon-note"></i>
                        <cite>便签</cite>
                    </a>
                </li>
                --}}
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <form class="layui-form" action=""  style="color: black;">

                        <span @if(0 >= auth()->user()->getMainId()) style="color: red;" @endif>
                             <i class="layui-icon layui-icon-service"></i> Service for &nbsp;
                        </span>
                        <div class="layui-input-inline">
                            <select id="selectProject" lay-filter="selectProject" lay-verify="required">
                                <option value="">Please select advertiser</option>
                                <option {{ auth()->user()->id == auth()->user()->getMainId() ? 'selected' : '' }} value="{{auth()->user()->id}}">{{auth()->user()->realname}}</option>
                                @foreach(auth()->user()->activeMainUsers as $main_user)
                                    <option {{ $main_user['id'] == auth()->user()->getMainId() ? 'selected' : '' }} value="{{$main_user['id']}}">{{$main_user['realname']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="javascript:;" layadmin-event="fullscreen">
                        <i class="layui-icon layui-icon-screen-full"></i>
                        <cite>Full screen</cite>
                    </a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;">
                        <i class="layui-icon layui-icon-username"></i>
                        <cite>{{auth()->user()->realname}}</cite>
                    </a>
                    <dl class="layui-nav-child">
                        <dd>
                            <a lay-href="{{route('home.user.edit',['id'=>auth()->user()->id])}}">
                                <i class="layui-icon layui-icon-set"></i>
                                Profile
                            </a>
                        </dd>
                        <!--dd><a id="change-password">修改密码</a></dd-->
                        {{--<dd><a lay-href="{{route('home.message.mine')}}">我的消息</a></dd>--}}
                        <hr>
                        <dd>
                            <a href="{{route('home.logout')}}">
                                <i class="layui-icon layui-icon-close"></i>
                                Log out
                            </a>
                        </dd>
                    </dl>
                </li>

                <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-unselect>
                    <a href="javascript:;" layadmin-event="more"><i class="layui-icon layui-icon-more-vertical"></i></a>
                </li>
            </ul>
        </div>

        <!-- 侧边菜单 -->
        <div class="layui-side layui-side-menu">
            <div class="layui-side-scroll">
                <div class="layui-logo" lay-href="{{route('home.index')}}">
                    <span>{{config('app.name')}}</span>
                </div>

                <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">
                    <li data-name="home" class="layui-nav-item layui-nav-itemed">
                        <dd data-name="console">
                            <a lay-href="{{route('advertise.dashboard.view')}}">
                                <i class="layui-icon layui-icon-home"></i>
                                <cite>Dashboard</cite>
                            </a>
                        </dd>
                    </li>
                    @foreach($menus as $menu)
                        @can($menu->name)
                        <li data-name="{{$menu->name}}" class="layui-nav-item">
                            <a href="javascript:;" lay-tips="{{$menu->display_name}}" lay-direction="2">
                                <i class="layui-icon {{$menu->icon->class??''}}"></i>
                                <cite>{{$menu->display_name}}</cite>
                            </a>
                            @if($menu->childs->isNotEmpty())
                            <dl class="layui-nav-child">
                                @foreach($menu->childs as $subMenu)
                                    @can($subMenu->name)
                                    <dd data-name="{{$subMenu->name}}" >
                                        <a lay-href="{{ route($subMenu->route) }}">{{$subMenu->display_name}}</a>
                                    </dd>
                                    @endcan
                                @endforeach
                            </dl>
                            @endif
                        </li>
                        @endcan
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- 页面标签 -->
        <div class="layadmin-pagetabs" id="LAY_app_tabs">
            <div class="layui-icon layadmin-tabs-control layui-icon-prev" layadmin-event="leftPage"></div>
            <div class="layui-icon layadmin-tabs-control layui-icon-next" layadmin-event="rightPage"></div>
            <div class="layui-icon layadmin-tabs-control layui-icon-down">
                <ul class="layui-nav layadmin-tabs-select" lay-filter="layadmin-pagetabs-nav">
                    <li class="layui-nav-item" lay-unselect>
                        <a href="javascript:;"></a>
                        <dl class="layui-nav-child layui-anim-fadein">
                            <dd layadmin-event="closeThisTabs"><a href="javascript:;">Close current tab</a></dd>
                            <dd layadmin-event="closeOtherTabs"><a href="javascript:;">Close other tabs</a></dd>
                            <dd layadmin-event="closeAllTabs"><a href="javascript:;">Close all tabs</a></dd>
                        </dl>
                    </li>
                </ul>
            </div>

            <div class="layui-tab" lay-unauto lay-allowClose="true" lay-filter="layadmin-layout-tabs">
                <ul class="layui-tab-title" id="LAY_app_tabsheader">
                    <li lay-id="{{route('advertise.dashboard.view')}}" lay-attr="{{route('advertise.dashboard.view')}}" class="layui-this"><i class="layui-icon layui-icon-home"></i>&nbsp;Dashboard</li>
                </ul>
            </div>
        </div>

        <!-- 主体内容 -->
        <div class="layui-body" id="LAY_app_body">
            <div class="layadmin-tabsbody-item layui-show">
                <iframe src="{{route('advertise.dashboard.view')}}" frameborder="0" class="layadmin-iframe"></iframe>
            </div>
        </div>

        <!-- 辅助元素，一般用于移动设备下遮罩 -->
        <div class="layadmin-body-shade" layadmin-event="shade"></div>
    </div>
</div>

<script src="/static/admin/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/static/admin/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'form', 'jquery'], function(){
        var $ = layui.jquery;
        var form = layui.form;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        form.render('select');
        form.on('select(selectProject)', function(data){
            var uid = 0;
            if(data.value){
                uid = data.value;
            }
            $.post('{{ route('home.user.change') }}',
                {
                    uid: uid,
                },
                function (result) {
                    if (result.code === 0){
                        window.location.reload();
                    }
            });
        });

    });
</script>
</body>
</html>


