<?php

namespace App\Http\Controllers\Advertise;

use App\Models\Advertise\App;
use App\Models\Advertise\Campaign;
use App\Models\Advertise\Region;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        return view('advertise.campaign.list');
    }

    public function data(Request $request)
    {
        $campaign_query = Campaign::query()->where('main_user_id', Auth::user()->getMainId());
        if(!empty($request->get('name'))){
            $campaign_query->where('name', 'like', '%'.$request->get('name').'%');
        }
        $res = $campaign_query->with('app')->orderBy($request->get('field','status'),$request->get('order','desc'))->orderBy('name','asc')->paginate($request->get('limit',30))->toArray();

        $data = [
            'code' => 0,
            'msg'   => '正在请求中...',
            'count' => $res['total'],
            'data'  => $res['data']
        ];
        return response()->json($data);
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
    public function edit($id = null)
    {
        if(empty($id)){
            $campaign = new Campaign();
        }else{
            /** @var Campaign $campaign */
            $campaign = Campaign::query()
                ->where(['id' => $id, 'main_user_id' => Auth::user()->getMainId()])
                ->firstOrFail();
        }

        $apps = App::query()
            ->where('main_user_id', Auth::user()->getMainId())
            ->get();
        $regions = Region::query()->get();
        return view('advertise.campaign.edit',compact('campaign', 'apps', 'regions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request, $id = null)
    {
        $this->validate($request,[
            'name'  => 'required|string|unique:a_campaign,name,'.$id,
        ]);
        $params = $request->all();
        $params['id'] = $id;
        $params['status'] = isset($params['status']) ? 1 : 0;
        if (Campaign::Make(Auth::user(), $params)){
            return redirect(route('advertise.campaign.edit', [$id]))->with(['status'=>'更新成功']);
        }
        return redirect(route('advertise.campaign.edit', [$id]))->withErrors(['status'=>'系统错误']);
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
        if (Campaign::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }
}
