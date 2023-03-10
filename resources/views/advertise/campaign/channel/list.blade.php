@extends('layout.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form" >
                {{--<div class="layui-input-inline">--}}
                    {{--<input type="text" name="keyword" id="keyword" placeholder="Keyword" class="layui-input">--}}
                {{--</div>--}}
                <div class="layui-input-inline">
                    <input type="text" name="rangedate" id="rangedate" class="layui-input" autocomplete="off" placeholder="default today" style="min-width: 15rem">
                </div>
                <button class="layui-btn" id="channelSearchBtn">Run Report</button>
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    {{--@can('advertise.campaign.channel.destroy')--}}
                        {{--<a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">Remove</a>--}}
                    {{--@endcan--}}
                </div>
            </script>
            <script type="text/html" id="nameTpl">
                    @{{ d.channel.name_hash }}
            </script>
            <script type="text/html" id="status">
                @{{# if(d.status){ }}
                    <a lay-event="disable" title="Click to pause" href="javascript:;"><i class="layui-icon layui-icon-radio" style="color: #76C81C;"></i></a>
                @{{# } else { }}
                    <a lay-event="enable" title="Click to activate" href="javascript:;"><i class="layui-icon layui-icon-radio" style="color: #666;"></i></a>
                @{{# } }}
            </script>
        </div>
    </div>
@endsection

@section('script')
    @can('advertise.campaign')
        <script>
            layui.use(['layer','table','form','laydate','util'],function () {
                var layer = layui.layer;
                var form = layui.form;
                var table = layui.table;
                var util = layui.util;

                //??????????????????
                var laydate = layui.laydate;
                laydate.render({
                    elem: '#rangedate'
                    ,lang: 'en'
                    ,range: '~' //??? range: '~' ????????????????????????
                    ,value: '{{ $rangedate }}' //util.toDateString(new Date(), 'yyyy-MM-dd ~ yyyy-MM-dd')
                    ,extrabtns:??[
                        // {id:'today',??text:'Today',??range:[new Date(),??new Date()]},
                        {id:'yesterday',??text:'Yesterday',??range:[new Date(new Date().setDate(new Date().getDate()-1)),
                                new Date(new Date().setDate(new Date().getDate()-1))]},
                        {id:'lastday-7',??text:'Last 7 days',??range:[new Date(new Date().setDate(new Date().getDate()-7)),
                                new Date(new Date().setDate(new Date().getDate()-1))]},
                        {id:'lastday-28',??text:'Last 28 days',??range:[new Date(new Date().setDate(new Date().getDate()-28)),
                                new Date(new Date().setDate(new Date().getDate()-1))]},
                        {id:'thismonth',??text:'This month',??range:[new Date(new Date().setDate(1)),
                                new Date(new Date(new Date().setMonth(new Date().getMonth()+1)).setDate(0))]},
                        {id:'lastmonth',??text:'Last month',??range:[new Date(new Date(new Date().setMonth(new Date().getMonth()-1)).setDate(1)),
                                new Date(new Date().setDate(0))]}
                    ],
                });

                //?????????????????????
                var dataTable = table.render({
                    elem: '#dataTable'
                    ,autoSort: false
                    ,height: 500
                    ,url: "{{ route('advertise.campaign.channel.data', [$campaign['id']]) }}" //????????????
                    ,where: {rangedate: '{{$rangedate}}'}
                    ,page: true //????????????
                    ,done: function(res, curr, count){
                        //?????????????????????????????????????????????????????????
                        if(res.data.length==0 && count>0){
                            dataTable.reload({
                                page: {
                                    curr: 1 //???????????? 1 ?????????
                                }
                            });
                        }
                    }
                    ,cols: [[ //??????
                        //{checkbox: true,fixed: true}
                        // ,{field: 'id', title: 'ID', sort: true,width:80}
                        {field: 'name', title: 'App', templet: '#nameTpl', width:300}
                        // ,{field: 'app.name', title: 'App', templet: '#appTpl'}
                        ,{field: 'kpi.impressions', title: 'Impressions', sort: true, templet: function(d){return d.impressions || 0;}, width:80}
                        ,{field: 'kpi.clicks', title: 'Clicks', sort: true, templet: function(d){return d.clicks || 0;}, width:80}
                        ,{field: 'kpi.installs', title: 'Installs', sort: true, templet: function(d){return d.installs || 0;}, width:80}
                        ,{field: 'kpi.ctr', title: 'CTR', sort: true, templet: function(d){return (d.ctr || '0.00') + '%';}, width:80}
                        ,{field: 'kpi.cvr', title: 'CVR', sort: true, templet: function(d){return (d.cvr || '0.00') + '%';}, width:80}
                        ,{field: 'kpi.ir', title: 'IR', sort: true, templet: function(d){return (d.ir || '0.00') + '%';}, width:80}
                        ,{field: 'kpi.spend', title: 'Spend', sort: true, templet: function(d){return '$' + (d.spend || '0.00');}}
                        ,{field: 'kpi.ecpi', title: 'eCPI', sort: true, templet: function(d){return '$' + (d.ecpi || '0.00');}}
                        ,{field: 'kpi.ecpm', title: 'eCPM', sort: true, templet: function(d){return '$' + (d.ecpm || '0.00');}, width:80}
                        ,{field: 'status', title: 'Status', templet: '#status', width:90}
                        // ,{fixed: 'right', width: 220, align:'center', toolbar: '#options'}
                    ]]
                });

                //???????????????
                table.on('tool(dataTable)', function(obj){ //??????tool????????????????????????dataTable???table????????????????????? lay-filter="????????????"
                    var data = obj.data //?????????????????????
                        ,layEvent = obj.event; //?????? lay-event ????????????
                    switch(layEvent) {
                        case 'enable':
                            layer.confirm('Confirm activate [ '+data.channel.name_hash+' ] for ' + data.app.name + '?', function(index){
                                $.post('/advertise/campaign/'+data.campaign_id+'/channel/' + data.target_app_id + '/enable',
                                    {},
                                    function (result) {
                                        layer.msg(result.msg);
                                        layer.close(index);
                                        if (result.code==0){
                                            dataTable.reload();
                                        }
                                    });
                            });
                            break;
                        case 'disable':
                            layer.confirm('Confirm pause [ '+data.channel.name_hash+' ] for ' + data.app.name + '?', function(index){
                                $.post('/advertise/campaign/'+data.campaign_id+'/channel/' + data.target_app_id + '/disable',
                                    {},
                                    function (result) {
                                        if (result.code==0){
                                        }
                                        layer.close(index);
                                        layer.msg(result.msg);
                                        dataTable.reload();
                                    });
                            });
                            break;
                    }
                });

                //??????????????????
                table.on('sort(dataTable)', function(obj){ //??????tool????????????????????????test???table????????????????????? lay-filter="????????????"

                    //??????????????? table ???????????????????????????????????????????????????
                    //?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????
                    table.reload('dataTable', {
                        initSort: obj //?????????????????????????????????????????????????????????????????????????????????
                        ,where: { //??????????????????????????????????????????????????????????????????????????????????????????
                            field: obj.field //????????????
                            ,order: obj.type //????????????
                        }
                    });
                });

                //??????
                $("#channelSearchBtn").click(function () {
                    // var keyword = $("#keyword").val();
                    var rangedate = $("#rangedate").val();
                    dataTable.reload({
                        where:{
                            // keyword:keyword,
                            rangedate:rangedate
                        },
                        page:{curr:1}
                    })
                })
            })
        </script>
    @endcan
@endsection
