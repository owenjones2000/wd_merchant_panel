@extends('layout.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>App Edit</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('publish.app.save',['id'=>$apps->id])}}" method="post" onsubmit="return dosubmit()">
                @include('publish.app._form')
            </form>
        </div>
    </div>
@endsection