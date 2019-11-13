<?php

namespace App\Http\Controllers\Home;

use App\Models\Icon;
use App\Models\Permission;
use App\Models\Role;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    /**
     * 后台布局
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function layout()
    {
        return view('home.layout');
    }

    /**
     * 后台首页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('home.index.index');
    }

    public function index1()
    {
        return view('home.index.index1');
    }
    
    public function index2()
    {
        return view('home.index.index2');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 数据表格接口
     */
    public function data(Request $request)
    {
        $model = $request->get('model');
        $hidden_ids = [1];
        switch (strtolower($model)) {
            case 'user':
                $query = new User();
                if(!in_array($request->user()->id, $hidden_ids)){
                    $query = $query->whereNotIn('id', $hidden_ids);
                }
                break;
            case 'role':
                $query = new Role();
                if(!in_array($request->user()->id, $hidden_ids)){
                    $query = $query->whereNotIn('id', [1]);
                }
                break;
            case 'permission':
                $query = new Permission();
                if(!in_array($request->user()->id, $hidden_ids)){
                    $query = $query->whereNotIn('id', [1]);
                }
                $query = $query->where('parent_id', $request->get('parent_id', 0))->with('icon');
                break;
            default:
                $query = new User();
                break;
        }
        $res = $query->paginate($request->get('limit', 30))->toArray();
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res['total'],
            'data' => $res['data'],
            'parent_id'=>$request->get('parent_id', 0),
        ];
        return response()->json($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 所有icon图标
     */
    public function icons()
    {
        $icons = Icon::orderBy('sort', 'desc')->get();
        return response()->json(['code' => 0, 'msg' => '请求成功', 'data' => $icons]);
    }

}
