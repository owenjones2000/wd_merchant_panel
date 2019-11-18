@extends('layout.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>Ad @if($ad->id) Edit @else Create @endif</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('advertise.campaign.ad.save',[$ad['campaign_id'], $ad['id']])}}" method="post" onsubmit="return dosubmit()">
                @include('advertise.campaign.ad._form')
            </form>
        </div>
    </div>
@endsection