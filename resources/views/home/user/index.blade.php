@extends('layout.base')

@section('content')
    <div class="layui-card">

        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                @can('system.user.destroy')
                <button class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">Remove Selected</button>
                @endcan
                @can('system.user.create')
                <a class="layui-btn layui-btn-sm" id="user_add">Add</a>
                @endcan
                @can('system.user.create')
                    <a class="layui-btn layui-btn-sm" id="user_assign">Assign</a>
                @endcan
            </div>
        </div>

        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    {{--@can('system.user.edit')--}}
                    {{--<a class="layui-btn layui-btn-sm" lay-event="edit">Edit</a>--}}
                    {{--@endcan--}}
                    {{--@can('system.user.role')--}}
                    {{--<a class="layui-btn layui-btn-sm" lay-event="role">Role</a>--}}
                    {{--@endcan--}}
                    @can('system.user.permission')
                    <a class="layui-btn layui-btn-sm" lay-event="permission">Permission</a>
                    @endcan
                    @can('system.user.destroy')
                    <a class="layui-btn layui-btn-danger layui-btn-sm " lay-event="del">Remove</a>
                    @endcan
                </div>
            </script>
        </div>

    </div>
@endsection

@section('script')
    @can('system.user')
    <script>
        layui.use(['layer','table','form'],function () {
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 500
                ,url: "{{ route('home.data') }}" //数据接口
                ,where:{model:"user"}
                ,page: true
                ,done: function(res, curr, count){
                    var page_now;
                    //接口回调，处理一些和表格相关的辅助事项
                    if(res.data.length==0 && count>0){
                        if(curr-1>0){
                            page_now =curr-1;
                        }else{
                            page_now = 1 ;
                        }
                        dataTable.reload({
                            page: {
                                curr: page_now //重新从第 1 页开始
                            }
                        });
                    }

                }
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    // ,{field: 'id', title: 'ID', sort: true,width:80}
                    // ,{field: 'username', title: 'Login Account'}
                    ,{field: 'realname', title: 'Real Name'}
                    ,{field: 'email', title: 'Email'}
                    ,{field: 'phone', title: 'Phone'}
                    // ,{field: 'created_at', title: 'Created'}
                    // ,{field: 'updated_at', title: 'Updated'}
                    ,{fixed: 'right', width: 320, align:'center', toolbar: '#options'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'del'){
                    layer.confirm('Confirm remove ？', function(index){
                        $.post("{{ route('home.user.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                            if (result.code==0){
                                obj.del(); //删除对应行（tr）的DOM结构
                                dataTable.reload();
                            }
                            layer.close(index);
                            layer.msg(result.msg,{icon:6})
                        });
                    });
                } else if(layEvent === 'edit'){
                    layer.open({
                        type: 2,
                        title:'User Edit',
                        shadeClose:true, area: ['100%', '100%'],
                        content: '/home/user/'+data.id+'/edit',
                        end:function () {
                            dataTable.reload();
                        }
                    });
                } else if (layEvent === 'role'){
                    layer.open({
                        type: 2,
                        title:'Role Edit',
                        shadeClose:true, area: ['100%', '100%'],
                        content: '/home/user/'+data.id+'/role',
                        end:function () {
                            dataTable.reload();
                        }
                    });
                } else if (layEvent === 'permission'){
                    layer.open({
                        type: 2,
                        title:'Permission Edit',
                        shadeClose:true, area: ['100%', '100%'],
                        content: '/home/user/'+data.id+'/permission',
                        end:function () {
                            dataTable.reload();
                        }
                    });
                }
            });

            $('#user_add').on('click',function () {
                layer.open({
                    type: 2,
                    title: '',
                    shadeClose: true, area: ['80%', '80%'],
                    content: "{{route('home.user.create') }}",
                    end: function () {
                        dataTable.reload();
                    }
                });
            });
            $('#user_assign').on('click',function () {
                layer.prompt({
                    formType: 0,
                    value: '',
                    title: 'Advertiser Email',
                }, function(value, index, elem){
                    var requestData = {};
                    requestData['email'] = value;
                    $.post(
                        '{{ route('home.user.assign') }}',
                        requestData,
                        function (result) {
                            if(result.hasOwnProperty('code') && result.code === 0){
                                layer.msg(result.msg);
                                layer.close(index);
                                dataTable.reload();
                            }else{
                                if(result.errors.email){
                                    layer.msg(result.errors.email[0]);
                                }else if(result.message){
                                    layer.msg(result.message);
                                }else{
                                    layer.msg('Unknown error.');
                                }
                            }
                        }
                    );
                });
            });

            //按钮批量删除
            $("#listDelete").click(function () {
                var ids = [];
                var hasCheck = table.checkStatus('dataTable');
                var hasCheckData = hasCheck.data;
                if (hasCheckData.length>0){
                    $.each(hasCheckData,function (index,element) {
                        ids.push(element.id)
                    })
                }
                if (ids.length>0){
                    layer.confirm('确认删除吗？', function(index){
                        $.post("{{ route('home.user.destroy') }}",{_method:'delete',ids:ids},function (result) {
                            if (result.code==0){
                                dataTable.reload()
                            }
                            layer.close(index);
                            layer.msg(result.msg,{icon:6})
                        });
                    })
                }else {
                    layer.msg('请选择删除项',{icon:5})
                }
            })
        })
    </script>
    @endcan
@endsection



