<script>
    //编辑关闭父窗口
    $('.close-iframe').on('click',function () {
        var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
        parent.layer.close(index); //再执行关闭
    });

    @if(session('status'))
        console.log(window.name);
        var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
    console.log(index);
        parent.layer.close(index); //再执行关闭
        parent.layer.msg("{{session('status')}}",{icon:6});
    @endif
</script>