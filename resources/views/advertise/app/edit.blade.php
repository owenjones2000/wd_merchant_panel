@extends('layout.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>App Edit</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('advertise.app.save',['id'=>$apps->id])}}" method="post" lay-filter="appform" onsubmit="return dosubmit()">
                @include('advertise.app._form')
            </form>
        </div>
    </div>
@endsection