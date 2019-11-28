{{csrf_field()}}

<div class="layui-form-item">
    <label for="" class="layui-form-label">Ad Type</label>
    <div class="layui-input-inline">
        <select name="type_id" id="type_id" lay-filter="selectType" lay-verify="required">
            @foreach(\App\Models\Advertise\AdType::$list as $ad_type)
                <option @if(($ad['type_id']??0) == $ad_type['id']) selected @endif value="{{ $ad_type['id']}}">{{ $ad_type['name'] }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">Name</label>
    <div class="layui-input-block">
        <input type="text" name="name" value="{{ $ad->name ?? old('name') }}" lay-verify="required" placeholder="" autocomplete="off" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">Status</label>
    <div class="layui-input-block">
        <input type="checkbox" name="status" @if($ad->status??false) checked @endif lay-skin="switch" lay-filter="switchStatus" lay-text="">
    </div>
</div>

<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>Creative Set</legend>
</fieldset>

<div class="layui-form-item">
    <div class="layui-upload-drag" id="upload">
        <i class="layui-icon"></i>
        <p>Drag & Drop your files or Browse</p>
        <div class="layui-progress" lay-filter="uploadProgress">
            <div class="layui-progress-bar"></div>
        </div>
    </div>

</div>

<div class="layui-collapse" id="fileList">
    @foreach($ad->assets as $asset)
        @php
            $media_width = $asset['spec']['width'] < 300 ? $asset['spec']['width'] : 300;
        @endphp
    <div class="layui-colla-item" data-type="{{$asset['type_id']}}">
        <h2 class="layui-colla-title">{{ \App\Models\Advertise\AssetType::get($asset['type_id'])['name'] }}</h2>
        <div class="layui-colla-content">
            @if($asset['type']['mime_type'] == 'video')
                <video width="{{$media_width}}px" height="auto" controls="controls">
                    <source src="{{ $asset['url'] }}">
                </video>
            @elseif($asset['type']['mime_type'] == 'image')
                <img src="{{ $asset['url'] }}" width="{{$media_width}}px">
            @endif
            <input type="hidden" name="asset[{{$asset['type_id']}}][id]" value="{{ $asset['id'] }}">
            <input type="hidden" name="asset[{{$asset['type_id']}}][type]" value="{{ $asset['type_id'] }}">
        </div>
    </div>
    @endforeach
</div>

<div class="layui-form-item">
</div>

<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit="">Save</button>
        <button class="layui-btn close-iframe">Cancel</button>
    </div>
</div>

@section('script')
    @include('layout.common_edit')
    @include('advertise.campaign.ad._js')
@endsection