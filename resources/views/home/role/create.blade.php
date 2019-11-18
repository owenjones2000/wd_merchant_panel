@extends('layout.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加角色</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('home.role.store')}}" method="post" class="layui-form">
                @include('home.role._form')
            </form>
        </div>
    </div>
@endsection