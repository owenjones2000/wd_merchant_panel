@extends('layout.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>Campaign @if($campaign->id) Edit @else Add @endif</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('advertise.campaign.save',['id'=>$campaign->id])}}" method="post" onsubmit="return dosubmit()">
                @include('advertise.campaign._form')
            </form>
        </div>
    </div>
@endsection