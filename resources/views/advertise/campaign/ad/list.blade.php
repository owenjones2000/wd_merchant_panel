@extends('layout.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group ">
                {{--@can('advertise.campaign.ad.destroy')--}}
                    {{--<button class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">Remove</button>--}}
                {{--@endcan--}}
                @can('advertise.campaign.ad.edit')
                    <button class="layui-btn layui-btn-normal layui-btn-sm" id="ad_add">Create Ad</button>
                @endcan
            </div>
            <div class="layui-form" >
                <div class="layui-input-inline">
                    <input type="text" name="keyword" id="keyword" placeholder="Keyword" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <input type="text" name="rangedate" id="rangedate" class="layui-input" autocomplete="off" placeholder="default today" style="min-width: 15rem">
                </div>
                <button class="layui-btn" id="adSearchBtn">Run Report</button>
                <button class="layui-btn" id="export"><i class="iconfont icon-export"></i> Export</button>
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    {{--@can('advertise.campaign.ad.destroy')--}}
                        {{--<a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">Remove</a>--}}
                    {{--@endcan--}}
                </div>
                <div class="layui-btn-group">
                    @can('advertise.campaign.ad.edit')
                        <a class="layui-btn layui-btn-warm layui-btn-sm" lay-event="clone">Clone</a>
                    @endcan
                </div>
            </script>
            <script type="text/html" id="nameTpl">
                @can('advertise.campaign.ad')
                    <a class="layui-table-link" lay-event="edit" href="javascript:;">
                @endcan
                    @{{ d.name }}
                @can('advertise.campaign.ad')
                    </a>
                @endcan
            </script>
            <script type="text/html" id="previewTpl">
                @{{# if(d.is_upload_completed){ }}
                <a lay-event="preview" title="Preview" href="javascript:;">
                    <i class="layui-icon layui-icon-carousel" style="color: #76C81C;"></i>
                </a>
                @{{# } else { }}
                <a lay-event="edit" title="Preview (Lack of assets)" href="javascript:;">
                    <i class="layui-icon layui-icon-tips" style="color: #FFB800;"></i>
                </a>
                @{{# } }}
            </script>
            <script type="text/html" id="typeTpl">
                @{{ d.type.name }}
            </script>
            <script type="text/html" id="status">
                @{{# if(d.status){ }}
                <a lay-event="disable" title="Click to pause" href="javascript:;">
                    <i class="layui-icon layui-icon-radio" style="color: #76C81C;"></i>
                </a>
                @{{# } else { }}
                    @{{# if(d.is_upload_completed){ }}
                        <a lay-event="enable" title="Click to activate" href="javascript:;">
                            <i class="layui-icon layui-icon-radio" style="color: #666;"></i>
                        </a>
                    @{{# } else { }}
                        <a class="layui-table-link" title="Lack of assets" lay-event="edit" href="javascript:;">
                            <i class="layui-icon layui-icon-radio" style="color: #666;"></i>
                            {{--<i class="layui-icon layui-icon-radio" style="color: #FFB800;"></i>--}}
                        </a>
                    @{{# } }}
                @{{# } }}
            </script>
        </div>
    </div>
@endsection

@section('script')
    @can('advertise.campaign.ad')
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
                    ,value: '{{ $rangedate }}' //util.toDateString(new Date(), 'yyyy-MM-dd ~ yyyy-MM-dd')
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
                    //height: 500
                    ,url: "{{ route('advertise.campaign.ad.data', [$campaign['id']]) }}" //数据接口
                    ,where: {rangedate: '{{$rangedate}}'}
                    ,page: true //开启分页
                    ,limit:20
                    ,done: function(res, curr, count){
                        //接口回调，处理一些和表格相关的辅助事项
                         exportData=res.data;
                        if(res.data.length==0 && count>0){
                            dataTable.reload({
                                page: {
                                    curr: 1 //重新从第 1 页开始
                                }
                            });
                        }
                    }
                    ,cols: [[ //表头previewTpl
                        //{checkbox: true,fixed: true}
                        // ,{field: 'id', title: 'ID', sort: true,width:80}
                        {field: 'name', title: 'Ad', templet: '#nameTpl', width:300, fixed:true},
                        {field: 'preview', title: '', templet: '#previewTpl', align:'center', width:50, fixed: true}
                        // ,{field: 'type.name', title: 'Type', templet: '#typeTpl'}
                        ,{field: 'status', title: 'Status', templet: '#status', align:'center', width:70, fixed: true}
                        ,{field: 'kpi.impressions', title: 'Impressions', sort: true, templet: function(d){return d.kpi ? d.kpi.impressions || 0 : '-';}, width:80}
                        ,{field: 'kpi.clicks', title: 'Clicks', sort: true, templet: function(d){return d.kpi ? d.kpi.clicks || 0 : '-';}, width:80}
                        ,{field: 'kpi.installs', title: 'Installs', sort: true, templet: function(d){return d.kpi ? d.kpi.installs || 0 : '-';}, width:80}
                        ,{field: 'kpi.ctr', title: 'CTR', sort: true, templet: function(d){return d.kpi ? (d.kpi.ctr || '0.00') + '%' : '-';}, width:80}
                        ,{field: 'kpi.cvr', title: 'CVR', sort: true, templet: function(d){return d.kpi ? (d.kpi.cvr || '0.00') + '%' : '-';}, width:80}
                        ,{field: 'kpi.ir', title: 'IR', sort: true, templet: function(d){return d.kpi ? (d.kpi.ir || '0.00') + '%' : '-';}, width:80}
                        ,{field: 'kpi.spend', title: 'Spend', sort: true, templet: function(d){return d.kpi ? '$' + (d.kpi.spend || '0.00') : '-';}}
                        ,{field: 'kpi.ecpi', title: 'eCPI', sort: true, templet: function(d){return d.kpi ? '$' + (d.kpi.ecpi || '0.00') : '-';}}
                        ,{field: 'kpi.ecpm', title: 'eCPM', sort: true, templet: function(d){return d.kpi ? '$' + (d.kpi.ecpm || '0.00') : '-';}, width:80}
                        ,{field: 'created', title: 'Created', width:110, templet: function(d){return util.toDateString(d.created_at, "yyyy-MM-dd");}}
                        ,{fixed: 'right', width: 100, align:'center', toolbar: '#options'}
                    ]]
                });

                //监听工具条
                table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                    var data = obj.data //获得当前行数据
                        ,layEvent = obj.event; //获得 lay-event 对应的值
                    switch(layEvent){
                        case 'del':
                        {{--layer.confirm('确认删除吗？', function(index){--}}
                            {{--$.post("{{ route('advertise.campaign.ad.destroy', [$campaign['id']]) }}",{_method:'delete',ids:[data.id]},function (result) {--}}
                                {{--if (result.code==0){--}}
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
                                title: ' ',
                                shadeClose: true, area: ['90%', '90%'],
                                content: '/advertise/campaign/{{$campaign['id']}}/ad/'+data.id,
                                end: function () {
                                    dataTable.reload();
                                }
                            });
                            break;
                        case 'enable':
                            layer.confirm('Confirm activate [ '+data.name+' ] ?', function(index){
                                $.post('/advertise/campaign/{{$campaign['id']}}/ad/'+data.id+'/enable',
                                    {},
                                    function (result) {
                                        layer.close(index);
                                        layer.msg(result.msg);
                                        if (result.code==0){
                                            dataTable.reload();
                                        }
                                    });
                            });
                            break;
                        case 'disable':
                            layer.confirm('Confirm pause [ '+data.name+' ] ?', function(index){
                                $.post('/advertise/campaign/{{$campaign['id']}}/ad/'+data.id+'/disable',
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
                        case 'clone':
                            // layer.confirm('Clone [ '+data.name+' ] ?', function(index){
                            //     $.post('/advertise/campaign/{{$campaign['id']}}/ad/'+data.id+'/clone',
                            //         {},
                            //         function (result) {
                            //             if (result.code==0){
                            //             }
                            //             layer.close(index);
                            //             layer.msg(result.msg);
                            //             dataTable.reload();
                            //         });
                            // });
                            // break;
                            layer.open({
                                type: 2,
                                title: 'Clone',
                                area: ['95%', '95%'],
                                content: '/advertise/campaign/{{$campaign['id']}}/ad/'+data.id + '/editclone',
                                end: function () {
                                    dataTable.reload();
                                }
                            });
                            break;
                        case 'preview':
                            previewAd(data);
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

                $('#ad_add').on('click',function () {
                    layer.open({
                        type: 2,
                        title: '',
                        shadeClose: true, area: ['80%', '80%'],
                        content: "{{route('advertise.campaign.ad.edit', [$campaign['id']]) }}",
                        end: function () {
                            dataTable.reload();
                        }
                    });
                });
                $("#export").click(function(){
                    table.exportFile(dataTable.config.id,exportData);
                })
                //搜索
                $("#adSearchBtn").click(function () {
                    var keyword = $("#keyword").val();
                    var rangedate = $("#rangedate").val();
                    dataTable.reload({
                        where:{keyword:keyword, rangedate:rangedate},
                        page:{curr:1}
                    })
                })
            });
            function previewAd(ad) {
                var preview_html = '<div style="padding: 20px; background-color: #F2F2F2;">' +
                    '<div class="layui-row layui-col-space15">';
                $(ad.assets).each(function(index, asset){
                    switch(asset.type.mime_type){
                        case 'video':
                            var template_video =
                                '    <div class="layui-col-md6">' +
                                '      <div class="layui-card">' +
                                '        <div class="layui-card-header">'+ asset.type.name +'</div>' +
                                '        <div class="layui-card-body" align="center">' +
                                '<video width="'+ (asset.spec.width > 240?'240':asset.spec.width) +'px" poster controls controlsList="nodownload">' +
                                '<source src="'+ asset.url +'" type="video/mp4" />' +
                                '</video>' +
                                '        </div>' +
                                '      </div>' +
                                '    </div>';
                            preview_html = preview_html.concat(template_video);
                            break;
                        case 'image':
                            var template_image =
                                '    <div class="layui-col-md6">' +
                                '      <div class="layui-card">' +
                                '        <div class="layui-card-header">'+ asset.type.name +'</div>' +
                                '        <div class="layui-card-body" align="center">' +
                                '<img src="'+ asset.url +'" width="'+ (asset.spec.width > 240?'240':asset.spec.width) +'px">' +
                                '        </div>' +
                                '    </div>' +
                                '  </div>';
                            preview_html = preview_html.concat(template_image);
                            break;
                        case 'html':
                            var template_image =
                                '    <div class="layui-col-md6">' +
                                '      <div class="layui-card">' +
                                '        <div class="layui-card-header">'+ asset.type.name +'</div>' +
                                '        <div class="layui-card-body" align="center">' +
                                '           <a href="'+ asset.url +'" target="_blank" class="layui-btn layui-btn-normal">Click to preview</a>' +
                                '        </div>' +
                                '    </div>' +
                                '  </div>';
                            preview_html = preview_html.concat(template_image);
                            break;
                    }
                });
                preview_html = preview_html.concat(
                    '  </div>' +
                    '</div> '
                );

                //弹出层
                layer.open({
                    type: 1,
                    //offset: 't',
                    //maxHeight: 660,
                    //area: [1024 + 'px',768+'px'],
                    area: ['800px', '700px'],
                    shadeClose: true,
                    //skin: 'layui-layer-rim', //加上边框
                    title: 'AD preview: ' + ad.name,
                    content: preview_html
                });
            }
        </script>
    @endcan
@endsection
