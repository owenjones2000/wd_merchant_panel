{{csrf_field()}}
@section('style')
    <link rel="stylesheet" href="/static/admin/layuiadmin/style/formSelects-v4.css" media="all">
@endsection

<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-block">
        <input type="text" name="name" value="{{ $campaign->name ?? old('name') }}" lay-verify="required" placeholder="请输入名称" autocomplete="off" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">平台</label>
    <div class="layui-input-block">
        <select name="app_id" lay-filter="app">
            @foreach($apps as $app_item)
                <option @if(isset($campaign['app_id']) && $campaign['app_id'] == $app_item['id']) selected @endif value="{{$app_item['id']}}">{{$app_item['name']}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">状态</label>
    <div class="layui-input-block">
        <input type="checkbox" name="status" @if($campaign->status??true) checked @endif lay-skin="switch" lay-filter="switchStatus" lay-text="启用|停用">
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">国家</label>
    <div class="layui-input-block">
        <select name="countries" xm-select="selectCountries" xm-select-search="">
            @foreach($countries as $country)
                <option
                        @if($campaign->countries->contains($country)) selected @endif
                value="{{ $country->id }}">
                    {{ $country->name }}({{ $country->code }})
                </option>
            @endforeach
        </select>
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
    @include('advertise.campaign._js')
@endsection