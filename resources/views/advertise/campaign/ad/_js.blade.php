<script>
    layui.extend({
        xmSelect: 'lib/extend/xm-select' // 多选组件
    }).use(['upload', 'form', 'element','xmSelect'],function () {
        var upload = layui.upload;
        var form = layui.form;
        var element = layui.element;

        var tags = {!! $tags  !!}
        var adtags = {!! $adtags  !!}
            console.log(tags)
            console.log(adtags)
            var appSelect = xmSelect.render({
                el: '#tag', 
                language: 'zn',
                tips: '',
                data: tags,
                prop:{
                    name: 'name',
                    value: 'id'
                },
                initValue: adtags,
                name: 'tags',
                filterable: true,
            })

        //多文件列表示例
        var fileList = $('#fileList');
        var uploadListIns = upload.render({
            elem: '#upload'
            ,accept: 'file'
            ,exts: 'mp4|png|jpg|html|htm'
            ,url: '{{ route('advertise.asset.process') }}'
            ,data: {
                ad_type_id: function(){
                    return $('#type_id').val();
                }
            }
            ,multiple: true
            ,auto: true
            ,progress: function(n){
                var percent = n - 20 + '%';
                element.progress('uploadProgress', percent);
            }
            ,done: function(res, index, upload){
                if(res.code == 0){ //上传成功
                    element.progress('uploadProgress', '100%');
                    // console.log(res);
                    var asset = res.asset;
                    var typeItem = fileList.find('[data-type='+ asset.type_group_key + ']');
                    if(typeItem.length > 0){
                        typeItem.remove();
                    }
                    var fileItemList = [
                        '<div class="layui-colla-item" data-type="'+ asset.type_group_key +'">',
                        '<h2 class="layui-colla-title">'+ asset.type.name +'</h2>',
                        '<div class="layui-colla-content layui-show">',
                    ];
                    var media_width = asset.spec.hasOwnProperty('width') ? (asset.spec.width < 300 ? asset.spec.width : 300) : 300;
                    if(asset.type.mime_type == 'video'){
                        fileItemList = fileItemList.concat([
                            '<video width="'+ media_width +'px" height="auto" controls="controls">',
                            '<source src="'+ asset.url +'">',
                            '</video>',
                        ]);
                    }else if(asset.type.mime_type == 'image'){
                        fileItemList = fileItemList.concat([
                            '<img src="'+ asset.url +'" width="'+ media_width +'px">'
                        ]);
                    }else if(asset.type.mime_type == 'html'){
                        fileItemList = fileItemList.concat([
                            '<a href="'+ asset.url +'" target="_blank" class="layui-btn layui-btn-normal">Click to preview</a>'
                        ]);
                    }
                    fileItemList = fileItemList.concat([
                        '<input type="hidden" name="asset['+ asset.type_id +'][id]" value="'+ asset.id +'">',
                        '<input type="hidden" name="asset['+ asset.type_id +'][type]" value="'+ asset.type_id +'">',
                        '</div>',
                        '</div>'
                    ]);
                    var fileItem = $(fileItemList.join(''));
                    fileList.append(fileItem);
                    element.render('collapse');
                    updateAssetTypeStatus();
                    return ; //删除文件队列已经上传成功的文件
                }else{
                    element.progress('uploadProgress', '0%');
                }
                this.error(res, index, upload);
            }
            ,error: function(res, index, upload){
                layer.alert(res.msg, {title:'upload failed'});
            }
        });

        form.on('select(selectType)', function(data){
            updateAssetTypeCheckList(data.value);
        });
    });
    function updateAssetTypeCheckList(type_id){
        var asset_type_li = [];
        switch(type_id){
            @foreach(\App\Models\Advertise\AdType::$list as $ad_type)
            case '{{$ad_type['id']}}':
                asset_type_li = [
                    @foreach($ad_type['need_asset_type'] as $asset_type_key => $asset_type_id)
                        @if(is_array($asset_type_id))
                                '<li data-type="{{$asset_type_key}}" >',
                                '<i class="layui-icon layui-icon-radio" style="color:#666;"></i> ',
                            @foreach($asset_type_id as $option_asset_type_id)
                                '{{ \App\Models\Advertise\AssetType::get($option_asset_type_id)['name'] }}',
                                @if(!$loop->last)
                                     ' or ',
                                @endif
                            @endforeach
                                '</li>',
                        @else
                            '<li data-type="{{$asset_type_key}}" >',
                            '<i class="layui-icon layui-icon-radio" style="color:#666;"></i> ',
                                '{{ \App\Models\Advertise\AssetType::get($asset_type_id)['name'] }}',
                            '</li>',
                        @endif
                    @endforeach
                ].join('');
                break;
            @endforeach
        }
        $('#assetTypeCheckList').html(asset_type_li);
        updateAssetTypeStatus();
    }
    function updateAssetTypeStatus(){
        var li_list = $('#assetTypeCheckList').children('li');
        li_list.each(function (index, li){
            if($('#fileList').find('[data-type='+ $(li).data('type') + ']').length > 0){
                $(li).children('i').css('color', '#76C81C');
            }else{
                $(li).children('i').css('color', '#666');
            }
        });
    }
</script>
