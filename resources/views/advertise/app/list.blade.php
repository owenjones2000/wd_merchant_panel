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
                //用户表格初始化
                var dataTable = table.render({
                    elem: '#dataTable'
                    ,autoSort: false
                    ,height: 500
                    ,url: "{{ route('advertise.app.data') }}" //数据接口
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
                        // {checkbox: true,fixed: true}
                        // ,{field: 'id', title: 'ID', sort: true,width:80}
                        {field: 'name', title: 'Name', templet: '#nameTpl', width:300}
                        ,{field: 'bundle_id', title: 'Package Name'}
                        ,{field: 'os', title: 'Platform'}
                        ,{field: 'description', title: 'Description'}
                        ,{field: 'track', title: 'Track', templet: '#track'}
                        ,{field: 'track_code', title: 'Track Code'}
                        ,{field: 'status', title: 'Status', templet: '#status', align:'center', width:70}
                        // ,{field: 'created_at', title: 'Created'}
                        // ,{field: 'updated_at', title: 'Updated'}
                        ,{fixed: 'right', width: 220, align:'center', toolbar: '#options'}
                    ]]
                });

                //监听工具条
                table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                    var data = obj.data //获得当前行数据
                        ,layEvent = obj.event; //获得 lay-event 对应的值
                    switch(layEvent){
                        case 'del':
                        {{--layer.confirm('确认删除吗？', function(index){--}}
                            {{--$.post("{{ route('advertise.app.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {--}}
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

                //搜索
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