<?php

namespace App\Http\Controllers\Advertise;

use App\Http\Controllers\Controller;
use App\Models\Advertise\AdType;
use App\Models\Advertise\Asset;
use App\Models\Advertise\AssetType;
use FFMpeg\FFProbe;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    public function processMediaFiles(Request $request)
    {
        //返回信息json
        $file = $request->file('file');

        try{
            if (!$file->isValid()){
                throw new \Exception($file->getErrorMessage());
            }
            $file_info = $this->decideAssetType($file);
            $ad_type = AdType::get($request->input('ad_type_id', null));
            if($ad_type != null){
                if(!in_array($file_info['type'], $ad_type['need_asset_type'])){
                    throw new \Exception('file type not support.');
                }
            }
            $main_id = Auth::user()->getMainId();
            $dir = "asset/{$main_id}";
            $file_name = date('Ymd').time().uniqid().".".$file->getClientOriginalExtension();
            $path = Storage::putFileAs($dir, $file, $file_name);


            $asset = Asset::create([
                'file_path' => $path,
                'type_id' => $file_info['type'],
                'spec' => $file_info
            ]);
            $asset['type'] = AssetType::get($asset['type_id']);

            if($path){
                $data = [
                    'code'  => 0,
                    'msg'   => '上传成功',
                    'asset' => $asset,
                ];
            }else{
                $data['msg'] = $file->getErrorMessage();
            }
            return response()->json($data);
        }catch (\Exception $ex){
            $data = [
                'code'=>1,
                'msg'=>$ex->getMessage()
            ];
            return response()->json($data);
        }
    }

    private function decideAssetType($file){
        $ffprobe = FFProbe::create([
            'ffmpeg.binaries'  => env('FFMPEG_BIN_PATH','/usr/local/bin/ffmpeg'),
            'ffprobe.binaries' => env('FFPROBE_BIN_PATH','/usr/local/bin/ffprobe')
        ]);
        $video_info = $ffprobe->streams($file)->videos()->first()->all();
        $width = Arr::get($video_info, 'width');
        $height = Arr::get($video_info, 'height');
        $duration = Arr::get($video_info, 'duration');
        if($width >= $height){
            $type = $duration > 15 ?
                AssetType::Landscape_Long : AssetType::Landscape_Short;
        }else{
            $type = $duration > 15 ?
                AssetType::Portrait_Long : AssetType::Portrait_Short;
        }
        return [
            'type' => $type,
            'width' => $width,
            'height' => $height,
            'duration' => $duration
        ];
    }
}
