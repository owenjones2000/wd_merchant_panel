{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-block">
        <input type="text" name="name" value="{{ $apps->name ?? old('name') }}" lay-verify="required" placeholder="请输入名称" autocomplete="off" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">Bundle ID</label>
    <div class="layui-input-block">
        <input type="text" name="bundle_id" value="{{ $apps->bundle_id ?? old('bundle_id') }}" lay-verify="required" placeholder="请输入BundleID" autocomplete="off" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">平台</label>
    <div class="layui-input-block">
        <select name="os" lay-filter="os">
            @foreach(\App\Models\Advertise\OS::$list as $os_code => $os)
                <option @if(isset($apps['os']) && $apps['os'] == $os_code) selected @endif value="{{$os_code}}">{{$os}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">状态</label>
    <div class="layui-input-block">
        <input type="checkbox" name="status" @if(isset($apps->status) && $apps->status) checked @endif lay-skin="switch" lay-filter="switchStatus" lay-text="启用|停用">
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