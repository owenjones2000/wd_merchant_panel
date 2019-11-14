<?php

namespace App\Http\Controllers\Advertise;

use App\Models\Advertise\App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('advertise.app.index');
    }

    public function data(Request $request)
    {
        $app_query = App::query();
        if(!empty($request->get('name'))){
            $app_query->where('name', 'like', '%'.$request->get('name').'%');
        }
        $res = $app_query->orderBy($request->get('field','status'),$request->get('order','desc'))->orderBy('name','asc')->paginate($request->get('limit',30))->toArray();

        $data = [
            'code' => 0,
            'msg'   => '正在请求中...',
            'count' => $res['total'],
            'data'  => $res['data']
        ];
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('advertise.app.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'  => 'required|string|unique:app,name',
            'bundle_id'  => 'required|unique:app,bundle_id',
        ]);
        $create_arr = $request->all();
        $create_arr['status'] = isset($create_arr['status']) ? 1 : 0;
        if (App::create($create_arr)){
            return redirect(route('advertise.app.create'))->with(['status'=>'添加完成']);
        }
        return redirect(route('advertise.app.create'))->with(['status'=>'系统错误']);
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
        $apps = App::findOrFail($id);
        return view('advertise.app.edit',compact('apps'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'name'  => 'required|string|unique:app,name,'.$id,
            'bundle_id'  => 'required|unique:app,bundle_id,'.$id,
        ]);
        $apps = App::findOrFail($id);
        $update_arr = $request->only(['name','bundle_id', 'os', 'status']);
        $update_arr['status'] = isset($update_arr['status']) ? 1 : 0;
        if ($apps->update($update_arr)){
            return redirect(route('advertise.app.edit', [$id]))->with(['status'=>'更新成功']);
        }
        return redirect(route('advertise.app.edit', [$id]))->withErrors(['status'=>'系统错误']);
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
        if (App::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }
}
