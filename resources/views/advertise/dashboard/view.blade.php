@extends('layout.base')

@section('content')
    <div class="layui-row layui-col-space15">

        <div class="layui-col-sm6 layui-col-md3">

            <div class="layui-card">

                <div class="layui-card-header">

                    Impressions

                    <span class="layui-badge layui-bg-blue layuiadmin-badge">Today</span>

                </div>

                <div class="layui-card-body layuiadmin-card-list">

                    <p id="impressions" class="layuiadmin-big-font">-</p>

                    <p>

                        Impressions to Installs

                        <span class="layuiadmin-span-color"><span id="ir">-</span> <i class="layui-inline layui-icon layui-icon-user"></i></span>

                    </p>

                </div>

            </div>

        </div>

        <div class="layui-col-sm6 layui-col-md3">

            <div class="layui-card">

                <div class="layui-card-header">

                    Clicks

                    <span class="layui-badge layui-bg-orange layuiadmin-badge">Today</span>

                </div>

                <div class="layui-card-body layuiadmin-card-list">

                    <p id="clicks" class="layuiadmin-big-font">-</p>

                    <p>

                        Impressions to Clicks

                        <span class="layuiadmin-span-color"><span id="ctr">-</span> <i class="layui-inline layui-icon layui-icon-username"></i></span>

                    </p>

                </div>

            </div>

        </div>

        <div class="layui-col-sm6 layui-col-md3">

            <div class="layui-card">

                <div class="layui-card-header">

                    Installs

                    <span class="layui-badge layui-bg-cyan layuiadmin-badge">Today</span>

                </div>

                <div class="layui-card-body layuiadmin-card-list">

                    <p id="installs" class="layuiadmin-big-font">-</p>

                    <p>

                        Clicks to Installs

                        <span class="layuiadmin-span-color"><span id="cvr">-</span> <i class="layui-inline layui-icon layui-icon-face-smile-b"></i></span>

                    </p>

                </div>

            </div>

        </div>

        <div class="layui-col-sm6 layui-col-md3">

            <div class="layui-card">

                <div class="layui-card-header">

                    Spends

                    <span class="layui-badge layui-bg-green layuiadmin-badge">Today</span>

                </div>

                <div class="layui-card-body layuiadmin-card-list">
                    <p id="spends" class="layuiadmin-big-font">-</p>
                    <p>

                        eCPM

                        <span class="layuiadmin-span-color"><span id="ecpm">-</span> <i class="layui-inline layui-icon layui-icon-dollar"></i></span>

                    </p>

                </div>

            </div>

        </div>


        <div class="layui-col-sm12">

            <div class="layui-card">

                <div class="layui-card-header">

                    Trends

                    {{--<div class="layui-btn-group layuiadmin-btn-group">--}}

                        {{--<a href="javascript:;" class="layui-btn layui-btn-primary layui-btn-xs">去年</a>--}}

                        {{--<a href="javascript:;" class="layui-btn layui-btn-primary layui-btn-xs">今年</a>--}}

                    {{--</div>--}}

                </div>

                <div class="layui-card-body">

                    <div class="layui-row">

                        <div class="layui-col-sm12">


                                <div class="layadmin-dataview" id="chart">

                                    <div><i class="layui-icon layui-icon-loading1 layadmin-loading"></i></div>

                                </div>

                        </div>

                        {{--<div class="layui-col-sm4">--}}

                            {{--<div class="layuiadmin-card-list">--}}

                                {{--<p class="layuiadmin-normal-font">月访问数</p>--}}

                                {{--<span>同上期增长</span>--}}

                                {{--<div class="layui-progress layui-progress-big" lay-showPercent="yes">--}}

                                    {{--<div class="layui-progress-bar" lay-percent="30%"></div>--}}

                                {{--</div>--}}

                            {{--</div>--}}

                            {{--<div class="layuiadmin-card-list">--}}

                                {{--<p class="layuiadmin-normal-font">月下载数</p>--}}

                                {{--<span>同上期增长</span>--}}

                                {{--<div class="layui-progress layui-progress-big" lay-showPercent="yes">--}}

                                    {{--<div class="layui-progress-bar" lay-percent="20%"></div>--}}

                                {{--</div>--}}

                            {{--</div>--}}

                            {{--<div class="layuiadmin-card-list">--}}

                                {{--<p class="layuiadmin-normal-font">月收入</p>--}}

                                {{--<span>同上期增长</span>--}}

                                {{--<div class="layui-progress layui-progress-big" lay-showPercent="yes">--}}

                                    {{--<div class="layui-progress-bar" lay-percent="25%"></div>--}}

                                {{--</div>--}}

                            {{--</div>--}}

                        {{--</div>--}}

                    </div>

                </div>

            </div>

        </div>
    </div>
@endsection

@section('script')
    @include('advertise.dashboard._js')
@endsection
