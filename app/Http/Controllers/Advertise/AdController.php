<?php

namespace App\Http\Controllers\Advertise;

use App\Models\Advertise\AdvertiseKpi;
use App\Models\Advertise\App;
use App\Models\Advertise\Ad;
use App\Models\Advertise\Campaign;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $start_date = date('Ymd', strtotime('-8 day'));
        $end_date = date('Ymd');
        $ad_base_query = Ad::query()->where('campaign_id', $campaign_id);
        if(!empty($request->get('name'))){
            $ad_base_query->where('name', 'like', '%'.$request->get('name').'%');
        }
        $ad_id_query = clone $ad_base_query;
        $ad_id_query->select('id');
        $advertise_kpi_query = AdvertiseKpi::multiTableQuery(function($query) use($start_date, $end_date, $ad_id_query){
            $query->whereBetween('date', [$start_date, $end_date])
                ->whereIn('ad_id', $ad_id_query);
            return $query;
        }, $start_date, $end_date);

        $advertise_kpi_query->select([
            DB::raw('sum(impressions) as impressions'),
            DB::raw('sum(clicks) as clicks'),
            DB::raw('sum(installations) as installs'),
            DB::raw('round(sum(spend), 2) as spend'),
            DB::raw('round(sum(spend) * 1000 / sum(installations), 2) as ecpi'),
            DB::raw('round(sum(spend) * 1000 / sum(impressions), 2) as ecpm'),
            'ad_id',
        ]);
        $advertise_kpi_query->groupBy('ad_id');

        $advertise_kpi_list = $advertise_kpi_query
            ->orderBy('spend','desc')
            ->get()
            ->keyBy('ad_id')
            ->toArray();
        $order_by_ids = implode(',', array_reverse(array_keys($advertise_kpi_list)));
        $ad_list = $ad_base_query->with('campaign.app')
            ->orderByRaw(DB::raw("FIELD(id,{$order_by_ids}) desc"))
            ->orderBy($request->get('field','status'),$request->get('order','desc'))
            ->paginate($request->get('limit',30))
            ->toArray();

        foreach($ad_list['data'] as &$ad){
            if(isset($advertise_kpi_list[$ad['id']])){
                $ad = array_merge($ad, $advertise_kpi_list[$ad['id']]);
            }
        }
        $data = [
            'code' => 0,
            'msg'   => '正在请求中...',
            'count' => $ad_list['total'],
            'data'  => $ad_list['data']
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
            'name'  => 'required|string|unique:a_campaign,name,'.$id,
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
