@extends('layout.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            {{-- <div class="layui-btn-group ">
                @can('publish.app.edit')
                    <button class="layui-btn layui-btn-normal layui-btn-sm" id="app_add">Create App</button>
                @endcan
            </div> --}}
            <div class="layui-form" >
                <div class="layui-input-inline">
                    <input type="text" name="keyword" id="keyword" placeholder="Keyword" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <input type="text" name="rangedate" id="rangedate" class="layui-input" autocomplete="off" placeholder="default today" style="min-width: 15rem">
                </div>
                <div class="layui-input-inline">
                    <select name="platform" id="platform" lay-verify="">
                    <option value="">platform</option>
                    <option value="ios">Ios</option>
                    <option value="android">Android</option>
                    </select> 
                </div>
                <div class="layui-input-inline">
                    <select name="Type" id="type" lay-verify="">
                    <option value="">Type</option>
                    <option value="1">Reward</option>
                    <option value="2">Interstitial</option>
                    </select> 
                </div>
                <div class="layui-input-inline">
                    <select name="country" id="country" lay-verify="">
                    <option value="">country</option>
                    @foreach($regions as $region)
                        <option
                        value="{{ $region->code }}">{{ $region->name }}({{ $region->code }})</option>
                    @endforeach
                    </select> 
                </div>
                <button class="layui-btn" id="appSearchBtn">Search</button>
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    {{--@can('publish.app.destroy')--}}
                        {{--<a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">Remove</a>--}}
                    {{--@endcan--}}
                </div>
            </script>
            <script type="text/html" id="nameTpl">
                
                    <img width="24px" height="24px" src="@{{ d.icon_url ? d.icon_url : '/image/none.png' }}" />
                    @{{ d.app.name }}
                
            </script>
            <script type="text/html" id="track">
                @{{# if(d.track){ }}
                    @{{ d.track.name }}
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
    @can('publish.app')
        <script>
            layui.use(['layer','table','form', 'laydate', 'util'],function () {
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
                    ,height: 500
                    ,url: "{{ route('publish.app.data') }}" //????????????
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
                        // {checkbox: true,fixed: true}
                        // ,{field: 'id', title: 'ID', sort: true,width:80}
                        {field: 'app.name', title: 'Name', templet: '#nameTpl', width:300, fixed: true, width:200}
                        ,{field: 'app.bundle_id', title: 'Package Name', templet: function(d){return  d.app.bundle_id;},fixed: true}
                        ,{field: 'app.platform', title: 'Platform', templet: function(d){return  d.app.platform;}, fixed: true}
                        ,{field: 'country', title: 'Country',templet: function(d){return  d.country || 'All';}, fixed: true}
                        ,{field: 'type', title: 'Type',templet: function(d){
                            if(d.type==1){return 'Reward';}
                            else if(d.type==2){return 'Interstitial';}
                            else{return 'All';}
                            }, fixed: true}
                        // ,{field: 'app.put_mode', title: 'Put Mode', templet: function(d){switch(d.app.put_mode){case 1: return 'Normal'; case 2: return 'Backup'; default: return 'Unknown';}}, fixed: true}
                        // ,{field: 'status', title: 'Status', templet: '#status', align:'center', width:70, fixed: true}
                        ,{field: 'impressions', title: 'Impressions', sort: true, templet: function(d){return  d.impressions || 0 ;}, }
                        ,{field: 'clicks', title: 'Clicks', sort: true, templet: function(d){return  d.clicks || 0 ;}, }
                        // ,{field: 'installs', title: 'Installs', sort: true, templet: function(d){return  d.installs || 0 ;}, width:80}
                        ,{field: 'ctr', title: 'CTR', sort: true, templet: function(d){return  (d.ctr || '0.00') + '%' ;}, }
                        // ,{field: 'cvr', title: 'CVR', sort: true, templet: function(d){return  (d.cvr || '0.00') + '%' ;}, width:80}
                        // ,{field: 'ir', title: 'IR', sort: true, templet: function(d){return  (d.ir || '0.00') + '%' ;}, width:80}
                        ,{field: 'spend', title: 'Revenue', sort: true, templet: function(d){return  '$' + (d.spend || '0.00') ;}}
                        // ,{field: 'ecpi', title: 'eCPI', sort: true, templet: function(d){return  '$' + (d.ecpi || '0.00') ;}}
                        ,{field: 'ecpm', title: 'eCPM', sort: true, templet: function(d){return  '$' + (d.ecpm || '0.00') ;}, }
                        // ,{field: 'created_at', title: 'Created'}
                        // ,{field: 'updated_at', title: 'Updated'}
                        // ,{fixed: 'right', width: 220, align:'center', toolbar: '#options'}
                    ]]
                });

                //???????????????
                table.on('tool(dataTable)', function(obj){ //??????tool????????????????????????dataTable???table????????????????????? lay-filter="????????????"
                    var data = obj.data //?????????????????????
                        ,layEvent = obj.event; //?????? lay-event ????????????
                    switch(layEvent){
                        case 'del':
                        {{--layer.confirm('??????????????????', function(index){--}}
                            {{--$.post("{{ route('publish.app.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {--}}
                                {{--if (result.code==0){--}}
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
                                shadeClose: true, area: ['90%', '90%'],
                                content: '/publish/app/'+data.target_app_id,
                                end: function () {
                                    dataTable.reload();
                                }
                            });
                            break;
                        case 'enable':
                            layer.confirm('Confirm activate [ '+data.name+' ] ?', function(index){
                                $.post('/publish/app/'+data.id+'/enable',
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
                        case 'disable':
                            layer.confirm('Confirm pause [ '+data.name+' ] ?', function(index){
                                $.post('/publish/app/'+data.id+'/disable',
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

                $('#app_add').on('click',function () {
                    layer.open({
                        type: 2,
                        title: '',
                        shadeClose: true, area: ['90%', '90%'],
                        content: "{{ route('publish.app.edit') }}",
                        end: function () {
                            dataTable.reload();
                        }
                    });
                });

                //??????
                $("#appSearchBtn").click(function () {
                    var keyword = $("#keyword").val();
                    var rangedate = $("#rangedate").val();
                    var platform = $("#platform").val();
                    var country = $("#country").val();
                    var type = $("#type").val();
                    dataTable.reload({
                        where:{keyword:keyword, rangedate:rangedate, platform:platform, country:country, type:type},
                        page:{curr:1}
                    })
                })
            })
        </script>
    @endcan
@endsection