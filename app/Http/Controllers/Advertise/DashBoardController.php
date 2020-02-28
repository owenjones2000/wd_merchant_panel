<?php

namespace App\Http\Controllers\Advertise;

use App\Models\Advertise\AdvertiseKpi;
use App\Models\Advertise\App;
use App\Models\Advertise\Campaign;
use App\Models\Advertise\Region;
use App\Rules\AdvertiseName;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashBoardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function view()
    {
        return view('advertise.dashboard.view');
    }

    public function data(Request $request)
    {
        $range_date = $request->get('range_date', null);
        if($range_date != 'now' && $range_date != null){
            $range_date = explode(' ~ ',$request->get('range_date'));
        }
        $start_date = date('Ymd', strtotime($range_date[0]??'-7 day'));
        $end_date = date('Ymd', strtotime($range_date[1]??'now'));
        $campaign_base_query = Campaign::query();

        $campaign_id_query = clone $campaign_base_query;
        $campaign_id_query->select('id');
        $advertise_kpi_query = AdvertiseKpi::multiTableQuery(function($query) use($start_date, $end_date, $campaign_id_query){
            $query->whereBetween('date', [$start_date, $end_date])
                ->whereIn('campaign_id', $campaign_id_query)
                ->select(['impressions', 'clicks', 'installations', 'spend',
                    'date'
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
            'date',
        ]);
        $advertise_kpi_query->groupBy('date');

        $advertise_kpi_list = $advertise_kpi_query
            ->orderBy('date','desc')
            ->get()
            ->toArray();
        if ($range_date == 'now') {
            $result = $advertise_kpi_list[0] ?? [];
        } else {
            $result = $advertise_kpi_list;
        }

        $data = [
            'code' => 0,
            'msg'   => '正在请求中...',
            'count' => count($advertise_kpi_list),
            'data'  => $result
        ];
        return response()->json($data);
    }
}
