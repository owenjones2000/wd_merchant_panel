{{csrf_field()}}
@section('style')
    <link rel="stylesheet" href="/static/admin/layuiadmin/style/formSelects-v4.css" media="all">
@endsection
@php
$disable_basic_info = $campaign['ads']->count() > 0;
@endphp
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>Basic info</legend>
</fieldset>
<div class="layui-form-item">
    <label for="" class="layui-form-label">Name</label>
    <div class="layui-input-block">
        <input type="text" name="name" value="{{ $campaign->name ?? old('name') }}" lay-verify="required" placeholder="" autocomplete="off" class="layui-input @if($disable_basic_info) layui-disabled @endif" >
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">App</label>
    <div class="layui-input-block">
        @if($disable_basic_info)
            <input type="text" value="{{ $campaign['app']['name'] ?? '' }}" placeholder="" autocomplete="off" class="layui-input layui-disabled">
        @else
            <select name="app_id" lay-filter="app">
                @foreach($apps as $app_item)
                    <option @if(isset($campaign['app_id']) && $campaign['app_id'] == $app_item['id']) selected @endif value="{{$app_item['id']}}">{{$app_item['name']}}</option>
                @endforeach
            </select>
        @endif
    </div>
</div>

{{--<div class="layui-form-item">--}}
    {{--<label class="layui-form-label">Status</label>--}}
    {{--<div class="layui-input-block">--}}
        {{--<input type="checkbox" name="status" @if($campaign->status??false) checked @endif lay-skin="switch" lay-filter="switchStatus" lay-text="">--}}
    {{--</div>--}}
{{--</div>--}}

<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>Targeting</legend>
</fieldset>
<div class="layui-collapse">
    <div class="layui-colla-item">
        <h2 class="layui-colla-title">Countries</h2>
        <div class="layui-colla-content layui-show">
            <div class="layui-input-block">
                <select name="regions" xm-select="selectRegions" xm-select-search="", lay-filter="selectRegions">
                    @foreach($regions as $region)
                        <option
                                @if($campaign->regions->contains($region)) selected @endif
                        value="{{ $region->code }}">{{ $region->name }}({{ $region->code }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="layui-colla-item">
        <h2 class="layui-colla-title">Audience</h2>
        <div class="layui-colla-content @if($campaign['audience']) layui-show @endif">
            <div class="layui-form-item">
                <label class="layui-form-label">State</label>
                <div class="layui-input-block">
                    <select name="audience[states]" xm-select="selectStates" xm-select-search="", lay-filter="selectStates">
                        @foreach($states as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">Gender</label>
                <div class="layui-input-block">
                    <input type="radio" name="audience[gender]" value="0" title="All" @if(0 == $campaign['audience']['gender']) checked @endif>
                    <input type="radio" name="audience[gender]" value="1" title="Male" @if(1 == $campaign['audience']['gender']) checked @endif>
                    <input type="radio" name="audience[gender]" value="2" title="Female" @if(2 == $campaign['audience']['gender']) checked @endif>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">Adult</label>
                <div class="layui-input-block">
                    <input type="checkbox" name="audience[adult]" value="1" lay-skin="switch" @if($campaign['audience']['adult']) checked @endif />
                </div>
            </div>
        </div>
    </div>

    <div class="layui-colla-item">
        @php
            // $is_budget_by_all_region = $campaign->budgets->count() == 0 || $campaign->budgets->contains('country', 'ALL');
            $budget_for_all_region = $campaign->budgets->where('country', 'ALL')->first();
        @endphp
        <h2 class="layui-colla-title">Daily Budgets</h2>
        <div class="layui-colla-content layui-show">
            <div class="layui-form-item">
                <label class="layui-form-label">Default</label>
                <div class="layui-input-inline">
                    <input type="hidden" name="budget[0][region_code]" value="0">
                    <input type="text" name="budget[0][amount]" value="{{ $budget_for_all_region['amount']??old('budget.0.amount', '') }}" placeholder="$" autocomplete="off" class="layui-input" lay-verify="required" >
                </div>
            </div>

            {{--<div class="layui-input-block">--}}
                {{--<div>--}}
                {{--<input type="radio" name="budget_by_region" value="0" title="Use default daily budget only" @if($is_budget_by_all_region) checked="" @endif lay-filter="radioByCountry">--}}
                {{--<input type="radio" name="budget_by_region" value="1" title="Daily budget by Country" @if(!$is_budget_by_all_region) checked="" @endif lay-filter="radioByCountry">--}}
                    {{--<div class="layui-colla-content @if(!$is_budget_by_all_region) layui-show @endif">--}}
                        {{--<ul id="budget">--}}
                            {{--@if(!$is_budget_by_all_region)--}}
                                {{--@foreach($campaign->budgets as $budget)--}}
                            {{--<li data-index="{{$budget['region']['code']}}">--}}
                                {{--<div class="layui-form-item">--}}
                                {{--<label class="layui-form-label">{{$budget['region']['name']}}</label>--}}
                                {{--<div class="layui-input-inline">--}}
                                    {{--<input type="hidden" name="budget[{{$budget['region']['code']}}][region_code]" value="{{ $budget['region']['code'] }}">--}}
                                    {{--<input type="text" name="budget[{{$budget['region']['code']}}][amount]" value="{{ $budget['amount'] }}" placeholder="$" autocomplete="off" class="layui-input" >--}}
                                {{--</div>--}}
                                {{--</div>--}}
                            {{--</li>--}}
                                {{--@endforeach--}}
                            {{--@endif--}}
                        {{--</ul>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        </div>
    </div>

    <div class="layui-colla-item">
        @php
            $is_bid_by_region = $campaign->bids->where('deleted_at', null)->count() > 1;
            $bid_for_all_region = $campaign->bids->where('country', 'ALL')->first();
        @endphp
        <h2 class="layui-colla-title">Bidding</h2>
        <div class="layui-colla-content layui-show">
            <div class="layui-form-item">
                <label class="layui-form-label">Default</label>
                <div class="layui-input-inline">
                    <input type="hidden" name="bid[0][region_code]" value="0">
                    <input type="text" name="bid[0][amount]" value="{{ $bid_for_all_region['amount']??old('bid.0.amount', '') }}" lay-verify="required" placeholder="$" autocomplete="off" class="layui-input" >
                </div>
            </div>

            <div class="layui-input-block">
                <input type="radio" name="bid_by_region" value="0" title="Use default bid only" @if(!$is_bid_by_region) checked="" @endif lay-filter="radioByCountry">
                <input type="radio" name="bid_by_region" value="1" title="CPI Bid by Country" @if($is_bid_by_region) checked="" @endif lay-filter="radioByCountry">
                <div class="layui-colla-content @if($is_bid_by_region) layui-show @endif">
                    <ul id="bid">
                        @if($is_bid_by_region)
                            @foreach($campaign->bids as $bid)
                                <li data-index="{{$bid['region']['code']}}">
                                    <div class="layui-form-item">
                                    <label class="layui-form-label">{{$bid['region']['name']}}({{$bid['region']['code']}})</label>
                                    <div class="layui-input-inline">
                                        <input type="hidden" name="bid[{{$bid['region']['code']}}][region_code]" value="{{ $bid['region']['code'] }}">
                                        <input type="text" name="bid[{{$bid['region']['code']}}][amount]" value="{{ $bid['amount'] }}" placeholder="$" autocomplete="off" class="layui-input" >
                                    </div>
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="layui-form-item"></div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit="">Save</button>
        <button class="layui-btn layui-btn-primary close-iframe">Cancel</button>
    </div>
</div>

@section('script')
    @include('layout.common_edit')
    @include('advertise.campaign._js')
@endsection