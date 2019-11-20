{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">User Name:</label>
    <div class="layui-input-inline">
        <input type="text" name="username" value="{{ $user->username ?? old('username') }}" lay-verify="required" placeholder="please input login account" autocomplete="username" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">Real Name:</label>
    <div class="layui-input-inline">
        <input type="text" name="realname" value="{{ $user->realname ?? old('realname') }}" lay-verify="required" placeholder="please input real name" autocomplete="off" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">Email:</label>
    <div class="layui-input-inline">
        <input type="email" name="email" value="{{$user->email??old('email')}}" lay-verify="email" placeholder="please input email" autocomplete="off" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">Phone:</label>
    <div class="layui-input-inline">
        <input type="text" name="phone" value="{{$user->phone??old('phone')}}" {{-- required="phone" lay-verify="phone" --}} placeholder="please input phone number" autocomplete="username" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">Password</label>
    <div class="layui-input-inline">
        <input type="password" name="password" placeholder="please input password" autocomplete="current-password" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">Confirm Password</label>
    <div class="layui-input-inline">
        <input type="password" name="password_confirmation" placeholder="please confirm password" autocomplete="new-password" class="layui-input">
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit="" lay-filter="formDemo">Save</button>
        <div  class="layui-btn layui-btn-primary close-iframe">Close</div>
    </div>
</div>
@section('script')
    @include('layout.common_edit')
@endsection
