<?php

namespace App\Http\Controllers\Advertise;

use App\Models\Advertise\App;
use App\Models\Advertise\Ad;
use App\Models\Advertise\Campaign;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list($campaign_id)
    {
        $campaign = Campaign::findOrFail($campaign_id);
        return view('advertise.campaign.ad.list', compact('campaign'));
    }

    public function data(Request $request, $campaign_id)
    {
        $ad_query = Ad::query()->where('campaign_id', $campaign_id);
        if(!empty($request->get('name'))){
            $ad_query->where('name', 'like', '%'.$request->get('name').'%');
        }
        $res = $ad_query->orderBy($request->get('field','status'),$request->get('order','desc'))->orderBy('name','asc')->paginate($request->get('limit',30))->toArray();

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
    public function edit($campaign_id, $id = null)
    {
        if($id == null){
            $ad = new Ad();
            $ad['campaign_id'] = $campaign_id;
        }else{
            $ad = Ad::query()->where(['id' => $id, 'campaign_id' => $campaign_id])->firstOrFail();
        }
        return view('advertise.campaign.ad.edit',compact('ad'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request, $campaign_id, $id = null)
    {
        $this->validate($request,[
            'name'  => 'required|string|unique:campaign,name,'.$id,
        ]);
        $params = $request->all();
        $params['id'] = $id;
        $params['status'] = isset($params['status']) ? 1 : 0;
        $params['campaign_id'] = $campaign_id;
        $ad = Ad::Make(Auth::user(), $params);
        if ($ad){
            return redirect(route('advertise.campaign.ad.edit', [$ad['campaign_id'], $ad['id']]))->with(['status'=>'更新成功']);
        }
        return redirect(route('advertise.campaign.ad.edit', [$ad['campaign_id'], $ad['id']]))->withErrors(['status'=>'系统错误']);
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
        if (Ad::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }
}
