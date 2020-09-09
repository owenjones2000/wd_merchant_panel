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
        <input type="text" name="bundle_id" value="{{ $apps->bundle_id ?? old('bundle_id') }}" lay-verify="required" placeholder="please input package name" autocomplete="off" class="layui-input" @if($apps->id) readonly @endif>
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
        <select name="os" lay-filter="os"  @if($apps->id) disabled @endif>
            @foreach(\App\Models\Advertise\OS::$list as $os_code => $os)
                <option @if(isset($apps['os']) && $apps['os'] == $os_code) selected @endif value="{{$os_code}}">{{$os}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="layui-form-item" id="appid">
    <label class="layui-form-label">App Id (IOS Required)</label>
    <div class="layui-input-block">
        <input type="text" name="app_id" value="{{ $apps->app_id ?? old('app_id') }}"  @if($apps->id) readonly @endif placeholder="e.g.:149****988" autocomplete="off" lay-verify="" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">Tracking</label>
    <div class="layui-input-inline">
        <select name="track_platform_id" lay-filter="track" @if($apps->id) disabled @endif>
            @foreach(\App\Models\Advertise\TrackPlatform::$list as $track_platform)
                <option @if(isset($apps['track_platform_id']) && $apps['track_platform_id'] == $track_platform['id']) selected @endif value="{{$track_platform['id']}}">{{$track_platform['name']}}</option>
            @endforeach
        </select>
    </div>
    <div class="layui-input-inline" style="width: 500px;">
        <input type="text" name="track_code"  id="track_code" value="{{ $apps['track_code'] ?? old('track_code') }}" @if($apps->id) readonly @endif placeholder="eg.id1234567890" lay-verify="required" autocomplete="off" class="layui-input" >
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">Tracking Url</label>
    <div class="layui-input-block">
        <input type="text" name="track_url" value="{{ $apps->track_url ?? old('track_url') }}" lay-verify="" @if($apps->id) readonly @endif placeholder="" autocomplete="off" class="layui-input">
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">App Tag</label>
    <div class="layui-input-block">
        <div id="tag" class="xm-select-demo"  class="layui-input"></div>
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
        layui.extend({
                xmSelect: 'lib/extend/xm-select' // 多选组件
            }).use(['upload', 'form','xmSelect'], function() {
            var form = layui.form;
            layui.upload.render({
                url: '{{ route('advertise.app.icon') }}'
                , elem: '#upload-icon' //指定原始元素，默认直接查找class="layui-upload-file"
                , acceptMime: 'image/*'
                , method: 'post' //上传接口的http类型
                , size: 200
                , done: function (res) {
                    $('#upload-icon').attr('src', res.url);
                    $('#input_icon').val(res.url);
                }
            });
            var tags = {!! $tags  !!}
            var apptags = {!! $apptags  !!}
                console.log(tags)
                console.log(apptags)
                var appSelect = xmSelect.render({
                    el: '#tag', 
                    language: 'zn',
                    tips: '',
                    data: tags,
                    prop:{
                        name: 'name',
                        value: 'id'
                    },
                    initValue: apptags,
                    name: 'tags',
                    filterable: true,
                })
            form.on('select', function(data){
                // console.log(data.elem); //得到select原始DOM对象
                console.log(data.value); //得到被选中的值
                // console.log(data.othis); //得到美化后的DOM对象
                osval = form.val('appform');
                console.log(osval);
                if (osval.os == 'android') {
                    document.getElementById('appid').style.display="none";
                }else {
                    document.getElementById('appid').style.display="block";
                }
                if(osval.track_platform_id == 3){
                    $('#track_code').attr('placeholder','the campaign id in click url');
                } else if(osval.track_platform_id == 2){
                    $('#track_code').attr('placeholder','the trackcode in click url');
                }else if (osval.track_platform_id==1 && osval.os == 'android'){
                    $('#track_code').attr('placeholder','bundle id, eg.com.xxx.xxxx');
                }else if (osval.track_platform_id==1 && osval.os == 'ios'){
                    $('#track_code').attr('placeholder','eg.id1234567890');
                }

            }); 
            // form.on('select(track)', function(data){
            //     if (data.value == 1) {
            //         $('#track_code').attr('placeholder','id145678*****');
            //     }else {
            //         document.getElementById('appid').style.display="block";
            //     }
            // }); 
        });
    </script>
    @include('layout.common_edit')
@endsection
