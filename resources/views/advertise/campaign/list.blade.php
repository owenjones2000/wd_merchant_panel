@extends('layout.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group ">
                {{--@can('advertise.campaign.destroy')--}}
                    {{--<button class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">Remove</button>--}}
                {{--@endcan--}}
                @can('advertise.campaign.edit')
                    <button class="layui-btn layui-btn-normal layui-btn-sm" id="campaign_add">Create Campaign</button>
                @endcan
            </div>
            <div class="layui-form" >
                <div class="layui-input-inline">
                    <input type="text" name="keyword" id="keyword" placeholder="Keyword" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <input type="text" name="rangedate" id="rangedate" class="layui-input" autocomplete="off" placeholder="default today">
                </div>
                <button class="layui-btn" id="campaignSearchBtn">Run Report</button>
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

                //日期范围选择
                var laydate = layui.laydate;
                laydate.render({
                    elem: '#rangedate'
                    ,lang: 'en'
                    ,range: '~' //或 range: '~' 来自定义分割字符
                    ,value: util.toDateString(new Date(), 'yyyy-MM-dd ~ yyyy-MM-dd')
                    ,extrabtns: [
                        // {id:'today', text:'Today', range:[new Date(), new Date()]},
                        {id:'yesterday', text:'Yesterday', range:[new Date(new Date().setDate(new Date().getDate()-1)),
                                new Date(new Date().setDate(new Date().getDate()-1))]},
                        {id:'lastday-7', text:'Last 7 days', range:[new Date(new Date().setDate(new Date().getDate()-7)),
                                new Date(new Date().setDate(new Date().getDate()-1))]},
                        {id:'lastday-28', text:'Last 28 days', range:[new Date(new Date().setDate(new Date().getDate()-28)),
                                new Date(new Date().setDate(new Date().getDate()-1))]},
                        {id:'thismonth', text:'This month', range:[new Date(new Date().setDate(1)),
                                new Date(new Date(new Date().setMonth(new Date().getMonth()+1)).setDate(0))]},
                        {id:'lastmonth', text:'Last month', range:[new Date(new Date(new Date().setMonth(new Date().getMonth()-1)).setDate(1)),
                                new Date(new Date().setDate(0))]}
                    ],
                });

                //用户表格初始化
                var dataTable = table.render({
                    elem: '#dataTable'
                    ,autoSort: false
                    ,height: 500
                    ,url: "{{ route('advertise.campaign.data') }}" //数据接口
                    ,page: true //开启分页
                    ,done: function(res, curr, count){
                        //接口回调，处理一些和表格相关的辅助事项
                        if(res.data.length==0 && count>0){
                            dataTable.reload({
                                page: {
                                    curr: 1 //重新从第 1 页开始
                                }
                            });
                        }
                    }
                    ,cols: [[ //表头
                        //{checkbox: true,fixed: true}
                        // ,{field: 'id', title: 'ID', sort: true,width:80}
                        {field: 'name', title: 'Campaign', templet: '#nameTpl', width:300, fixed: true}
                        ,{field: 'app.name', title: 'App', templet: '#appTpl', width:180, fixed: true}
                        ,{field: 'status', title: 'Status', templet: '#status', align:'center', width:70, fixed: true}
                        ,{field: 'budget', title: 'Budget', width:100, align:'center', templet: function(d){return '$' + (d.default_budget || '0.00');} }
                        ,{field: 'impressions', title: 'Impressions', sort: true, templet: function(d){return d.impressions || 0;}, width:80}
                        ,{field: 'clicks', title: 'Clicks', sort: true, templet: function(d){return d.clicks || 0;}, width:80}
                        ,{field: 'installs', title: 'Installs', templet: function(d){return d.installs || 0;}, width:80}
                        ,{field: 'ctr', title: 'CTR', templet: function(d){return (d.ctr || '0.00') + '%';}, width:80}
                        ,{field: 'cvr', title: 'CVR', templet: function(d){return (d.cvr || '0.00') + '%';}, width:80}
                        ,{field: 'ir', title: 'IR', templet: function(d){return (d.ir || '0.00') + '%';}, width:80}
                        ,{field: 'spend', title: 'Spend', templet: function(d){return '$' + (d.spend || '0.00');}}
                        ,{field: 'ecpi', title: 'eCPI', templet: function(d){return '$' + (d.ecpi || '0.00');}}
                        ,{field: 'ecpm', title: 'eCPM', templet: function(d){return '$' + (d.ecpm || '0.00');}, width:80}
                        ,{field: 'created', title: 'Created', width:110, align:'center', templet: function(d){return util.toDateString(d.created_at, "yyyy-MM-dd");}}
                        ,{fixed: 'right', width: 220, align:'center', toolbar: '#options'}
                    ]]
                });

                //监听工具条
                table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                    var data = obj.data //获得当前行数据
                        ,layEvent = obj.event; //获得 lay-event 对应的值
                    var rangedate = $("#rangedate").val();
                    switch(layEvent) {
                        case 'del':
                            {{--layer.confirm('确认删除吗？', function (index) {--}}
                                {{--$.post("{{ route('advertise.campaign.destroy') }}", {--}}
                                    {{--_method: 'delete',--}}
                                    {{--ids: [data.campaign_id]--}}
                                {{--}, function (result) {--}}
                                    {{--if (result.code == 0) {--}}
                                        {{--obj.del(); //删除对应行（tr）的DOM结构--}}
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
                                area: ['90%', '90%'],
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
                                area: ['90%', '90%'],
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
                                area: ['90%', '90%'],
                                content: '/advertise/campaign/' + data.id + '/region/list?rangedate=' + rangedate,
                                end: function () {
                                    // dataTable.reload();
                                }
                            });
                            break;
                    }
                });

                //监听排序事件
                table.on('sort(dataTable)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"

                    //尽管我们的 table 自带排序功能，但并没有请求服务端。
                    //有些时候，你可能需要根据当前排序的字段，重新向服务端发送请求，从而实现服务端排序，如：
                    table.reload('dataTable', {
                        initSort: obj //记录初始排序，如果不设的话，将无法标记表头的排序状态。
                        ,where: { //请求参数（注意：这里面的参数可任意定义，并非下面固定的格式）
                            field: obj.field //排序字段
                            ,order: obj.type //排序方式
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

                //搜索
                $("#campaignSearchBtn").click(function () {
                    var keyword = $("#keyword").val();
                    var rangedate = $("#rangedate").val();
                    dataTable.reload({
                        where:{keyword:keyword, rangedate:rangedate},
                        page:{curr:1}
                    })
                })
            })
        </script>
    @endcan
@endsection
