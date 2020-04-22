{{csrf_field()}}

@php
    $selected_ad_type = \App\Models\Advertise\AdType::get(empty($ad['type_id'])?\App\Models\Advertise\AdType::Video_Landscape_Short:$ad['type_id']);
@endphp
<div class="layui-form-item">
    <label for="" class="layui-form-label">Ad Type</label>
    <div class="layui-input-inline">
        <select name="type_id" id="type_id" lay-filter="selectType" @if($ad->assets->count()>0) disabled @endif lay-verify="required">
            @foreach(\App\Models\Advertise\AdType::$list as $ad_type)
                <option @if($selected_ad_type['id'] == $ad_type['id']) selected @endif value="{{ $ad_type['id']}}">{{ $ad_type['name'] }}</option>
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

{{--<div class="layui-form-item">--}}
    {{--<label class="layui-form-label">Status</label>--}}
    {{--<div class="layui-input-block">--}}
        {{--<input type="checkbox" name="status" @if($ad->status??false) checked @endif lay-skin="switch" lay-filter="switchStatus" lay-text="">--}}
    {{--</div>--}}
{{--</div>--}}

<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>Creative Set</legend>
</fieldset>
<div class="layui-form-item">
    <div class="layui-collapse" id="fileList">
        @foreach($ad->assets as $asset)
            @php
                $media_width = isset($asset['spec']['width']) ? ($asset['spec']['width'] < 300 ? $asset['spec']['width'] : 300) : 300;
            @endphp
        <div class="layui-colla-item" data-type="{{ \App\Models\Advertise\AdType::getAssetTypeGroupKey($selected_ad_type['id'], $asset['type_id']) }}">
            <h2 class="layui-colla-title">{{ \App\Models\Advertise\AssetType::get($asset['type_id'])['name'] }}</h2>
            <div class="layui-colla-content">
                @if($asset['type']['mime_type'] == 'video')
                    <video width="{{$media_width}}px" height="auto" controls="controls">
                        <source src="{{ $asset['url'] }}">
                    </video>
                @elseif($asset['type']['mime_type'] == 'image')
                    <img src="{{ $asset['url'] }}" width="{{$media_width}}px">
                @elseif($asset['type']['mime_type'] == 'html')
                    <a href="{{ $asset['url'] }}" target="_blank" class="layui-btn layui-btn-normal">Click to preview</a>
                @endif
                <input type="hidden" name="asset[{{$asset['type_id']}}][id]" value="{{ $asset['id'] }}">
                <input type="hidden" name="asset[{{$asset['type_id']}}][type]" value="{{ $asset['type_id'] }}">
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="layui-form-item">
    <div class="layui-upload-drag" id="upload">
        <i class="layui-icon"></i>
        <p>Drag & Drop your files or Browse</p>
        <div class="layui-progress" lay-filter="uploadProgress">
            <div class="layui-progress-bar"></div>
        </div>
    </div>
</div>

<div class="layui-form-item">
    <ul id="assetTypeCheckList">
    @foreach($selected_ad_type['need_asset_type'] as $asset_type_key => $asset_type_id)
        @if(is_array($asset_type_id))
            <li data-type="{{$asset_type_key}}" >
                <i class="layui-icon layui-icon-radio" style="color: @if($ad->assets->whereIn('type_id', $asset_type_id)->count())#76C81C;@else#666;@endif"></i>
                @foreach($asset_type_id as $sub_asset_type_id)
                    {{ \App\Models\Advertise\AssetType::get($sub_asset_type_id)['name'] }}
                    @if(!$loop->last)
                         or
                    @endif
                @endforeach
            </li>
        @else
            <li data-type="{{$asset_type_key}}" >
                <i class="layui-icon layui-icon-radio" style="color: @if($ad->assets->contains('type_id', $asset_type_id))#76C81C;@else#666;@endif"></i>
                {{ \App\Models\Advertise\AssetType::get($asset_type_id)['name'] }}
            </li>
        @endif
    @endforeach
    </ul>
</div>

<div class="layui-form-item">
</div>

<div class="layui-form-item">
    <div class="layui-input-block">
        @can('advertise.campaign.ad.edit')
        <button type="submit" class="layui-btn" lay-submit="">Save</button>
        @endcan
        <button class="layui-btn layui-btn-primary close-iframe">Cancel</button>
    </div>
</div>

@section('script')
    @include('layout.common_edit')
    @include('advertise.campaign.ad._js')
@endsection
