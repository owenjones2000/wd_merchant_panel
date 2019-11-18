@extends('layout.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新角色</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('home.role.update',['id'=>$role])}}" method="post" class="layui-form">
                {{method_field('put')}}
                <input type="hidden" name="id" value="{{$role->id}}">
                @include('home.role._form')
            </form>
        </div>
    </div>
@endsection