@extends('home.login_register.base')

@section('content')
    <div class="layadmin-user-login-box layadmin-user-login-body layui-form">
        <form action="{{route('home.login')}}" method="post">
            {{csrf_field()}}
            <div class="layui-form-item">
                <label class="layadmin-user-login-icon layui-icon layui-icon-username" for="LAY-user-login-username"></label>
                <input type="text" name="email" value="{{old('email')}}" lay-verify="required" placeholder="email" autocomplete="username" class="layui-input">
            </div>
            <div class="layui-form-item">
                <label class="layadmin-user-login-icon layui-icon layui-icon-password" for="LAY-user-login-password"></label>
                <input type="password" name="password"  lay-verify="required" placeholder="password" autocomplete="current-password" class="layui-input">
            </div>

            <div class="layui-form-item">
                <button type="submit" class="layui-btn layui-btn-fluid" lay-submit lay-filter="">Log in</button>
            </div>
            <div class="layui-form-item">
                <a href="http://uatest.wudiads.com/" class="layui-btn layui-btn-warm layui-btn-fluid" target="_blank">Try The New Web</a>
            </div>
        </form>
    </div>
@endsection