{{csrf_field()}}
@section('style')
    <link rel="stylesheet" href="/static/admin/layuiadmin/style/formSelects-v4.css" media="all">
@endsection
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>基础信息</legend>
</fieldset>
<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-block">
        <input type="text" name="name" value="{{ $campaign->name ?? old('name') }}" lay-verify="required" placeholder="请输入名称" autocomplete="off" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">应用</label>
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

<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>投放目标</legend>
</fieldset>
<div class="layui-collapse">
    <div class="layui-colla-item">
        <h2 class="layui-colla-title">Countries</h2>
        <div class="layui-colla-content">
            <div class="layui-input-block">
                <select name="countries" xm-select="selectCountries" xm-select-search="">
                    @foreach($countries as $country)
                        <option
                                @if($campaign->countries->contains($country)) selected @endif
                        value="{{ $country->id }}">{{ $country->name }}({{ $country->code }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="layui-colla-item">
        <h2 class="layui-colla-title">Track URL</h2>
        <div class="layui-colla-content">
            <div class="layui-input-block">
                <div>
                    <input type="radio" name="track_by_country" value="0" title="Single daily budget for all Countries" checked="" lay-filter="radioBudget">
                    <div class="layui-colla-content layui-show">
                        <input type="hidden" name="track[0][country]" value="0">
                        <input type="text" name="track[0][amount]" value="" placeholder="$" autocomplete="off" class="layui-input" >
                    </div>
                </div>

                <div>
                    <input type="radio" name="track_by_country" value="1" title="Daily budget by Country" lay-filter="radioBudget">
                    <div class="layui-colla-content">
                        <ul id="track">
                            @foreach($campaign->trackUrls as $trackUrl)
                            <li data-index="{{$trackUrl['country']['id']}}">
                                <input type="hidden" name="track[{{$trackUrl['country']['id']}}][country]" value="{{$trackUrl['country']['id']}}">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">{{$trackUrl['country']['name']}}</label>
                                <div class="layui-input-block">
                                    <input type="text" name="track[{{$trackUrl['country']['id']}}][impression]" value="{{ $trackUrl['impression'] }}" placeholder="impression url" autocomplete="off" class="layui-input" >
                                </div>
                                </div>
                                <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <input type="text" name="track[{{$trackUrl['country']['id']}}][click]" value="{{ $trackUrl['click'] }}" placeholder="click url" autocomplete="off" class="layui-input" >
                                </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-colla-item">
        <h2 class="layui-colla-title">Daily Budgets</h2>
        <div class="layui-colla-content">
            <div class="layui-input-block">
                <div>
                    <input type="radio" name="budget_by_country" value="0" title="Single daily budget for all Countries" checked="" lay-filter="radioBudget">
                    <div class="layui-colla-content layui-show">
                        <input type="text" name="budget[all]" value="{{ $campaign->budget ?? old('budget') }}" placeholder="$" autocomplete="off" class="layui-input" >
                    </div>
                </div>

                <div>
                    <input type="radio" name="budget_by_country" value="1" title="Daily budget by Country" lay-filter="radioBudget">
                    <div class="layui-colla-content">
                        <ul id="budget">
                            <li data-index="0">
                                <label class="layui-form-label">US: </label>
                                <div class="layui-input-inline">
                                    <input type="hidden" name="budget[0][country]" value="{{ $campaign->budget ?? old('budget.all') }}">
                                    <input type="text" name="budget[0][amount]" value="{{ $campaign->budget ?? old('budget.all') }}" placeholder="$" autocomplete="off" class="layui-input" >
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-colla-item">
        <h2 class="layui-colla-title">Bidding</h2>
        <div class="layui-colla-content">
            <div class="layui-input-block">
                <div>
                    <input type="radio" name="bid_by_country" value="0" title="Single daily budget for all Countries" checked="" lay-filter="radioBudget">
                    <div class="layui-colla-content layui-show">
                        <input type="text" name="bid[all]" value="{{ $campaign->budget ?? old('budget') }}" placeholder="$" autocomplete="off" class="layui-input" >
                    </div>
                </div>

                <div>
                    <input type="radio" name="bid_by_country" value="1" title="Daily budget by Country" lay-filter="radioBudget">
                    <div class="layui-colla-content">
                        <ul id="bid">
                            <li data-index="0">
                                <label class="layui-form-label">US: </label>
                                <div class="layui-input-inline">
                                    <input type="hidden" name="bid[0][country]" value="{{ $campaign->budget ?? old('bid.all') }}">
                                    <input type="text" name="bid[0][amount]" value="{{ $campaign->budget ?? old('bid.all') }}" placeholder="$" autocomplete="off" class="layui-input" >
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="layui-form-item"></div>
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