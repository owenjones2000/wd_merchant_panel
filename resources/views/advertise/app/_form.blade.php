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
    <label class="layui-form-label">Description</label>
    <div class="layui-input-block">
        <input type="text" name="description" value="{{ $apps->description ?? old('description') }}" placeholder="description will be displayed in the ad" autocomplete="off" class="layui-input">
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
    <label class="layui-form-label">Tracking</label>
    <div class="layui-input-inline">
        <select name="track_platform_id" lay-filter="track">
            @foreach(\App\Models\Advertise\TrackPlatform::$list as $track_platform)
                <option @if(isset($apps['track_platform_id']) && $apps['track_platform_id'] == $track_platform['id']) selected @endif value="{{$track_platform['id']}}">{{$track_platform['name']}}</option>
            @endforeach
        </select>
    </div>
    <div class="layui-input-inline" style="width: 300px;">
        <input type="text" name="track_code" value="{{ $apps['track_code'] ?? old('track_code') }}" placeholder="track code (or package name)" lay-verify="required" autocomplete="off" class="layui-input" >
    </div>
</div>

{{--<div class="layui-form-item">--}}
    {{--<label class="layui-form-label">Status</label>--}}
    {{--<div class="layui-input-block">--}}
        {{--<input type="checkbox" name="status" @if($apps->status??false) checked @endif lay-skin="switch" lay-filter="switchStatus" lay-text="">--}}
    {{--</div>--}}
{{--</div>--}}

<div class="layui-form-item">
    <div class="layui-input-block">
        @can('advertise.app.edit')
            <button type="submit" class="layui-btn" lay-submit="">Save</button>
        @endcan
        <button class="layui-btn layui-btn-primary close-iframe">Cancel</button>
    </div>
</div>

@section('script')
    <script>
        layui.use('upload', function() {
            layui.upload.render({
                url: '{{ route('advertise.app.icon') }}'
                , elem: '#upload-icon' //指定原始元素，默认直接查找class="layui-upload-file"
                , method: 'post' //上传接口的http类型
                , done: function (res) {
                    $('#upload-icon').attr('src', res.url);
                    $('#input_icon').val(res.url);
                }
            });
        });
    </script>
    @include('layout.common_edit')
@endsection