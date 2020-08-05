{{csrf_field()}}
@section('style')
    <link rel="stylesheet" href="/static/admin/layuiadmin/style/formSelects-v4.css" media="all">
@endsection

<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>Campaigns</legend>
</fieldset>
<div class="layui-collapse">
    <div class="layui-colla-item">
        <h2 class="layui-colla-title">Campaigns</h2>
        <div class="layui-colla-content layui-show">
            <div class="layui-input-block">
                <select name="campaigns" xm-select="selectRegions" xm-select-search="", lay-filter="selectRegions">
                    @foreach($campaigns as $value)
                        <option
                                @if($value->id == $campaign->id)) selected @endif
                        value="{{ $value->id }}">{{ $value->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

</div>
<div class="layui-form-item"></div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit="">Save</button>
        <button class="layui-btn layui-btn-primary close-iframe">Cancel</button>
    </div>
</div>

@section('script')
    @include('layout.common_edit')
    <script>
    layui.extend({
        formSelects: 'lib/extend/formSelects-v4' // 多选组件
    }).use(['form', 'formSelects'],function () {
        var formSelects = layui.formSelects;
        var form = layui.form;

        // 多选初始化
        formSelects.render('selectRegions', {placeholder:'Campaigns'});

        // 初始化
        var regions = formSelects.value('selectRegions');

        formSelects.on('selectRegions', function(id, vals, val, isAdd, isDisabled){
            //id:           点击select的id
            //vals:         当前select已选中的值
            //val:          当前select点击的值
            //isAdd:        当前操作选中or取消
            //isDisabled:   当前选项是否是disabled
            // updateCountryList('#track', vals);
            // updateStateList(vals);
            // updateCountryList('#budget', vals);
            // updateCountryList('#bid', vals);
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

        function updateStateList(regions){
            for(i = 0,len=regions.length; i < len; i++) {
                if(regions[i].value === 'US'){
                    formSelects.render('selectStates', { skin: 'primary'});
                    return;
                }
            }
            formSelects.render('selectStates', { skin: 'default', init: []});
            formSelects.disabled('selectStates');
        }

    });
</script>
@endsection
