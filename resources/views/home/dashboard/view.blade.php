@extends('layout.base')

@section('content')
<div class="layui-tab" lay-filter="dash">
  <ul class="layui-tab-title">
    <li class="layui-this layui-bg-blue">User Acquisition</li>
    <li class="layui-bg-green">Ad Monetization</li>
  </ul>
  <div class="layui-tab-content">
    <div class="layui-tab-item layui-show">
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
        <div class="layui-col-sm6 layui-col-md3">

            <div class="layui-card">

                <div class="layui-card-header">

                    Available credit

                    <span class="layui-badge layui-bg-green layuiadmin-badge">Today</span>

                </div>

                <div class="layui-card-body layuiadmin-card-list">
                    <p id="credit" class="layuiadmin-big-font">-</p>
                    {{-- <p>

                        eCPM

                        <span class="layuiadmin-span-color"><span id="ecpm">-</span> <i class="layui-inline layui-icon layui-icon-dollar"></i></span>

                    </p> --}}

                </div>

            </div>

        </div>


        <div class="layui-col-sm12">

            <div class="layui-card">

                <div class="layui-card-header">

                    Trends

                </div>

                <div class="layui-card-body">

                    <div class="layui-row">

                        <div class="layui-col-sm12">

                                 <div class="layadmin-dataview" id="chart">

                                    <div><i class="layui-icon layui-icon-loading1 layadmin-loading"></i></div>

                                </div>

                                
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>
    </div>
    <div class="layui-tab-item layui-show">
         <div class="layui-row layui-col-space15">

        <div class="layui-col-sm6 layui-col-md4">

            <div class="layui-card">

                <div class="layui-card-header">

                    Impressions

                    <span class="layui-badge layui-bg-blue layuiadmin-badge">Today</span>

                </div>

                <div class="layui-card-body layuiadmin-card-list">

                    <p id="impressions1" class="layuiadmin-big-font">-</p>

                    <p>

                        Impressions 


                    </p>

                </div>

            </div>

        </div>

        <div class="layui-col-sm6 layui-col-md4">

            <div class="layui-card">

                <div class="layui-card-header">

                    Clicks

                    <span class="layui-badge layui-bg-orange layuiadmin-badge">Today</span>

                </div>

                <div class="layui-card-body layuiadmin-card-list">

                    <p id="clicks1" class="layuiadmin-big-font">-</p>

                    <p>

                        CTR

                        <span class="layuiadmin-span-color"><span id="ctr1">-</span> <i class="layui-inline layui-icon layui-icon-username"></i></span>

                    </p>

                </div>

            </div>

        </div>

        <div class="layui-col-sm6 layui-col-md4">

            <div class="layui-card">

                <div class="layui-card-header">

                    Revenue

                    <span class="layui-badge layui-bg-green layuiadmin-badge">Today</span>

                </div>

                <div class="layui-card-body layuiadmin-card-list">
                    <p id="spends1" class="layuiadmin-big-font">-</p>
                    <p>

                        eCPM

                        <span class="layuiadmin-span-color"><span id="ecpm1">-</span> <i class="layui-inline layui-icon layui-icon-dollar"></i></span>

                    </p>

                </div>

            </div>

        </div>


        <div class="layui-col-sm12">

            <div class="layui-card">

                <div class="layui-card-header">

                    Trends

                </div>

                <div class="layui-card-body">

                    <div class="layui-row">

                        <div class="layui-col-sm12">


                                <div class="layadmin-dataview" id="chart1">

                                    <div><i class="layui-icon layui-icon-loading1 layadmin-loading"></i></div>

                                </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>
    </div>
  </div>
</div>
    
@endsection

@section('script')
    @include('home.dashboard._js')
@endsection
