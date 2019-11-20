{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">Name</label>
    <div class="layui-input-block">
        <input type="text" name="name" value="{{ $apps->name ?? old('name') }}" lay-verify="required" placeholder="please input name" autocomplete="off" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">Package Name</label>
    <div class="layui-input-block">
        <input type="text" name="bundle_id" value="{{ $apps->bundle_id ?? old('bundle_id') }}" lay-verify="required" placeholder="please input package name" autocomplete="off" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">Platform</label>
    <div class="layui-input-block">
        <select name="os" lay-filter="os">
            @foreach(\App\Models\Advertise\OS::$list as $os_code => $os)
                <option @if(isset($apps['os']) && $apps['os'] == $os_code) selected @endif value="{{$os_code}}">{{$os}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">Status</label>
    <div class="layui-input-block">
        <input type="checkbox" name="status" @if($apps->status??true) checked @endif lay-skin="switch" lay-filter="switchStatus" lay-text="">
    </div>
</div>

<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit="">Save</button>
        <button class="layui-btn close-iframe">Cancel</button>
    </div>
</div>

@section('script')
    @include('layout.common_edit')
@endsection