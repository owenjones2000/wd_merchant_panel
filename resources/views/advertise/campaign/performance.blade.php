@extends('layout.base')

@section('content')
    <style>
        .layui-form-checkbox span {
            min-width: 88px;
        }
    </style>
    <div class="layui-card">

    <form class="layui-form" action="{{route('advertise.campaign.export')}}" id="performance">
                <label class="layui-form-label">Controls</label>
                <div class="layui-form-item">
                <div class=" layui-input-block">
                     <input type="text" name="rangedate" id="rangedate" class="layui-input" autocomplete="off" placeholder="default today" style="min-width: 15rem">
                </div>   
                </div>
                <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="checkbox" name="date"  value="date" id = "date" title="Day">
                </div>   
                </div>
                <div class="layui-form-item">
                <div class="layui-input-block layui-inline">
                    <div class="layui-input-inline" style="width: 200px;">
                        <input type="checkbox" name="os" value="os"  id="os"title="Platform">
                    </div>
                    <div class="layui-input-inline" style="width:800px">
                        <select name="os_select" lay-verify="" >
                            <option value=""> </option>
                            <option value="ios">IOS</option>
                            <option value="android">Android</option>
                        </select>
                    </div>
                </div>   
                </div>
                <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="checkbox" name="country" value="country" id="country" title="Country">
                </div>   
                </div>
                <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="checkbox" name="ad_id" value="ad_id" id="ad_id" title="Ad">
                </div>   
                </div>
                
                <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="checkbox" name="campaign_id" value="campaign_id" id="campaign_id" title="Campaign">
                </div>   
                </div>
                

                <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="checkbox" name="target_app_id" value="target_app_id" id="ad_id" title="Sub Site Id">
                </div>   
                </div>
                <div class="layui-form-item">
                <div class="layui-inline layui-input-block">
                    <div class="layui-input-inline" style="width: 200px;">
                    <input type="checkbox" name="app_id" value="app_id" id="ad_id" title="App"  class="layui-input">
                    </div>
                    <div class="layui-input-inline" style="width:800px">
                    <div id="app" class="xm-select-demo"  class="layui-input"></div>
                    </div>
                    
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
        
            layui.extend({
                xmSelect: 'lib/extend/xm-select' // 多选组件
            }).use(['layer','table','form','laydate','util', 'xmSelect'],function () {
                var layer = layui.layer;
                var form = layui.form;
                var table = layui.table;
                var util = layui.util;
                var mycols = [
                    {field: 'impressions', title: 'Impressions'},
                    {field: 'clicks', title: 'Clicks'},
                    {field: 'installs', title: 'Installs'},
                    {field: 'ctr', title: 'CTR'},
                    {field: 'cvr', title: 'CVR'},
                    {field: 'ir', title: 'IR'},
                    {field: 'ecpi', title: 'eCPI'},
                    {field: 'ecpm', title: 'eCPM'},
                    {field: 'spend', title: 'Spend'},
                ];
                var apps = {!! $apps  !!}
                console.log(apps)
                var appSelect = xmSelect.render({
                    el: '#app', 
                    language: 'zn',
                    tips: '',
                    data: apps,
                    prop:{
                        name: 'name',
                        value: 'value'
                    },
                    name: 'app_select',
                    filterable: true,
                })
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
                    //     var mycols = [
                    //     {field: 'impressions', title: 'Impressions'},
                    //     {field: 'clicks', title: 'Clicks'},
                    //     {field: 'installs', title: 'Installs'},
                    //     {field: 'ctr', title: 'CTR'},
                    //     {field: 'cvr', title: 'CVR'},
                    //     {field: 'ir', title: 'IR'},
                    //     {field: 'ecpi', title: 'eCPI'},
                    //     // {field: 'ecpm', title: 'Ecpm'},
                    //     {field: 'spend', title: 'Spend'},
                    // ];
                    var selectArr  = appSelect.getValue('value');
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
                        if($(this).val() == 'app_id') {
                            mycols.unshift({field: 'app', title: 'App'})
                        }
                        if($(this).val() == 'target_app_id') {
                            mycols.unshift({field: 'target_app', title: 'Sub Site Id'})
                        }
                        if($(this).val() == 'date') {
                            mycols.unshift({field: 'date', title: 'Day'})
                        }
                        
                    });
                    console.log(mycols);
                    console.log(selectArr);
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
                        ,where: obj
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
