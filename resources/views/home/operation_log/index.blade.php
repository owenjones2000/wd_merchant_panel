@extends('home.base')


@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label" style="width: 60px">用户名</label>
                        <div class="layui-input-inline">
                            <input type="text" name="username" placeholder="请输入用户名" maxlength="16" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label" style="width: 40px">姓名</label>
                        <div class="layui-input-inline">
                            <input type="text" name="name" placeholder="请输入姓名" maxlength="16" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">请求方式</label>
                        <div class="layui-input-inline">
                            <select class="select3" name="method">
                                <option value="">全部</option>
                                @foreach(['GET','POST','PUT',"DELETE",] as $v)
                                    <option value="{{$v}}">{{$v}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">操作时间</label>
                        <div class="layui-input-inline">
                            <input type="text" id="created_start_at" name="created_start_at" placeholder="请选择开始时间" readonly class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" id="created_end_at" name="created_end_at" placeholder="请选择结束时间" readonly class="layui-input">
                        </div>
                        @can('system.operation_log')
                            <button style="float: left" type="submit" lay-submit lay-filter="search" class="layui-btn" >搜 索</button>
                        @endcan
                    </div>
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
        </div>
    </div>
@endsection

@section('script')
    @can('system.operation_log')
        <script>
            layui.use(['layer','table','form','laydate'],function () {
                var layer = layui.layer;
                var form = layui.form;
                var table = layui.table;
                var laydate = layui.laydate;
                //用户表格初始化
                var dataTable = table.render({
                    elem: '#dataTable'
                    ,height: 500
                    ,url: "{{ route('home.operation_log') }}" //数据接口
                    ,where:{model:"role"}
                    ,page: true //开启分页
                    ,cols: [[ //表头
                        {field: 'id', title: 'ID', sort: true,width:80}
                        ,{field: 'username', title: '用户名'}
                        ,{field: 'realname', title: '姓名'}
                        ,{field: 'method', title: '请求方式'}
                        ,{field: 'uri', title: '请求URI'}
                        ,{field: 'query', title: '请求参数'}
                        ,{field: 'ip', title: '请求IP'}
                        ,{field: 'created_at', title: '操作时间'}
                    ]]
                });

                //搜索
                form.on('submit(search)',function (data) {
                    dataTable.reload({
                        where : data.field,
                        page : {curr : 1}
                    })
                    return false;
                })

                //按钮批量删除
                /*$("#listDelete").click(function () {
                    var ids = []
                    var hasCheck = table.checkStatus('dataTable')
                    var hasCheckData = hasCheck.data
                    if (hasCheckData.length>0){
                        $.each(hasCheckData,function (index,element) {
                            ids.push(element.id)
                        })
                    }
                    if (ids.length>0){
                        layer.confirm('确认删除吗？', function(index){
                            $.post("{{ route('home.operation_log.destroy') }}",{_method:'delete',ids:ids},function (result) {
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
                })*/

                //时间日期
                laydate.render({elem:'#created_start_at',type:'datetime'})
                laydate.render({elem:'#created_end_at',type:'datetime'})
            })
        </script>
    @endcan
@endsection


