@extends('layout.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group ">

            </div>
            <div class="layui-form" >
                <div class="layui-input-inline">
                    <input type="text" name="keyword" id="keyword" placeholder="Keyword" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <input type="text" name="rangedate" id="rangedate" class="layui-input" autocomplete="off" placeholder="default today" style="min-width: 15rem">
                </div>

                <button class="layui-btn" id="campaignSearchBtn">Run Report</button>
                <button class="layui-btn" id="export"><i class="iconfont icon-export"></i> Export</button>
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('advertise.campaign.edit')
                        <a class="layui-btn layui-btn-sm" lay-event="edit">Edit</a>
                    @endcan
                    @can('advertise.campaign')
                        <a class="layui-btn layui-btn-sm" lay-event="region">Countries</a>
                    @endcan
                    @can('advertise.campaign')
                        <a class="layui-btn layui-btn-sm" lay-event="channel">Sources</a>
                    @endcan
                    {{--@can('advertise.campaign.destroy')--}}
                        {{--<a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">Remove</a>--}}
                    {{--@endcan--}}
                </div>
            </script>
            <script type="text/html" id="nameTpl">
                @can('advertise.campaign.ad')
                    <a class="layui-table-link" title="Click to ads" lay-event="ad"  href="javascript:;">
                @endcan
                    @{{ d.name }}
                @can('advertise.campaign.ad')
                    </a>
                @endcan
            </script>
            <script type="text/html" id="appTpl">
                @{{# if(d.app){ }}
                    @{{ d.app.name }} (@{{ d.app.os }})
                @{{# } }}
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
                    ,value: util.toDateString(new Date(), 'yyyy-MM-dd ~ yyyy-MM-dd')
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
                    // ,height: 500
                    ,url: "{{ route('advertise.campaign.data', ['app_id' => $app_id]) }}" //????????????
                    ,page: true, //????????????
                    limit:20
                    ,done: function(res, curr, count){
                        //?????????????????????????????????????????????????????????
                        exportData=res.data;
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
                        {field: 'name', title: 'Campaign', templet: '#nameTpl', width:200, fixed: true}
                        ,{field: 'status', title: 'Status', templet: '#status', align:'center', width:70, fixed: true}
                        ,{field: 'budget', title: 'Budget', width:100, align:'center', templet: function(d){return '$' + (d.default_budget || '0.00');} }
                        ,{field: 'bid', title: 'Bid', width:70, align:'center', templet: function(d){return '$' + (d.default_bid || '0.00');} }
                        ,{field: 'kpi.impressions', title: 'Impressions', sort: true, templet: function(d){return d.kpi ? d.kpi.impressions || 0 : '-';}, width:80}
                        ,{field: 'kpi.clicks', title: 'Clicks', sort: true, templet: function(d){return d.kpi ? d.kpi.clicks || 0 : '-';}, width:80}
                        ,{field: 'kpi.installs', title: 'Installs', sort: true, templet: function(d){return d.kpi ? d.kpi.installs || 0 : '-';}, width:80}
                        ,{field: 'kpi.ctr', title: 'CTR', sort: true, templet: function(d){return d.kpi ? (d.kpi.ctr || '0.00') + '%' : '-';}, width:80}
                        ,{field: 'kpi.cvr', title: 'CVR', sort: true, templet: function(d){return d.kpi ? (d.kpi.cvr || '0.00') + '%' : '-';}, width:80}
                        ,{field: 'kpi.ir', title: 'IR', sort: true, templet: function(d){return d.kpi ? (d.kpi.ir || '0.00') + '%' : '-';}, width:80}
                        ,{field: 'kpi.spend', title: 'Spend', sort: true, templet: function(d){return d.kpi ? '$' + (d.kpi.spend || '0.00') : '-';}}
                        ,{field: 'kpi.ecpi', title: 'eCPI', sort: true, templet: function(d){return d.kpi ? '$' + (d.kpi.ecpi || '0.00') : '-';}}
                        ,{field: 'kpi.ecpm', title: 'eCPM', sort: true, templet: function(d){return d.kpi ? '$' + (d.kpi.ecpm || '0.00') : '-';}, width:80}
                        ,{field: 'created', title: 'Created', width:110, align:'center', templet: function(d){return util.toDateString(d.created_at, "yyyy-MM-dd");}}
                        ,{fixed: 'right', width: 220, align:'center', toolbar: '#options'}
                    ]]
                });

                //???????????????
                table.on('tool(dataTable)', function(obj){ //??????tool????????????????????????dataTable???table????????????????????? lay-filter="????????????"
                    var data = obj.data //?????????????????????
                        ,layEvent = obj.event; //?????? lay-event ????????????
                    var rangedate = $("#rangedate").val();
                    switch(layEvent) {
                        case 'del':
                            {{--layer.confirm('??????????????????', function (index) {--}}
                                {{--$.post("{{ route('advertise.campaign.destroy') }}", {--}}
                                    {{--_method: 'delete',--}}
                                    {{--ids: [data.campaign_id]--}}
                                {{--}, function (result) {--}}
                                    {{--if (result.code == 0) {--}}
                                        {{--obj.del(); //??????????????????tr??????DOM??????--}}
                                    {{--}--}}
                                    {{--layer.close(index);--}}
                                    {{--layer.msg(result.msg);--}}
                                    {{--dataTable.reload();--}}
                                {{--});--}}
                            {{--});--}}
                            break;
                        case 'edit':
                            layer.open({
                                type: 2,
                                title: '',
                                shadeClose: true, area: ['80%', '80%'],
                                content: '/advertise/campaign/' + data.id,
                                end: function () {
                                    dataTable.reload();
                                }
                            });
                            break;
                        case 'enable':
                            layer.confirm('Confirm activate [ '+data.name+' ] ?', function(index){
                                $.post('/advertise/campaign/'+data.id+'/enable',
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
                            layer.confirm('Confirm pause [ '+data.name+' ] ?', function(index){
                                $.post('/advertise/campaign/'+data.id+'/disable',
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
                        case 'ad':
                            layer.open({
                                type: 2,
                                title: 'Campaign: ' + data.name,
                                shadeClose: true,
                                area: ['95%', '95%'],
                                content: '/advertise/campaign/' + data.id + '/ad/list?rangedate=' + rangedate,
                                end: function () {
                                    // dataTable.reload();
                                }
                            });
                            break;
                        case 'channel':
                            layer.open({
                                type: 2,
                                title: 'Campaign: ' + data.name,
                                shadeClose: true,
                                area: ['95%', '95%'],
                                content: '/advertise/campaign/' + data.id + '/channel/list?rangedate=' + rangedate,
                                end: function () {
                                    // dataTable.reload();
                                }
                            });
                            break;
                        case 'region':
                            layer.open({
                                type: 2,
                                title: 'Campaign: ' + data.name,
                                shadeClose: true,
                                area: ['95%', '95%'],
                                content: '/advertise/campaign/' + data.id + '/region/list?rangedate=' + rangedate,
                                end: function () {
                                    // dataTable.reload();
                                }
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

                $('#campaign_add').on('click',function () {
                    layer.open({
                        type: 2,
                        title:'',
                        shadeClose: true, area: ['80%', '80%'],
                        content: "{{route('advertise.campaign.edit') }}",
                        end: function () {
                            dataTable.reload();
                        }
                    });
                });
                //     var keyword = $("#keyword").val();
                //     var rangedate = $("#rangedate").val();
                //     var platform = $("#platform").val();
                //     var exportInfo = table.render({
                //     // elem: '#dataTable',
                //     autoSort: false
                //     ,height: 500
                //     ,url: "{{ route('advertise.campaign.alldata') }}" //????????????
                //     ,where:{keyword:keyword, rangedate:rangedate, platform:platform}
                //     ,done: function(res, curr, count){
                //         //?????????????????????????????????????????????????????????
                //         exportDataAll=res.data;
                //     }
                //     ,cols: [[ //??????
                //         //{checkbox: true,fixed: true}
                //         // ,{field: 'id', title: 'ID', sort: true,width:80}
                //         {field: 'name', title: 'Campaign', templet: '#nameTpl', width:200, fixed: true}
                //         ,{field: 'app.name', title: 'App', templet: '#appTpl', width:180, fixed: true}
                //         ,{field: 'budget', title: 'Budget', width:100, align:'center', templet: function(d){return '$' + (d.default_budget || '0.00');} }
                //         ,{field: 'bid', title: 'Bid', width:70, align:'center', templet: function(d){return '$' + (d.default_bid || '0.00');} }
                //         ,{field: 'kpi.impressions', title: 'Impressions', sort: true, templet: function(d){return d.kpi ? d.kpi.impressions || 0 : '-';}, width:80}
                //         ,{field: 'kpi.clicks', title: 'Clicks', sort: true, templet: function(d){return d.kpi ? d.kpi.clicks || 0 : '-';}, width:80}
                //         ,{field: 'kpi.installs', title: 'Installs', sort: true, templet: function(d){return d.kpi ? d.kpi.installs || 0 : '-';}, width:80}
                //         ,{field: 'kpi.ctr', title: 'CTR', sort: true, templet: function(d){return d.kpi ? (d.kpi.ctr || '0.00') + '%' : '-';}, width:80}
                //         ,{field: 'kpi.cvr', title: 'CVR', sort: true, templet: function(d){return d.kpi ? (d.kpi.cvr || '0.00') + '%' : '-';}, width:80}
                //         ,{field: 'kpi.ir', title: 'IR', sort: true, templet: function(d){return d.kpi ? (d.kpi.ir || '0.00') + '%' : '-';}, width:80}
                //         ,{field: 'kpi.spend', title: 'Spend', sort: true, templet: function(d){return d.kpi ? '$' + (d.kpi.spend || '0.00') : '-';}}
                //         ,{field: 'kpi.ecpi', title: 'eCPI', sort: true, templet: function(d){return d.kpi ? '$' + (d.kpi.ecpi || '0.00') : '-';}}
                //         ,{field: 'kpi.ecpm', title: 'eCPM', sort: true, templet: function(d){return d.kpi ? '$' + (d.kpi.ecpm || '0.00') : '-';}, width:80}
                //         ,{field: 'created', title: 'Created', width:110, align:'center', templet: function(d){return util.toDateString(d.created_at, "yyyy-MM-dd");}}
                //     ]]
                // });
                $("#export").click(function(){
                    // LAY_EXCEL.exportExcel([['Hello', 'World', '!']], 'hello.xlsx', 'xlsx')
                    table.exportFile(dataTable.config.id, exportData);
                })
                console.log(dataTable)
                //??????
                $("#campaignSearchBtn").click(function () {
                    var keyword = $("#keyword").val();
                    var rangedate = $("#rangedate").val();
                    var platform = $("#platform").val();
                    dataTable.reload({
                        where:{keyword:keyword, rangedate:rangedate, platform:platform},
                        page:{curr:1}
                    })
                })
            })
        </script>
    @endcan
@endsection
