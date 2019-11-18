{{csrf_field()}}

<div class="layui-form-item">
    <label for="" class="layui-form-label">Name</label>
    <div class="layui-input-block">
        <input type="text" name="name" value="{{ $ad->name ?? old('name') }}" lay-verify="required" placeholder="" autocomplete="off" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">Status</label>
    <div class="layui-input-block">
        <input type="checkbox" name="status" @if($ad->status??true) checked @endif lay-skin="switch" lay-filter="switchStatus" lay-text="Enable|Disable">
    </div>
</div>

<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit="">Save</button>
        <button class="layui-btn close-iframe">Cancel</button>
    </div>
</div>

@section('script')
    @include('layout.common_edit')
@endsection