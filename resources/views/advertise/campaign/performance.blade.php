@extends('layout.base')

@section('content')
    <div class="layui-card">

    <form class="layui-form" action="{{route('advertise.campaign.export')}}" id="performance">
                <label class="layui-form-label">Controls</label>
                <div class="layui-form-item">
                <div class="layui-input-block">
                     <input type="text" name="rangedate" id="rangedate" class="layui-input" autocomplete="off" placeholder="default today" style="min-width: 15rem">
                </div>   
                </div>
                <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="checkbox" name="date"  value="date" id = "date" title="Day">
                </div>   
                </div>
                <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="checkbox" name="os" value="os"  id="os"title="Platform">
                </div>   
                </div>
                <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="checkbox" name="country" value="country" id="country" title="Country">
                </div>   
                </div>
                <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="checkbox" name="campaign_id" value="campaign_id" id="campaign_id" title="Campaign">
                </div>   
                </div>
                <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="checkbox" name="ad_id" value="ad_id" id="ad_id" title="Ad">
                </div>   
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                    <button class="layui-btn" id="reportBtn" type ="button">Run Report</button>
                    <button class="layui-btn" lay-submit lay-filter="export">Export</button>
                    
                    </div>
                </div>
                
            </form>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
           
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
                var mycols = [
                    {field: 'impressions', title: 'Impressions'},
                    {field: 'clicks', title: 'Clicks'},
                    {field: 'installs', title: 'Installs'},
                    {field: 'ctr', title: 'CTR'},
                    // {field: 'cvr', title: 'Cvr'},
                    {field: 'ir', title: 'IR'},
                    {field: 'ecpi', title: 'eCPI'},
                    // {field: 'ecpm', title: 'Ecpm'},
                    {field: 'spend', title: 'Spend'},
                ];
                //日期范围选择
                var laydate = layui.laydate;
                laydate.render({
                    elem: '#rangedate'
                    ,lang: 'en'
                    ,min: -30
                    ,max:0
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
                    ,url: "{{ route('advertise.campaign.performance-data') }}" //数据接口
                    ,page: true //开启分页
                    ,limit:50
                    ,where:{}
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
                    ,cols: [mycols]
                });

                //监听工具条
          
                //监听排序事件
                
                
                // console.log(dataTable)
                //搜索
                $("#reportBtn").click(function () {
                        var mycols = [
                        {field: 'impressions', title: 'Impressions'},
                        {field: 'clicks', title: 'Clicks'},
                        {field: 'installs', title: 'Installs'},
                        {field: 'ctr', title: 'Ctr'},
                        // {field: 'cvr', title: 'Cvr'},
                        {field: 'ir', title: 'Ir'},
                        {field: 'ecpi', title: 'Ecpi'},
                        // {field: 'ecpm', title: 'Ecpm'},
                        {field: 'spend', title: 'Spend'},
                    ];
                    var rangedate = $("#rangedate").val();
                    // group = document.getElementsByClassName('group');
                    // $('input[type="checkbox"]:checked').each(function(index,value) {
                    //         // if($(value).val() != 'on') {
                    //         //     ids += $(this).val() + ',';
                    //         // }
                    //         console.log($(value).val())
                    // });
                    $.each($('input:checkbox:checked'),function(){
                        if($(this).val() == 'country') {
                            mycols.unshift({field: 'country', title: 'Country'})
                        }
                        if($(this).val() == 'os') {
                            mycols.unshift({field: 'os', title: 'Platform'})
                        }
                        if($(this).val() == 'ad_id') {
                            mycols.unshift({field: 'ad', title: 'Ad'})
                        }
                        if($(this).val() == 'campaign_id') {
                            mycols.unshift({field: 'campaign', title: 'Campaign'})
                        }
                        if($(this).val() == 'date') {
                            mycols.unshift({field: 'date', title: 'Day'})
                        }
                        
                    });
                    console.log(mycols);
                    // check_val = [];
                    // for(let i=0; i<obj.length; i++) {
                    //     console.log(obj[i]);
                    //  }
                    // for (const k in obj) {
                    //     if (obj[k].checked) {
                    //        check_val.push(obj)
                            
                    //     }
                    // }
                    
                    params = $('#performance').serializeArray(); 
                    var obj = {};
                    for (var i = 0; i < params.length; i++) {//数据类型为"自定义类的字段名=数据"后台会自动对数据进行匹配
                        obj[params[i].name] = params[i].value;
                    }
                    // where = {params:obj}
                    // console.log(where); 
                    console.log(obj)

                    // var date = $("#date").val();
                    // console.log(date, os)
                    // dataTable.reload({
                    //     where: {params:obj},
                    //     cols: [mycols],
                    //     page:{curr:1}
                    // })
                    var dataTable = table.render({
                        elem: '#dataTable'
                        ,url: "{{ route('advertise.campaign.performance-data') }}" //数据接口
                        ,page: true //开启分页
                        ,limit:50
                        ,where: {params:obj}
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
                        ,cols: [mycols]
                    });
                })
            })
        </script>
    @endcan
@endsection
