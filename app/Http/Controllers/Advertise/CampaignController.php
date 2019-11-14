<?php

namespace App\Http\Controllers\Advertise;

use App\Models\Advertise\App;
use App\Models\Advertise\Bid;
use App\Models\Advertise\Campaign;
use App\Models\Advertise\DailyBudget;
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
    public function index()
    {
        return view('advertise.campaign.index');
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $apps = App::query()->where('main_user_id', Auth::user()->getMainId())->get();
        return view('advertise.campaign.create', compact('apps'));
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
            'name'  => 'required|string|unique:campaign,name',
        ]);
        $create_arr = $request->all();
        $create_arr['status'] = isset($create_arr['status']) ? 1 : 0;
        $create_arr['main_user_id'] = Auth::user()->getMainId();
        if (Campaign::create($create_arr)){
            return redirect(route('advertise.campaign.create'))->with(['status'=>'添加完成']);
        }
        return redirect(route('advertise.campaign.create'))->with(['status'=>'系统错误']);
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
        /** @var Campaign $campaign */
        $campaign = Campaign::query()->where(['id' => $id, 'main_user_id' => Auth::user()->getMainId()])->firstOrFail();
        //$campaign->dailyBudgets()->save(new DailyBudget());
        var_dump($campaign['bids']->toArray());
        die;
        $apps = App::query()->where('main_user_id', Auth::user()->getMainId())->get();
        return view('advertise.campaign.edit',compact('campaign', 'apps'));
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
            'name'  => 'required|string|unique:campaign,name,'.$id,
        ]);
        $campaign = Campaign::query()->where(['id' => $id, 'main_user_id' => Auth::user()->getMainId()])->firstOrFail();
        $update_arr = $request->only(['name','app_id', 'status']);
        $update_arr['status'] = isset($update_arr['status']) ? 1 : 0;
        if ($campaign->update($update_arr)){
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
