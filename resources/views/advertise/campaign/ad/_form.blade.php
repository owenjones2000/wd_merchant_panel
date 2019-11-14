{{csrf_field()}}

<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-block">
        <input type="text" name="name" value="{{ $ad->name ?? old('name') }}" lay-verify="required" placeholder="请输入名称" autocomplete="off" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">状态</label>
    <div class="layui-input-block">
        <input type="checkbox" name="status" @if($ad->status??true) checked @endif lay-skin="switch" lay-filter="switchStatus" lay-text="启用|停用">
    </div>
</div>

<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit="">确 认</button>
        <button class="layui-btn close-iframe">关闭</button>
    </div>
</div>

@section('script')
    @include('layout.common_edit')
@endsection