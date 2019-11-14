@extends('layout.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新应用</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('advertise.campaign.ad.update',[$ad['campaign_id'], $ad['id']])}}" method="post" onsubmit="return dosubmit()">
                {{ method_field('put') }}
                @include('advertise.campaign.ad._form')
            </form>
        </div>
    </div>
@endsection