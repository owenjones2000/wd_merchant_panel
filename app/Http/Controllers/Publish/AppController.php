<?php

namespace App\Http\Controllers\Publish;

use App\Exceptions\BizException;
use App\Models\Advertise\AdvertiseKpi;
use App\Models\Advertise\App;
use App\Models\Advertise\Channel;
use App\Rules\AdvertiseName;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Advertise\Impression;
use App\Models\ChannelCpm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AppController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('publish.app.list');
    }

    public function data(Request $request)
    {
        if (!empty($request->get('rangedate'))) {
            $range_date = explode(' ~ ', $request->get('rangedate'));
        }
        $start_date = date('Ymd', strtotime($range_date[0] ?? 'now'));
        $end_date = date('Ymd', strtotime($range_date[1] ?? 'now'));
        $order_by = explode('.', $request->get('field', 'status'));
        $order_sort = $request->get('order', 'desc') ?: 'desc';

        $channel_base_query = Channel::query()->where('main_user_id', Auth::user()->getMainId());
        if (!empty($request->get('keyword'))) {
            $like_keyword = '%' . $request->get('keyword') . '%';
            $channel_base_query->where('name', 'like', $like_keyword);
        }
        if (!empty($request->get('platform'))) {
            $platform  = $request->get('platform');
            $channel_base_query->where('platform', $platform);

        }

        $channel_id_query = clone $channel_base_query;
        $channel_id_query->select('id');
        $advertise_kpi_query = AdvertiseKpi::multiTableQuery(function ($query) use ($start_date, $end_date, $channel_id_query) {
            $query->whereBetween('date', [$start_date, $end_date])
                ->whereIn('target_app_id', $channel_id_query)
                ->select([
                    'impressions', 'clicks', 'installations', 'spend',
                    'date', 'target_app_id',
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
            'target_app_id',
        ]);
        $advertise_kpi_query->groupBy('target_app_id');
        if ($order_by[0] === 'kpi' && isset($order_by[1])) {
            $advertise_kpi_query->orderBy($order_by[1], $order_sort);
        }

        $advertise_kpi_list = $advertise_kpi_query
            ->orderBy('spend', 'desc')
            ->get()
            ->keyBy('target_app_id')
            ->toArray();
        $order_by_ids = implode(',', array_reverse(array_keys($advertise_kpi_list)));
        $channel_query = clone $channel_base_query;
        if (!empty($order_by_ids)) {
            $channel_query->orderByRaw(DB::raw("FIELD(id,{$order_by_ids}) desc"));
        }
        if ($order_by[0] !== 'kpi') {
            $channel_query->orderBy($order_by[0], $order_sort);
        }
        $channel_list = $channel_query->paginate($request->get('limit', 30))
            ->toArray();

        //spend 从impression表取
        // $impression_query = Impression::multiTableQuery(function ($query) use ($start_date, $end_date, $channel_id_query) {
        //     $query->whereBetween('date', [$start_date, $end_date])
        //         ->whereIn('target_app_id', $channel_id_query)
        //         ->select([
        //             'ecpm',
        //             'target_app_id',
        //         ]);
        //     return $query;
        // }, $start_date, $end_date);
        // $impression_list = $impression_query->select([
        //     DB::raw('round(sum(ecpm)/1000, 2) as spend'),
        //     'target_app_id',
        // ])->groupBy('target_app_id')
        //     ->get()
        //     ->keyBy('target_app_id')
        //     ->toArray();

        $impression_list = ChannelCpm::whereBetween('date', [$start_date, $end_date])
            ->whereIn('target_app_id', $channel_id_query)
            ->select([
                DB::raw('sum(cpm_revenue) as cpm'),
                'target_app_id',
            ])->groupBy('target_app_id')
            ->get()->keyBy('target_app_id')
            ->toArray();;

        foreach ($advertise_kpi_list as $key => &$kpi) {
            $kpi['spend'] = $impression_list[$key]['cpm'] ?? 0;
        }
        foreach ($channel_list['data'] as &$channel) {
            if (isset($advertise_kpi_list[$channel['id']])) {
                $channel['kpi'] = $advertise_kpi_list[$channel['id']];
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
        if (empty($id)) {
            $apps = new Channel();
        } else {
            /** @var App $apps */
            $apps = Channel::findOrFail($id);
        }
        return view('publish.app.edit', compact('apps'));
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
        $this->validate($request, [
            'name'  => ['required', 'string', 'unique:a_target_apps,name,' . $id, new AdvertiseName()],
            'bundle_id'  => 'required|unique:a_target_apps,bundle_id,' . $id,
            'icon_url' => 'string|max:200',
        ]);
        try {
            $params = $request->all();
            $params['id'] = $id;
            Channel::Make(Auth::user(), $params);
            return redirect(route('publish.app.edit', [$id]))->with(['status' => 'Update successfully']);
        } catch (BizException $ex) {
            return redirect(route('publish.app.edit', [$id]))->withErrors(['status' => $ex->getMessage()]);
        }
    }

    /**
     * 上传Icon
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uplodeIcon(Request $request)
    {
        //返回信息json
        $file = $request->file('file');

        try {
            if (!$file->isValid()) {
                throw new \Exception($file->getErrorMessage());
            }
            $main_id = Auth::user()->getMainId();
            $dir = "icon/{$main_id}";
            $file_name = date('Ymd') . time() . uniqid() . "." . $file->getClientOriginalExtension();
            $path = Storage::putFileAs($dir, $file, $file_name);

            if ($path) {
                $data = [
                    'code'  => 0,
                    'msg'   => '上传成功',
                    'url' => Storage::url($path),
                ];
            } else {
                $data['msg'] = $file->getErrorMessage();
            }
            return response()->json($data);
        } catch (\Exception $ex) {
            $data = [
                'code' => 1,
                'msg' => $ex->getMessage()
            ];
            return response()->json($data);
        }
    }

    /**
     * 启动
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function enable($id)
    {
        /** @var Channel $apps */
        $apps = Channel::findOrFail($id);
        $apps->enable();
        return response()->json(['code' => 0, 'msg' => 'Successful']);
    }

    /**
     * 停止
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function disable($id)
    {
        /** @var Channel $apps */
        $apps = Channel::findOrFail($id);
        $apps->disable();
        return response()->json(['code' => 0, 'msg' => 'Successful']);
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
    //        if (Channel::destroy($ids)){
    //            return response()->json(['code'=>0,'msg'=>'删除成功']);
    //        }
    //        return response()->json(['code'=>1,'msg'=>'删除失败']);
    //    }
}
