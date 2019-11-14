@extends('layout.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新应用</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('advertise.campaign.update',['id'=>$campaign->id])}}" method="post" onsubmit="return dosubmit()">
                {{ method_field('put') }}
                @include('advertise.campaign._form')
            </form>
        </div>
    </div>
@endsection