@extends('layout.base')

@section('content')
    <div class="layui-row layui-col-space15">

        <div class="layui-col-sm6 layui-col-md4">

            <div class="layui-card">

                <div class="layui-card-header">

                    Impressions

                    <span class="layui-badge layui-bg-blue layuiadmin-badge">Today</span>

                </div>

                <div class="layui-card-body layuiadmin-card-list">

                    <p id="impressions" class="layuiadmin-big-font">-</p>

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

                    <p id="clicks" class="layuiadmin-big-font">-</p>

                    <p>

                        CTR

                        <span class="layuiadmin-span-color"><span id="ctr">-</span> <i class="layui-inline layui-icon layui-icon-username"></i></span>

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

                    {{-- <div class="layui-btn-group layuiadmin-btn-group">

                        <a href="javascript:;" class="layui-btn layui-btn-primary layui-btn-xs">去年</a>

                        <a href="javascript:;" class="layui-btn layui-btn-primary layui-btn-xs">今年</a>

                    </div> --}}

                </div>

                <div class="layui-card-body">

                    <div class="layui-row">

                        <div class="layui-col-sm12">


                                <div class="layadmin-dataview" id="chart">

                                    <div><i class="layui-icon layui-icon-loading1 layadmin-loading"></i></div>

                                </div>

                        </div>

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
@endsection
@section('script')
    <script>
    layui.use(['index', "carousel", "echarts"],function () {
        $.get('{{ route('publish.app.dashboard.data', ['range_date' => 'now']) }}',
            {},
            function (result) {
                if (result.code==0){
                    var kpi = result['data'];
                    $('#impressions').text(toThousands(kpi['impressions']));
                    $('#clicks').text(toThousands(kpi['clicks']));
                    $('#spends').text(kpi['revenue']);
                    $('#ctr').text(kpi['ctr'] ? (kpi['ctr'] + '%') : '-');
                    $('#ecpm').text(kpi['ecpm'] ? kpi['ecpm'] : '-');
                }

        });

        var myChart = echarts.init(document.getElementById('chart'));
        var myChart1 = echarts.init(document.getElementById('chart1'));
        myChart.showLoading();
        myChart1.showLoading();
        $.get(
            '{{ route('publish.app.dashboard.data') }}',
            {},
            function (data, status) {
                if (data != null) {
                    var option = {
                        title: {
                            text: '无数据',
                            left: 'center'
                        },
                        textStyle:{
                            fontSize:32,
                            fontWeight:'bold'
                        }
                    };

                    option = buildLineChartOptions(data.data, ['impressions', 'clicks'], 'date', null);
                    option1 = buildLineChartOptions(data.data, ['revenue'], 'date', null);
                    myChart1.hideLoading();
                    myChart.hideLoading();
                    myChart.setOption(option, true);
                    myChart1.setOption(option1, true);
                }
            }, "json");

        function buildLineChartOptions(data, selects, group, subgroup){
            var {legend_data, xAxis_data, series} = buildBarOrLineData('line', data, selects, group, subgroup);

            return option = {
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: [...legend_data]
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                toolbox: {
                    feature: {
                        saveAsImage: {}
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: [...xAxis_data]
                },
                yAxis: {
                    type: 'value'
                },
                series: series
            };
        }
        function buildBarOrLineData(type, data, selects, group, subgroup){
            var series = [];
            var xAxis_data = new Set();
            var legend_data = new Set();
            if(data.length > 0){
                var bar_data = {};
                data.forEach(function(item){
                    selects.forEach(function(select){
                        var value = Number(item[select]);
                        if(value > 0){
                            if(!bar_data.hasOwnProperty(select)){
                                bar_data[select]= {};
                            }
                            var subGroupName = (item[subgroup] instanceof Object) ? item[subgroup]['name'] : item[subgroup] || '未知';
                            if(!bar_data[select].hasOwnProperty(subGroupName)){
                                bar_data[select][subGroupName] = {};
                            }
                            var groupName = (item[group] instanceof Object) ? item[group]['name'] : item[group] || '未知';
                            bar_data[select][subGroupName][groupName] = value;
                            xAxis_data.add(groupName);
                        }
                    });
                });
                for(let key in bar_data){
                    for(let sub_key in bar_data[key]){
                        var legend_key = (subgroup != undefined ? (sub_key + ' ' + key) : key);
                        var serie = {
                            name: legend_key,
                            type: type,
                        };
                        if(type === 'bar'){
                            serie['stack'] = key;
                        }
                        serie['data'] = [];
                        xAxis_data.forEach(function(item){
                            if(bar_data[key][sub_key].hasOwnProperty(item)){
                                serie['data'].push(Number(bar_data[key][sub_key][item]));
                                legend_data.add(legend_key);
                            }else{
                                serie['data'].push(0);
                            }
                        });
                        series.push(serie);
                    }
                }
            }

            return {legend_data, xAxis_data, series};
        }

        function toThousands(num) {
            var num = (num || 0).toString(), result = '';
            while (num.length > 3) {
                result = ',' + num.slice(-3) + result;
                num = num.slice(0, num.length - 3);
            }
            if (num) { result = num + result; }
            return result;
        }

    });
</script>
@endsection


