@extends('layout.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form" >
                <div class="layui-input-inline">
                    <input type="text" name="name" id="name" placeholder="Name" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <input type="text" name="rangedate" id="rangedate" class="layui-input" autocomplete="off" placeholder="default today">
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
                    <span class="layui-bg-green">Enabled</span>
                @{{# } else { }}
                    <span class="layui-bg-red">Disabled</span>
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
                    ,range: '~' //或 range: '~' 来自定义分割字符
                    ,value: '{{ $rangedate }}' //util.toDateString(new Date(), 'yyyy-MM-dd ~ yyyy-MM-dd')
                    ,extrabtns: [
                        {id:'today', text:'今天', range:[new Date(), new Date()]},
                        {id:'yesterday', text:'昨天', range:[new Date(new Date().setDate(new Date().getDate()-1)),
                                new Date(new Date().setDate(new Date().getDate()-1))]},
                        {id:'lastday-7', text:'过去7天', range:[new Date(new Date().setDate(new Date().getDate()-7)),
                                new Date(new Date().setDate(new Date().getDate()-1))]},
                        {id:'lastday-28', text:'过去28天', range:[new Date(new Date().setDate(new Date().getDate()-28)),
                                new Date(new Date().setDate(new Date().getDate()-1))]},
                        {id:'thismonth', text:'本月', range:[new Date(new Date().setDate(1)),
                                new Date(new Date(new Date().setMonth(new Date().getMonth()+1)).setDate(0))]},
                        {id:'lastmonth', text:'上个月', range:[new Date(new Date(new Date().setMonth(new Date().getMonth()-1)).setDate(1)),
                                new Date(new Date().setDate(0))]}
                    ],
                });

                //用户表格初始化
                var dataTable = table.render({
                    elem: '#dataTable'
                    ,autoSort: false
                    ,height: 500
                    ,url: "{{ route('advertise.campaign.channel.data', [$campaign['id']]) }}" //数据接口
                    ,where: {rangedate: '{{$rangedate}}'}
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
                        {field: 'name', title: 'App', templet: '#nameTpl', width:300}
                        // ,{field: 'app.name', title: 'App', templet: '#appTpl'}
                        ,{field: 'impressions', title: 'Impressions', templet: function(d){return d.impressions || 0;}}
                        ,{field: 'clicks', title: 'Clicks', templet: function(d){return d.clicks || 0;}}
                        ,{field: 'installs', title: 'Installs', templet: function(d){return d.installs || 0;}}
                        ,{field: 'ctr', title: 'CTR', templet: function(d){return (d.ctr || '0.00') + '%';}}
                        ,{field: 'ir', title: 'IR', templet: function(d){return (d.ir || '0.00') + '%';}}
                        ,{field: 'spend', title: 'Spend', templet: function(d){return '$' + (d.spend || '0.00');}}
                        ,{field: 'ecpi', title: 'eCPI', templet: function(d){return '$' + (d.ecpi || '0.00');}}
                        ,{field: 'ecpm', title: 'eCPM', templet: function(d){return '$' + (d.ecpm || '0.00');}}
                        //,{field: 'status', title: 'Status', templet: '#status', width:90}
                        // ,{fixed: 'right', width: 220, align:'center', toolbar: '#options'}
                    ]]
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

                //搜索
                $("#channelSearchBtn").click(function () {
                    var name = $("#name").val();
                    var rangedate = $("#rangedate").val();
                    dataTable.reload({
                        where:{name:name, rangedate:rangedate},
                        page:{curr:1}
                    })
                })
            })
        </script>
    @endcan
@endsection