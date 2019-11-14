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
    public function index($campaign_id)
    {
        $campaign = Campaign::findOrFail($campaign_id);
        return view('advertise.campaign.ad.index', compact('campaign'));
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($campaign_id)
    {
        $campaign = Campaign::findOrFail($campaign_id);
        return view('advertise.campaign.ad.create', compact('campaign'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $campaign_id)
    {
        $this->validate($request,[
            'name'  => 'required|string|unique:ad,name',
        ]);
        $create_arr = $request->all();
        $create_arr['status'] = isset($create_arr['status']) ? 1 : 0;
        $campaign = Campaign::findOrFail($campaign_id);
        $create_arr['campaign_id'] = $campaign['id'];
        if (Ad::create($create_arr)){
            return redirect(route('advertise.campaign.ad.create', [$campaign_id]))->with(['status'=>'添加完成']);
        }
        return redirect(route('advertise.campaign.ad.create', [$campaign_id]))->with(['status'=>'系统错误']);
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
    public function edit($campaign_id, $id)
    {
        $ad = Ad::query()->where(['id' => $id, 'campaign_id' => $campaign_id])->firstOrFail();
        return view('advertise.campaign.ad.edit',compact('ad'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $campaign_id, $id)
    {
        $this->validate($request,[
            'name'  => 'required|string|unique:ad,name,'.$id,
        ]);
        $ad = Ad::query()->where(['id' => $id, 'campaign_id' => $campaign_id])->firstOrFail();
        $update_arr = $request->only(['name','bundle_id', 'os', 'status']);
        $update_arr['status'] = isset($update_arr['status']) ? 1 : 0;
        if ($ad->update($update_arr)){
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
