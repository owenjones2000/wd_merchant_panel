@extends('layout.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加广告</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('advertise.campaign.ad.store', [$campaign['id']])}}" method="post" onsubmit="return dosubmit()">
                @include('advertise.campaign.ad._form')
            </form>
        </div>
    </div>
@endsection