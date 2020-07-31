@extends('layout.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>Ad Clone</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('advertise.ad.clone',[$campaign['id'], $id])}}" method="post" onsubmit="return dosubmit()">
                @include('advertise.campaign.ad.clone')
            </form>
        </div>
    </div>
@endsection