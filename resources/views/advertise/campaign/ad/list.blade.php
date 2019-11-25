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
                    <input type="text" name="name" id="name" placeholder="Name" class="layui-input">
                </div>
                <button class="layui-btn" id="adSearchBtn">Search</button>
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
            </script>
            <script type="text/html" id="nameTpl">
                @can('advertise.campaign.ad.edit')
                    <a class="layui-table-link" lay-event="edit">
                @endcan
                    @{{ d.ad.name }}
                @can('advertise.campaign.ad.edit')
                    </a>
                @endcan

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
    @can('advertise.campaign.ad')
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
                    ,url: "{{ route('advertise.campaign.ad.data', [$campaign['id']]) }}" //数据接口
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
                        {field: 'name', title: 'Name', templet: '#nameTpl', width:300}
                        // ,{field: 'app.name', title: 'App', templet: '#appTpl'}
                        ,{field: 'created', title: 'Created', width:110}
                        ,{field: 'impressions', title: 'Impressions'}
                        ,{field: 'clicks', title: 'Clicks'}
                        ,{field: 'installs', title: 'Installs'}
                        ,{field: 'spend', title: 'Spend'}
                        ,{field: 'ecpi', title: 'eCPI'}
                        ,{field: 'ecpm', title: 'eCPM'}
                        ,{field: 'status', title: 'Status', templet: '#status', width:90}
                        // ,{fixed: 'right', width: 220, align:'center', toolbar: '#options'}
                    ]]
                });

                //监听工具条
                table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                    var data = obj.data //获得当前行数据
                        ,layEvent = obj.event; //获得 lay-event 对应的值
                    if(layEvent === 'del'){
                        layer.confirm('确认删除吗？', function(index){
                            $.post("{{ route('advertise.campaign.ad.destroy', [$campaign['id']]) }}",{_method:'delete',ids:[data.ad_id]},function (result) {
                                if (result.code==0){
                                    obj.del(); //删除对应行（tr）的DOM结构
                                }
                                layer.close(index);
                                layer.msg(result.msg);
                                dataTable.reload();
                            });
                        });
                    } else if(layEvent === 'edit'){
                        layer.open({
                            type: 2,
                            title: '',
                            shadeClose: true, area: ['90%', '90%'],
                            content: '/advertise/campaign/{{$campaign['id']}}/ad/'+data.ad_id,
                            end: function () {
                                dataTable.reload();
                            }
                        });
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

                //按钮批量删除
                $("#listDelete").click(function () {
                    var ids = []
                    var hasCheck = table.checkStatus('dataTable');
                    var hasCheckData = hasCheck.data
                    if (hasCheckData.length>0){
                        $.each(hasCheckData,function (index,element) {
                            ids.push(element.id)
                        })
                    }
                    if (ids.length>0){
                        layer.confirm('确认删除吗？', function(index){
                            $.post("{{ route('advertise.campaign.ad.destroy', [$campaign['id']]) }}",{_method:'delete',ids:ids},function (result) {
                                if (result.code==0){
                                    dataTable.reload()
                                }
                                layer.close(index);
                                layer.msg(result.msg,)
                            });
                        })
                    }else {
                        layer.msg('请选择删除项')
                    }
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

                //搜索
                $("#adSearchBtn").click(function () {
                    var name = $("#name").val();
                    var type = $("#type").val();
                    dataTable.reload({
                        where:{name:name, type:type},
                        page:{curr:1}
                    })
                })
            })
        </script>
    @endcan
@endsection