<script>
    layui.extend({
        formSelects: 'lib/extend/formSelects-v4' // 多选组件
    }).use(['formSelects'],function () {
        var formSelects = layui.formSelects;

        // 多选初始化
        formSelects.render('selectCountries', {placeholder:'国家'});

    });
</script>