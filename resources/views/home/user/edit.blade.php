@extends('layout.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>User Edit</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('home.user.update',['id'=>$user])}}" method="post">
                <input type="hidden" name="id" value="{{$user->id}}">
                {{method_field('put')}}
                @include('home.user._form')
            </form>
        </div>
    </div>
@endsection
