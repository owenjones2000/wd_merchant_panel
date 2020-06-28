<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login - Wudi Ads</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/static/admin/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/admin/layuiadmin/style/admin.css" media="all">
    <link rel="stylesheet" href="/static/admin/layuiadmin/style/login.css" media="all">
    <style type="text/css">
        .browser-tip{
            padding: 20px;
            background-color: #FFB800;
            color: #FFF;
            line-height: 1.5em;
            display: none;
        }
        .browser-tip a{
            color: #01AAED;
            font-weight: bold;
        }
        .layadmin-user-login-main{
            background-color: #FFF;
        }
    </style>
</head>
<body>
<div class="layadmin-user-login layadmin-user-display-show" >

    <div class="layadmin-user-login-main">
        <div class="layadmin-user-login-box layadmin-user-login-header">
            <h2>{{config('app.name')}}</h2>
            {{--<p>Wudi Ads </p>--}}
        </div>
        @yield('content')
        <div class="browser-tip" id="browser-tip">
            <p>Sorry, your browser may not be compatible with our system, please change to another browser, such as <a
                    href="https://google.com/chrome/">Google Chrome</a>, to avoid system problems, thank you!</p>
        </div>
    </div>

</div>

<script src="/static/admin/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/static/admin/layuiadmin/' //静态资源所在路径
    }).use(['layer'],function () {
        var layer = layui.layer;

        //表单提示信息
        @if(count($errors)>0)
            @foreach($errors->all() as $error)
                layer.msg("{{$error}}",{icon:5});
                @break
            @endforeach
        @endif

        @if(session('status')=='logout')
          window.parent.location.href="{{route('home.logout')}}";
        @endif
        //正确提示
        @if(session('success'))
        layer.msg("{{session('success')}}",{icon:6});
        @endif

    });

    window.onload = function () {
        var ver = IEVersion();
        if(ver != -1){
            var tip = document.getElementById('browser-tip');
            tip.style.display = "block";
        }
    };

    function IEVersion() {
        var userAgent = navigator.userAgent; //取得浏览器的userAgent字符串
        var isIE = userAgent.indexOf("compatible") > -1 && userAgent.indexOf("MSIE") > -1; //判断是否IE<11浏览器
        var isEdge = userAgent.indexOf("Edge") > -1 && !isIE; //判断是否IE的Edge浏览器
        var isIE11 = userAgent.indexOf('Trident') > -1 && userAgent.indexOf("rv:11.0") > -1;
        if(isIE) {
            var reIE = new RegExp("MSIE (\\d+\\.\\d+);");
            reIE.test(userAgent);
            var fIEVersion = parseFloat(RegExp["$1"]);
            if(fIEVersion == 7) {
                return 7;
            } else if(fIEVersion == 8) {
                return 8;
            } else if(fIEVersion == 9) {
                return 9;
            } else if(fIEVersion == 10) {
                return 10;
            } else {
                return 6;//IE版本<=7
            }
        } else if(isEdge) {
            return 'edge';//edge
        } else if(isIE11) {
            return 11; //IE11
        }else{
            return -1;//不是ie浏览器
        }
    }
</script>
</body>
</html>
