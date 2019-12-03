<?php

namespace App\Http\Controllers\Advertise;

use App\Models\Advertise\AdvertiseKpi;
use App\Models\Advertise\App;
use App\Models\Advertise\Ad;
use App\Models\Advertise\Campaign;
use App\Models\Advertise\Channel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChannelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request, $campaign_id)
    {
        $rangedate = $request->input('rangedate', date('Y-m-d ~ Y-m-d'));
        $campaign = Campaign::findOrFail($campaign_id);
        return view('advertise.campaign.channel.list', compact('campaign', 'rangedate'));
    }

    public function data(Request $request, $campaign_id)
    {
        if(!empty($request->get('rangedate'))){
            $range_date = explode(' ~ ',$request->get('rangedate'));
        }
        $start_date = date('Ymd', strtotime($range_date[0]??'now'));
        $end_date = date('Ymd', strtotime($range_date[1]??'now'));
        $channel_base_query = Channel::query();
        if(!empty($request->get('name'))){
            $channel_base_query->where('name', 'like', '%'.$request->get('name').'%');
        }
        $channel_id_query = clone $channel_base_query;
        $channel_id_query->select('id');
        $advertise_kpi_query = AdvertiseKpi::multiTableQuery(function($query) use($start_date, $end_date, $channel_id_query, $campaign_id){
            $query->whereBetween('date', [$start_date, $end_date])
                ->where('campaign_id', $campaign_id)
                ->whereIn('target_app_id', $channel_id_query);
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
            'target_app_id',
        ]);
        $advertise_kpi_query->groupBy('target_app_id');

        $advertise_kpi_list = $advertise_kpi_query
            ->with('channel')
            ->orderBy('spend','desc')
            ->paginate($request->get('limit',30))
            ->toArray();

        $data = [
            'code' => 0,
            'msg'   => '正在请求中...',
            'count' => $advertise_kpi_list['total'],
            'data'  => $advertise_kpi_list['data']
        ];
        return response()->json($data);
    }
}
