@extends('layout.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group ">
                {{--@can('advertise.app.destroy')--}}
                    {{--<button class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">Remove Selected</button>--}}
                {{--@endcan--}}
                @can('advertise.app.edit')
                    <button class="layui-btn layui-btn-normal layui-btn-sm" id="app_add">Create App</button>
                @endcan
            </div>
            <div class="layui-form" >
                <div class="layui-input-inline">
                    <input type="text" name="keyword" id="keyword" placeholder="Keyword" class="layui-input">
                </div>
                <button class="layui-btn" id="appSearchBtn">Search</button>
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    {{--@can('advertise.app.destroy')--}}
                        {{--<a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">Remove</a>--}}
                    {{--@endcan--}}
                    @can('advertise.campaign')
                        <a class="layui-btn layui-btn-sm" lay-event="campaign">Campaigns</a>
                    @endcan
                </div>
            </script>
            <script type="text/html" id="nameTpl">
                <img width="24px" height="24px" src="@{{ d.icon_url ? d.icon_url : '/image/none.png' }}" />
                @can('advertise.app')
                <a class="layui-table-link" lay-event="edit" href="javascript:;">
                @endcan
                    @{{ d.name }}
                @can('advertise.app')
                </a>
                @endcan
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
    @can('advertise.app')
        <script>
            layui.use(['layer','table','form'],function () {
                var layer = layui.layer;
                var form = layui.form;
                var table = layui.table;
                //?????????????????????
                var dataTable = table.render({
                    elem: '#dataTable'
                    ,autoSort: false
                    ,height: 500
                    ,url: "{{ route('advertise.app.data') }}" //????????????
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
                        {field: 'name', title: 'Name', templet: '#nameTpl', width:300}
                        ,{field: 'bundle_id', title: 'Package Name'}
                        ,{field: 'os', title: 'Platform'}
                        ,{field: 'description', title: 'Description'}
                        ,{field: 'track', title: 'Track', templet: '#track'}
                        ,{field: 'track_code', title: 'Track Code'}
                        ,{field: 'status', title: 'Status', templet: '#status', align:'center',}
                        // ,{field: 'created_at', title: 'Created'}
                        // ,{field: 'updated_at', title: 'Updated'}
                        ,{fixed: 'right', width: 220, align:'center', toolbar: '#options'}
                    ]]
                });

                //???????????????
                table.on('tool(dataTable)', function(obj){ //??????tool????????????????????????dataTable???table????????????????????? lay-filter="????????????"
                    var data = obj.data //?????????????????????
                        ,layEvent = obj.event; //?????? lay-event ????????????
                    switch(layEvent){
                        case 'del':
                        {{--layer.confirm('??????????????????', function(index){--}}
                            {{--$.post("{{ route('advertise.app.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {--}}
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
                                content: '/advertise/app/'+data.id,
                                end: function () {
                                    dataTable.reload();
                                }
                            });
                            break;
                        case 'enable':
                            layer.confirm('Confirm activate [ '+data.name+' ] ?', function(index){
                                $.post('/advertise/app/'+data.id+'/enable',
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
                                $.post('/advertise/app/'+data.id+'/disable',
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
                        case 'campaign':
                            layer.open({
                                type: 2,
                                title: 'App: ' + data.name +'('+data.os+')',
                                shadeClose: true,
                                area: ['95%', '95%'],
                                content: '/advertise/app/' + data.id + '/campaign/list',
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

                $('#app_add').on('click',function () {
                    layer.open({
                        type: 2,
                        title: '',
                        shadeClose: true, area: ['90%', '90%'],
                        content: "{{ route('advertise.app.edit') }}",
                        end: function () {
                            dataTable.reload();
                        }
                    });
                });

                //??????
                $("#appSearchBtn").click(function () {
                    var keyword = $("#keyword").val();
                    var type = $("#type").val();
                    dataTable.reload({
                        where:{keyword:keyword, type:type},
                        page:{curr:1}
                    })
                })
            })
        </script>
    @endcan
@endsection