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
    <label class="layui-form-label">Icon</label>
    <div>
        <img id="upload-icon" name="icon_url" style="height: 48px;" src="{{ $apps->icon_url }}" onerror="src='/image/none_add.png';onerror=null;"/>
        <input type="hidden" id="input_icon" name="icon_url" value="{{ $apps->icon_url }}">
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">Platform</label>
    <div class="layui-input-block">
        <select name="platform" lay-filter="os">
            @foreach(\App\Models\Advertise\OS::$list as $os_code => $os)
                <option @if(isset($apps['platform']) && $apps['platform'] == $os_code) selected @endif value="{{$os_code}}">{{$os}}</option>
            @endforeach
        </select>
    </div>
</div>

{{-- <div class="layui-form-item">
    <label class="layui-form-label">Put Mode</label>
    <div class="layui-input-block">
        <select name="put_mode" lay-filter="put_mode">
            @foreach(\App\Models\Advertise\ChannelPutMode::$list as $put_mode_id => $put_mode)
                <option @if(isset($apps['put_mode']) && $apps['put_mode'] == $put_mode_id) selected @endif value="{{$put_mode_id}}">{{$put_mode['name']}}</option>
            @endforeach
        </select>
    </div>
</div> --}}

{{--<div class="layui-form-item">--}}
    {{--<label class="layui-form-label">Status</label>--}}
    {{--<div class="layui-input-block">--}}
        {{--<input type="checkbox" name="status" @if($apps->status??false) checked @endif lay-skin="switch" lay-filter="switchStatus" lay-text="">--}}
    {{--</div>--}}
{{--</div>--}}

<div class="layui-form-item">
    <div class="layui-input-block">
        @can('publish.app.edit')
            <button type="submit" class="layui-btn" lay-submit="">Save</button>
        @endcan
        <button class="layui-btn layui-btn-primary close-iframe">Cancel</button>
    </div>
</div>

@section('script')
    <script>
        layui.use('upload', function() {
            layui.upload.render({
                url: '{{ route('publish.app.icon') }}'
                , elem: '#upload-icon' //指定原始元素，默认直接查找class="layui-upload-file"
                , method: 'post' //上传接口的http类型
                , size: 200
                , done: function (res) {
                    $('#upload-icon').attr('src', res.url);
                    $('#input_icon').val(res.url);
                }
            });
        });
    </script>
    @include('layout.common_edit')
@endsection