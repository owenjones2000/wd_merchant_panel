@extends('layout.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header  layuiadmin-card-header-auto">
            <h2>Add Advertiser</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('home.user.store')}}" method="post" onsubmit="return dosubmit()">
            @include('home.user._form')
        </form>
        </div>
    </div>
@endsection