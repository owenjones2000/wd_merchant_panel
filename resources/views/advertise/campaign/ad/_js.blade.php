<script>
    layui.extend({
        //formSelects: 'lib/extend/formSelects-v4' // 多选组件
    }).use(['upload', 'form', 'element'],function () {
        var upload = layui.upload;
        var form = layui.form;
        var element = layui.element;

        //多文件列表示例
        var fileList = $('#fileList');
            var uploadListIns = upload.render({
            elem: '#upload'
            ,accept: 'file'
            ,exts: 'mp4'
            ,url: '{{ route('advertise.asset.process') }}'
            ,multiple: true
            ,auto: true
            ,done: function(res, index, upload){
                if(res.code == 0){ //上传成功
                    // console.log(res);
                    var asset = res.asset;
                    var typeItem = fileList.find('[data-type='+ asset.type_id + ']');
                    if(typeItem){
                        typeItem.remove();
                    }
                    var fileItem = $([
                        '<div class="layui-colla-item" data-type="'+ asset.type_id +'">',
                            '<h2 class="layui-colla-title">'+ asset.type.name +'</h2>',
                            '<div class="layui-colla-content">',
                            '<video width="300px" height="auto" controls="controls">',
                            '<source src="/storage/'+ asset.file_path +'">',
                            '</video>',
                            '<input type="hidden" name="asset['+ asset.type_id +'][id]" value="'+ asset.id +'">',
                            '<input type="hidden" name="asset['+ asset.type_id +'][type]" value="'+ asset.type_id +'">',
                            '</div>',
                        '</div>',
                    ].join(''));
                    fileList.append(fileItem);
                    element.render('collapse');
                    return ; //删除文件队列已经上传成功的文件
                }
                this.error(res, index, upload);
            }
            ,error: function(res, index, upload){
                layer.alert(res.name+': '+ res.msg, {title:'上传失败'});
            }
        });
    });
</script>