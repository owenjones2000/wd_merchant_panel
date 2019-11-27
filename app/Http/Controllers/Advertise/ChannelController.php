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
    public function list($campaign_id)
    {
        $campaign = Campaign::findOrFail($campaign_id);
        return view('advertise.campaign.channel.list', compact('campaign'));
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
        $advertise_kpi_query = AdvertiseKpi::multiTableQuery(function($query) use($start_date, $end_date, $channel_id_query){
            $query->whereBetween('date', [$start_date, $end_date])
                ->whereIn('target_app_id', $channel_id_query);
            return $query;
        }, $start_date, $end_date);

        $advertise_kpi_query->select([
            DB::raw('sum(impressions) as impressions'),
            DB::raw('sum(clicks) as clicks'),
            DB::raw('sum(installations) as installs'),
            DB::raw('round(sum(spend), 2) as spend'),
            DB::raw('round(sum(spend) * 1000 / sum(installations), 2) as ecpi'),
            DB::raw('round(sum(spend) * 1000 / sum(impressions), 2) as ecpm'),
            'target_app_id',
        ]);
        $advertise_kpi_query->groupBy('target_app_id');

        $advertise_kpi_list = $advertise_kpi_query
            ->orderBy('spend','desc')
            ->get()
            ->keyBy('target_app_id')
            ->toArray();
        $order_by_ids = implode(',', array_reverse(array_keys($advertise_kpi_list)));
        if(!empty($order_by_ids)){
            $channel_base_query->orderByRaw(DB::raw("FIELD(id,{$order_by_ids}) desc"));
        }
        $channel_list = $channel_base_query->orderBy($request->get('field','id'),$request->get('order','desc'))
            ->paginate($request->get('limit',30))
            ->toArray();

        foreach($channel_list['data'] as &$channel){
            if(isset($advertise_kpi_list[$channel['id']])){
                $channel = array_merge($channel, $advertise_kpi_list[$channel['id']]);
            }
        }
        $data = [
            'code' => 0,
            'msg'   => '正在请求中...',
            'count' => $channel_list['total'],
            'data'  => $channel_list['data']
        ];
        return response()->json($data);
    }
}
