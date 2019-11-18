{{csrf_field()}}
@section('style')
    <link rel="stylesheet" href="/static/admin/layuiadmin/style/formSelects-v4.css" media="all">
@endsection
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>Basic info</legend>
</fieldset>
<div class="layui-form-item">
    <label for="" class="layui-form-label">Name</label>
    <div class="layui-input-block">
        <input type="text" name="name" value="{{ $campaign->name ?? old('name') }}" lay-verify="required" placeholder="" autocomplete="off" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">App</label>
    <div class="layui-input-block">
        <select name="app_id" lay-filter="app">
            @foreach($apps as $app_item)
                <option @if(isset($campaign['app_id']) && $campaign['app_id'] == $app_item['id']) selected @endif value="{{$app_item['id']}}">{{$app_item['name']}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">Status</label>
    <div class="layui-input-block">
        <input type="checkbox" name="status" @if($campaign->status??true) checked @endif lay-skin="switch" lay-filter="switchStatus" lay-text="Enable|Disable">
    </div>
</div>

<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>Targeting</legend>
</fieldset>
<div class="layui-collapse">
    <div class="layui-colla-item">
        <h2 class="layui-colla-title">Countries</h2>
        <div class="layui-colla-content layui-show">
            <div class="layui-input-block">
                <select name="countries" xm-select="selectCountries" xm-select-search="", lay-filter="selectCountries">
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
        @php
            $is_track_by_all_country = $campaign->trackUrls->count() == 0 || $campaign->trackUrls->contains('country_id', null);
            $track_for_all_country = $campaign->trackUrls->where('country_id', 0)->first();
        @endphp
        <h2 class="layui-colla-title">Track URL</h2>
        <div class="layui-colla-content @if(!$is_track_by_all_country) layui-show @endif">
            <div class="layui-input-block">
                <div>
                    <input type="radio" name="track_by_country" value="0" title="Single URL for all countries" @if($is_track_by_all_country) checked="" @endif lay-filter="radioByCountry">
                    <div class="layui-colla-content @if($is_track_by_all_country) layui-show @endif">
                        <div class="layui-form-item">
                            <label class="layui-form-label"></label>
                            <input type="hidden" name="track[0][country]" value="">
                            <div class="layui-input-block">
                                <input type="text" name="track[0][impression]" value="{{ $track_for_all_country['impression']??'' }}" placeholder="impression url" autocomplete="off" class="layui-input" >
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <input type="text" name="track[0][click]" value="{{ $track_for_all_country['click']??'' }}" placeholder="click url" autocomplete="off" class="layui-input" >
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <input type="radio" name="track_by_country" value="1" title="URL by Country" @if(!$is_track_by_all_country) checked="" @endif lay-filter="radioByCountry">
                    <div class="layui-colla-content @if(!$is_track_by_all_country) layui-show @endif">
                        <ul id="track">
                            @if(!$is_track_by_all_country)
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
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="layui-colla-item">
        @php
            $is_budget_by_all_country = $campaign->dailyBudgets->count() == 0 || $campaign->dailyBudgets->contains('country_id', 0);
            $budget_for_all_country = $campaign->dailyBudgets->where('country_id', 0)->first();
        @endphp
        <h2 class="layui-colla-title">Daily Budgets</h2>
        <div class="layui-colla-content">
            <div class="layui-input-block">
                <div>
                    <input type="radio" name="budget_by_country" value="0" title="Single daily budget for all Countries" @if($is_budget_by_all_country) checked="" @endif lay-filter="radioByCountry">
                    <div class="layui-colla-content @if($is_budget_by_all_country) layui-show @endif">
                        <input type="hidden" name="budget[0][country]" value="0">
                        <input type="text" name="budget[0][amount]" value="{{ $budget_for_all_country['amount']??'' }}" placeholder="$" autocomplete="off" class="layui-input" >
                    </div>
                </div>

                <div>
                    <input type="radio" name="budget_by_country" value="1" title="Daily budget by Country" @if(!$is_budget_by_all_country) checked="" @endif lay-filter="radioByCountry">
                    <div class="layui-colla-content @if(!$is_budget_by_all_country) layui-show @endif">
                        <ul id="budget">
                            @if(!$is_budget_by_all_country)
                                @foreach($campaign->dailyBudgets as $budget)
                            <li data-index="{{$budget['country']['id']}}">
                                <div class="layui-form-item">
                                <label class="layui-form-label">{{$budget['country']['name']}}</label>
                                <div class="layui-input-inline">
                                    <input type="hidden" name="budget[{{$budget['country']['id']}}][country]" value="{{ $budget['amount'] }}">
                                    <input type="text" name="budget[{{$budget['country']['id']}}][amount]" value="{{ $budget['amount'] }}" placeholder="$" autocomplete="off" class="layui-input" >
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

    <div class="layui-colla-item">
        @php
            $is_bid_by_all_country = $campaign->bids->count() == 0 || $campaign->bids->contains('country_id', 0);
            $bid_for_all_country = $campaign->bids->where('country_id', 0)->first();
        @endphp
        <h2 class="layui-colla-title">Bidding</h2>
        <div class="layui-colla-content">
            <div class="layui-input-block">
                <div>
                    <input type="radio" name="bid_by_country" value="0" title="CPI Bid for all Countries" @if($is_bid_by_all_country) checked="" @endif lay-filter="radioByCountry">
                    <div class="layui-colla-content @if($is_bid_by_all_country) layui-show @endif">
                        <input type="hidden" name="bid[0][country]" value="0">
                        <input type="text" name="bid[0][amount]" value="{{ $bid_for_all_country['amount']??'' }}" placeholder="$" autocomplete="off" class="layui-input" >
                    </div>
                </div>

                <div>
                    <input type="radio" name="bid_by_country" value="1" title="CPI Bid by Country" @if(!$is_bid_by_all_country) checked="" @endif lay-filter="radioByCountry">
                    <div class="layui-colla-content @if(!$is_bid_by_all_country) layui-show @endif">
                        <ul id="bid">
                            @if(!$is_bid_by_all_country)
                                @foreach($campaign->bids as $bid)
                                    <li data-index="{{$bid['country']['id']}}">
                                        <div class="layui-form-item">
                                        <label class="layui-form-label">{{$bid['country']['name']}}({{$bid['country']['code']}})</label>
                                        <div class="layui-input-inline">
                                            <input type="hidden" name="bid[{{$bid['country']['id']}}][country]" value="{{ $bid['amount'] }}">
                                            <input type="text" name="bid[{{$bid['country']['id']}}][amount]" value="{{ $bid['amount'] }}" placeholder="$" autocomplete="off" class="layui-input" >
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