<?php

namespace App\Http\Controllers\Home;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home.user.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('home.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserCreateRequest $request)
    {
        /** @var User $user */
        $op_user = Auth::user();
        if(!$op_user->isMainAccount()){
            return redirect()->to(route('home.user.create'))->withErrors('Permission denied');
        }
        $data =  $request->all();
        $data['uuid'] = \Faker\Provider\Uuid::uuid();
        $data['password_hash'] = Hash::make($data['password']);
        $result = DB::transaction(function () use($data, $op_user) {
            $user = User::firstOrNew(
                    ['username' => $data['username']],
                    $data
                );
            $user['main_user_id'] = $op_user->getMainId();
            $user['status'] = true;
            $user->saveOrFail();
            return true;
        }, 3);
        if ($result){

            return redirect()->to(route('home.user.create'))->with(['status'=>'Add user successful.']);
        }
        return redirect()->to(route('home.user.create'))->withErrors('Error');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('home.user.edit',compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, $id)
    {
        try{
            /** @var User $op_user */
            $op_user = Auth::user();
            DB::transaction(function () use($id, $request, $op_user) {
                $user = User::query()
                    ->where('id', $id)
                    ->where(function($query) use($id, $op_user) {
                        $query->where('main_user_id', $op_user['id'])
                            ->orWhere('id', $op_user['id']);
                    })->firstOrFail();
                $data = $request->except('password');
                if ($request->get('password')){
                    $data['password_hash'] = Hash::make($request->get('password'));
                }
                $user->update($data);
            }, 3);
            return redirect()->to(route('home.user.edit',[$id]))->with(['status'=>'更新用户成功']);
        }catch(\Exception $ex) {
            return redirect()->to(route('home.user.edit',[$id]))->withErrors('Permission denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return response()->json(['code'=>1,'msg'=>'请选择删除项']);
        }
        /** @var User $op_user */
        $op_user = Auth::user();
        $result = User::query()
            ->whereIn('id', $ids)
            ->where('main_user_id', $op_user['id'])->delete();
        if ($result){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }

    /**
     * 分配角色
     */
//    public function role(Request $request,$id)
//    {
//        $user = User::findOrFail($id);
//        if(in_array($request->user()->id, [1, 2])){
//            $roles = Role::get();
//        }else{
//            $roles = Role::query()->whereNotIn('id', [1, 6])->get();
//        }
//        $hasRoles = $user->roles();
//        foreach ($roles as $role){
//            $role->own = $user->hasRole($role) ? true : false;
//        }
//        return view('home.user.role',compact('roles','user'));
//    }

    /**
     * 更新分配角色
     */
//    public function assignRole(Request $request,$id)
//    {
//        $user = User::findOrFail($id);
//        $roles = $request->get('roles',[]);
//        if ($user->roles()->sync($roles)){
//           return redirect()->to(route('home.user.role',[$id]))->with(['status'=>'更新用户角色成功']);
//        }
//        return redirect()->to(route('home.user'))->withErrors('系统错误');
//    }

    /**
     * 分配权限
     */
    public function permission(Request $request,$id)
    {
        /** @var User $op_user */
        $op_user = Auth::user();
        $user = User::findOrFail($id);
        $permissions = $this->tree();
        foreach ($permissions as $key1 => &$item1){
            $permissions[$key1]['own'] = $user->hasDirectPermission($item1['id']) ? 'checked' : false ;
            if (isset($item1['_child'])){
                foreach ($item1['_child'] as $key2 => &$item2){
                    $permissions[$key1]['_child'][$key2]['own'] = $user->hasDirectPermission($item2['id']) ? 'checked' : false ;
                    if (isset($item2['_child'])){
                        foreach ($item2['_child'] as $key3 => $item3){
                            if(!empty($op_user['main_user_id']) || !empty($user['main_user_id'])){
                                if(in_array($item3['id'], [3, 5, 7])){
                                    unset($item2['_child'][$key3]);
                                    continue;
                                }
                            }
                            $permissions[$key1]['_child'][$key2]['_child'][$key3]['own'] = $user->hasDirectPermission($item3['id']) ? 'checked' : false ;
                        }
                    }
                }
            }
        }
        return view('home.user.permission',compact('user','permissions'));
    }

    /**
     * 存储权限
     */
    public function assignPermission(Request $request,$id)
    {
        /** @var User $op_user */
        $op_user = Auth::user();
        if(!empty($op_user['main_user_id'])){
            return redirect()->to(route('home.user.permission',[$id]))->withErrors('Permission denied.');
        }

        try{
            $user = User::query()
                ->where('id', $id)
                ->where(function($query) use($id, $op_user) {
                    $query->where('main_user_id', $op_user['id'])
                        ->orWhere('id', $op_user['id']);
                })->firstOrFail();
        }catch (\Exception $ex){
            return redirect()->to(route('home.user.permission',[$id]))->withErrors('Permission denied.');
        }


        $permissions = $request->get('permissions');

        if (empty($permissions)){
            $user->permissions()->detach();
            return redirect()->to(route('home.user.permission',[$id]))->with(['status'=>'Update permission successful.']);
        }
        $user->syncPermissions($permissions);
        return redirect()->to(route('home.user.permission',[$id]))->with(['status'=>'Update permission successful.']);
    }

}
