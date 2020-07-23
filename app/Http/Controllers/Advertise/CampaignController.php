<?php

namespace App\Http\Controllers\Advertise;

use App\Models\Advertise\AdvertiseKpi;
use App\Models\Advertise\App;
use App\Models\Advertise\Campaign;
use App\Models\Advertise\State;
use App\Models\Advertise\Region;
use App\Rules\AdvertiseName;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Advertise\Ad;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Dcat\EasyExcel\Excel;

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

    public function performance()
    {
        return view('advertise.campaign.performance');
    }

    public function performanceData(Request $request)
    {
        // dd($request->all());
        $data = $request->input('params');
        if (isset($data['rangedate'])) {
            $range_date = explode(' ~ ', $data['rangedate']);
        }
        $start_date = date('Ymd', strtotime($range_date[0] ?? 'now'));
        $end_date = date('Ymd', strtotime($range_date[1] ?? 'now'));
        unset($data['rangedate']);
        $group = $data;
        $groupby = [];
        if ($group) {
            $groupby =  array_keys($group);
        }

        // dd($data,$start_date, $end_date,$groupby);
        $campaign_id_query = Campaign::query()->select('id');
        $advertise_kpi_query = AdvertiseKpi::multiTableQuery(function ($query) use ($start_date, $end_date, $campaign_id_query) {
            $query->whereBetween('date', [$start_date, $end_date])
                ->whereIn('campaign_id', $campaign_id_query);
            return $query;
        }, $start_date, $end_date);

        $advertise_kpi_query->join('a_app', 'a_app.id', '=', 'z_sub_tasks.app_id')
        ->select([
            DB::raw('sum(impressions) as impressions'),
            DB::raw('sum(clicks) as clicks'),
            DB::raw('sum(installations) as installs'),
            DB::raw('round(sum(clicks) * 100 / sum(impressions), 2) as ctr'),
            DB::raw('round(sum(installations) * 100 / sum(clicks), 2) as cvr'),
            DB::raw('round(sum(installations) * 100 / sum(impressions), 2) as ir'),
            DB::raw('round(sum(spend), 2) as spend'),
            DB::raw('round(sum(spend) / sum(installations), 2) as ecpi'),
            DB::raw('round(sum(spend) * 1000 / sum(impressions), 2) as ecpm'),
            'z_sub_tasks.campaign_id',
            'ad_id',
            'z_sub_tasks.app_id',
            'date',
            'country',
            'a_app.os'
        ]);
        if ($groupby) {
            $advertise_kpi_query->groupBy(...$groupby);
        }
        $advertise_kpi_list = $advertise_kpi_query->orderBy('spend', 'desc')->paginate($request->get('limit', 30))->toArray();
        $campaigns = Campaign::all()->pluck('name', 'id');
        $ads = Ad::query()->whereIn('campaign_id', $campaign_id_query)->pluck('name', 'id');
        foreach ($advertise_kpi_list['data'] as $key => &$kpi) {
            $kpi['campaign'] = $campaigns[$kpi['campaign_id']];
            $kpi['ad'] = $ads[$kpi['ad_id']];
            $kpi['ir'] = $kpi['ir'] . '%';
            $kpi['ctr'] = $kpi['ctr'] . '%';
            $kpi['cvr'] = $kpi['cvr'] . '%';
        }
        $data = [
            'code' => 0,
            'msg'   => '正在请求中...',
            'count' => $advertise_kpi_list['total'],
            'data'  => $advertise_kpi_list['data']
        ];
        return response()->json($data);
    }
    public function performanceExport(Request $request)
    {
        // dd($request->all());
        if (!empty($request->get('rangedate'))) {
            $range_date = explode(' ~ ', $request->get('rangedate'));
        }
        $start_date = date('Ymd', strtotime($range_date[0] ?? 'now'));
        $end_date = date('Ymd', strtotime($range_date[1] ?? 'now'));
        $group = $request->except('rangedate');
        $groupby = [];
        if ($group){
            $groupby =  array_keys($group);
        }
        
        // dd($start_date, $end_date,$groupby);
        $campaign_id_query = Campaign::query()->select('id');
        $advertise_kpi_query = AdvertiseKpi::multiTableQuery(function ($query) use ($start_date, $end_date, $campaign_id_query) {
            $query->whereBetween('date', [$start_date, $end_date])
                ->whereIn('campaign_id', $campaign_id_query)
                ;
            return $query;
        }, $start_date, $end_date);

        $advertise_kpi_query->join('a_app', 'a_app.id', '=', 'z_sub_tasks.app_id')
        ->select([
            DB::raw('sum(impressions) as impressions'),
            DB::raw('sum(clicks) as clicks'),
            DB::raw('sum(installations) as installs'),
            DB::raw('round(sum(clicks) * 100 / sum(impressions), 2) as ctr'),
            DB::raw('round(sum(installations) * 100 / sum(clicks), 2) as cvr'),
            DB::raw('round(sum(installations) * 100 / sum(impressions), 2) as ir'),
            DB::raw('round(sum(spend), 2) as spend'),
            DB::raw('round(sum(spend) / sum(installations), 2) as ecpi'),
            DB::raw('round(sum(spend) * 1000 / sum(impressions), 2) as ecpm'),
            'z_sub_tasks.campaign_id',
            'ad_id',
            'z_sub_tasks.app_id',
            'date',
            'country',
            'a_app.os'
        ]);
        if ($groupby){
            $advertise_kpi_query->groupBy(...$groupby);
        }
        $advertise_kpi_list = $advertise_kpi_query->orderBy('spend', 'desc')->get()->toArray();
        $campaigns = Campaign::all()->pluck('name', 'id');
        $ads = Ad::query()->whereIn('campaign_id', $campaign_id_query)->pluck('name', 'id');
        foreach ($advertise_kpi_list as $key => &$kpi) {
            $kpi['campaign'] = $campaigns[$kpi['campaign_id']];
            $kpi['ad'] = $ads[$kpi['ad_id']];
            $kpi['ir'] = $kpi['ir'].'%';
            $kpi['ctr'] = $kpi['ctr'].'%';
            $kpi['cvr'] = $kpi['cvr'].'%';
        }

        $headings = [
            // 'date'    => 'Day',
            // 'campaign' => 'Campaign',
            // 'ad'  => 'Ad',
            // 'os'  => 'Platform',
            // 'country' => 'Country',
            'impressions' => 'Impressions',
            'clicks' => 'Clicks',
            'installs' => 'Installs',
            'ctr' => 'Ctr',
            // 'cvr' => 'Cvr',
            'ir' => 'Ir',
            'ecpi' => 'Ecpi',
            // 'ecpm' => 'Ecpm',
            'spend' => 'Spend',
        ];
        $headings = array_reverse($headings);
        if (in_array('country', $groupby)){
            $headings['country'] = 'Country';
        }
        if (in_array('os', $groupby)){
            $headings['os'] = 'Platform';
        }
        if (in_array('ad_id', $groupby)){
            $headings['ad'] = 'Ad';
        }
        if (in_array('campaign_id', $groupby)){
            $headings['campaign'] = 'Campaign';
        }
        if (in_array('date', $groupby)){
            $headings['date'] = 'Day';
        }
        $headings = array_reverse($headings);
        // dd($advertise_kpi_list);
        Excel::export($advertise_kpi_list)->headings($headings)->download($request->get('rangedate') .'.csv');
    }
    public function data(Request $request)
    {
        if(!empty($request->get('rangedate'))){
            $range_date = explode(' ~ ',$request->get('rangedate'));
        }
        $start_date = date('Ymd', strtotime($range_date[0]??'now'));
        $end_date = date('Ymd', strtotime($range_date[1]??'now'));
        $order_by = explode('.', $request->get('field', 'status'));
        $order_sort = $request->get('order', 'desc') ?: 'desc';

        $campaign_base_query = Campaign::query();
        if(!empty($request->get('keyword'))){
            $like_keyword = '%'.$request->get('keyword').'%';
            $campaign_base_query->where('name', 'like', $like_keyword);
        }
        if(!empty($request->get('platform'))){
            $platform  = $request->get('platform');
            $campaign_base_query->whereHas('app', function($query)use ($platform){
                $query->where('os', $platform);
            });
        }

        $campaign_id_query = clone $campaign_base_query;
        $campaign_id_query->select('id');
        $advertise_kpi_query = AdvertiseKpi::multiTableQuery(function($query) use($start_date, $end_date, $campaign_id_query){
            $query->whereBetween('date', [$start_date, $end_date])
                ->whereIn('campaign_id', $campaign_id_query)
                ->select(['impressions', 'clicks', 'installations', 'spend',
                    'date', 'campaign_id',
                ]);
            return $query;
        }, $start_date, $end_date);

        $advertise_kpi_query->select([
            DB::raw('sum(impressions) as impressions'),
            DB::raw('sum(clicks) as clicks'),
            DB::raw('sum(installations) as installs'),
            DB::raw('round(sum(clicks) * 100 / sum(impressions), 2) as ctr'),
            DB::raw('round(sum(installations) * 100 / sum(clicks), 2) as cvr'),
            DB::raw('round(sum(installations) * 100 / sum(impressions), 2) as ir'),
            DB::raw('round(sum(spend), 2) as spend'),
            DB::raw('round(sum(spend) / sum(installations), 2) as ecpi'),
            DB::raw('round(sum(spend) * 1000 / sum(impressions), 2) as ecpm'),
            'campaign_id',
        ]);
        $advertise_kpi_query->groupBy('campaign_id');
        if($order_by[0] === 'kpi' && isset($order_by[1])){
            $advertise_kpi_query->orderBy($order_by[1], $order_sort);
        }

        $advertise_kpi_list = $advertise_kpi_query
            ->orderBy('spend','desc')
            ->get()
            ->keyBy('campaign_id')
            ->toArray();
        $order_by_ids = implode(',', array_reverse(array_keys($advertise_kpi_list)));
        $campaign_query = clone $campaign_base_query;
        $campaign_query->with('app');
        if(!empty($order_by_ids)){
            $campaign_query->orderByRaw(DB::raw("FIELD(id,{$order_by_ids}) desc"));
        }
        if($order_by[0] !== 'kpi'){
            $campaign_query->orderBy($order_by[0], $order_sort);
        }
        $campaign_list = $campaign_query->paginate($request->get('limit',30))
            ->toArray();

        foreach($campaign_list['data'] as &$campaign){
            if(isset($advertise_kpi_list[$campaign['id']])){
                $campaign['kpi'] = $advertise_kpi_list[$campaign['id']];
            }
        }
        $data = [
            'code' => 0,
            'msg'   => '正在请求中...',
            'count' => $campaign_list['total'],
            'data'  => $campaign_list['data']
        ];
        return response()->json($data);
    }


    public function alldata(Request $request)
    {
        if (!empty($request->get('rangedate'))) {
            $range_date = explode(' ~ ', $request->get('rangedate'));
        }
        $start_date = date('Ymd', strtotime($range_date[0] ?? 'now'));
        $end_date = date('Ymd', strtotime($range_date[1] ?? 'now'));
        $order_by = explode('.', $request->get('field', 'status'));
        $order_sort = $request->get('order', 'desc') ?: 'desc';

        $campaign_base_query = Campaign::query();
        if (!empty($request->get('keyword'))) {
            $like_keyword = '%' . $request->get('keyword') . '%';
            $campaign_base_query->where('name', 'like', $like_keyword);
        }
        if (!empty($request->get('platform'))) {
            $platform  = $request->get('platform');
            $campaign_base_query->whereHas('app', function ($query) use ($platform) {
                $query->where('os', $platform);
            });
        }

        $campaign_id_query = clone $campaign_base_query;
        $campaign_id_query->select('id');
        $advertise_kpi_query = AdvertiseKpi::multiTableQuery(function ($query) use ($start_date, $end_date, $campaign_id_query) {
            $query->whereBetween('date', [$start_date, $end_date])
                ->whereIn('campaign_id', $campaign_id_query)
                ->select([
                    'impressions', 'clicks', 'installations', 'spend',
                    'date', 'campaign_id',
                ]);
            return $query;
        }, $start_date, $end_date);

        $advertise_kpi_query->select([
            DB::raw('sum(impressions) as impressions'),
            DB::raw('sum(clicks) as clicks'),
            DB::raw('sum(installations) as installs'),
            DB::raw('round(sum(clicks) * 100 / sum(impressions), 2) as ctr'),
            DB::raw('round(sum(installations) * 100 / sum(clicks), 2) as cvr'),
            DB::raw('round(sum(installations) * 100 / sum(impressions), 2) as ir'),
            DB::raw('round(sum(spend), 2) as spend'),
            DB::raw('round(sum(spend) / sum(installations), 2) as ecpi'),
            DB::raw('round(sum(spend) * 1000 / sum(impressions), 2) as ecpm'),
            'campaign_id',
        ]);
        $advertise_kpi_query->groupBy('campaign_id');
        if ($order_by[0] === 'kpi' && isset($order_by[1])) {
            $advertise_kpi_query->orderBy($order_by[1], $order_sort);
        }

        $advertise_kpi_list = $advertise_kpi_query
            ->orderBy('spend', 'desc')
            ->get()
            ->keyBy('campaign_id')
            ->toArray();
        $order_by_ids = implode(',', array_reverse(array_keys($advertise_kpi_list)));
        $campaign_query = clone $campaign_base_query;
        $campaign_query->with('app');
        if (!empty($order_by_ids)) {
            $campaign_query->orderByRaw(DB::raw("FIELD(id,{$order_by_ids}) desc"));
        }
        if ($order_by[0] !== 'kpi') {
            $campaign_query->orderBy($order_by[0], $order_sort);
        }
        $campaign_list = $campaign_query->get()
        ->toArray();

        foreach ($campaign_list as &$campaign) {
            if (isset($advertise_kpi_list[$campaign['id']])) {
                $campaign['kpi'] = $advertise_kpi_list[$campaign['id']];
            }
        }
        $data = [
            'code' => 0,
            'data'  => $campaign_list
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
            $campaign = Campaign::findOrFail($id);
        }
        $mainUserId = Auth::user()->getMainId();
        $apps = App::query()
            ->where('main_user_id', Auth::user()->getMainId())
            ->get();
        $regions = Region::query()->orderBy('sort', 'desc')->get();
        $states = State::query()->get();
        return view('advertise.campaign.edit',compact('campaign', 'apps', 'regions', 'states', 'mainUserId'));
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
            'name'  => ['required','string','max:100','unique:a_campaign,name,'.$id, new AdvertiseName()],
            'app_id' => 'exists:a_app,id',
            'regions' => 'string',
            'budget' => 'array',
            'budget.*.region_code' => 'required|string|max:3',
            'budget.*.amount' => 'numeric',
            'bid_by_region' => 'bool',
            'bid' => 'array',
            'bid.*.region_code' => 'required|string|max:3',
            'bid.*.amount' => 'numeric',
            'audience.gender' => 'in:0,1,2',
            'audience.adult' => 'bool',

        ]);
        $params = $request->all();
        $params['id'] = $id;
//        $params['status'] = isset($params['status']) ? 1 : 0;
        if (Campaign::Make(Auth::user(), $params)){
            return redirect(route('advertise.campaign.edit', [$id]))->with(['status'=>'Save successfully.']);
        }
        return redirect(route('advertise.campaign.edit', [$id]))->withErrors(['status'=>'Error']);
    }

    /**
     * 启动
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function enable($id)
    {
        try {
            /** @var Campaign $campaign */
            $campaign = Campaign::findOrFail($id);
            $campaign->enable();
            return response()->json(['code' => 0, 'msg' => 'Enabled']);
        } catch (\Exception $ex) {
            return response()->json(['code' => -1, 'msg' => $ex->getMessage()]);
        }
    }

    /**
     * 停止
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function disable($id)
    {
        /** @var Campaign $campaign */
        $campaign = Campaign::findOrFail($id);
        $campaign->disable();
        return response()->json(['code'=>0,'msg'=>'Disabled']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function destroy(Request $request)
//    {
//        $ids = $request->get('ids');
//        if (empty($ids)){
//            return response()->json(['code'=>1,'msg'=>'请选择删除项']);
//        }
//        if (Campaign::destroy($ids)){
//            return response()->json(['code'=>0,'msg'=>'删除成功']);
//        }
//        return response()->json(['code'=>1,'msg'=>'删除失败']);
//    }
}
