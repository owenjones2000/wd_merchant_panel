<script>
    layui.extend({
        formSelects: 'lib/extend/formSelects-v4' // 多选组件
    }).use(['form', 'formSelects'],function () {
        var formSelects = layui.formSelects;
        var form = layui.form;

        // 多选初始化
        formSelects.render('selectRegions', {placeholder:'Countries'});

        // 初始化分国列表
        var regions = formSelects.value('selectRegions');
        // updateCountryList('#track', regions);
        updateCountryList('#budget', regions);
        updateCountryList('#bid', regions);

        formSelects.on('selectRegions', function(id, vals, val, isAdd, isDisabled){
            //id:           点击select的id
            //vals:         当前select已选中的值
            //val:          当前select点击的值
            //isAdd:        当前操作选中or取消
            //isDisabled:   当前选项是否是disabled
            // updateCountryList('#track', vals);
            updateCountryList('#budget', vals);
            updateCountryList('#bid', vals);
            //如果return false, 那么将取消本次操作
            return true;
        }, true);

        // 单选框初始化
        form.on('radio(radioByCountry)', function(data){
            if(data.value == 0){
                // 关闭折叠
                $(data.elem.parentNode).children('.layui-colla-content').removeClass('layui-show');
            }else{
                // 打开折叠
                $(data.elem.parentNode).children('.layui-colla-content').addClass('layui-show');
            }
        });

        function updateCountryList(elem, regions){
            var ul = $(elem);
            ul.children().each(function(){
                var li_index = $(this).data('index');
                if(-1 === regions.findIndex(obj => obj.value == li_index)){
                    this.remove();
                }
            }, regions);
            $(regions).each(function(){
                if(ul.find('[data-index='+this.value + ']').length){
                    return;
                }
                ul.append(buildCountryItem(elem, this));
            });
        }

        function buildCountryItem(elem, obj){
            switch(elem){
                case '#track':
                    return $([
                        '<li data-index="'+ obj.value +'">',
                        '<input type="hidden" name="track['+ obj.value +'][region_code]" value="'+ obj.value +'">',
                        '<div class="layui-form-item">',
                        '<label class="layui-form-label">'+ obj.name +'</label>',
                        '<div class="layui-input-block">',
                        '<input type="text" name="track['+ obj.value +'][impression]" value="" placeholder="impression url" autocomplete="off" class="layui-input" >',
                        '</div>',
                        '</div>',
                        '<div class="layui-form-item">',
                        '<div class="layui-input-block">',
                        '<input type="text" name="track['+ obj.value +'][click]" value="" placeholder="click url" autocomplete="off" class="layui-input" >',
                        '</div>',
                        '</div>',
                        '</li>',
                    ].join(''));
                case '#budget':
                    return $([
                        '<li data-index="'+ obj.value +'">',
                        '<div class="layui-form-item">',
                        '<label class="layui-form-label">'+ obj.name +'</label>',
                        '<div class="layui-input-inline">',
                        '<input type="hidden" name="budget['+ obj.value +'][region_code]" value="'+ obj.value +'">',
                        '<input type="text" name="budget['+ obj.value +'][amount]" value="" placeholder="$" autocomplete="off" class="layui-input" >',
                        '</div>',
                        '</div>',
                        '</li>',
                    ].join(''));
                case '#bid':
                    return $([
                        '<li data-index="'+ obj.value +'">',
                        '<div class="layui-form-item">',
                        '<label class="layui-form-label">'+ obj.name +'</label>',
                        '<div class="layui-input-inline">',
                        '<input type="hidden" name="bid['+ obj.value +'][region_code]" value="'+ obj.value +'">',
                        '<input type="text" name="bid['+ obj.value +'][amount]" value="" placeholder="$" autocomplete="off" class="layui-input" >',
                        '</div>',
                        '</div>',
                        '</li>',
                    ].join(''));
            }
        }
    });
</script>