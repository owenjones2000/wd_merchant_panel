<?php

namespace App\Console\Commands;

use App\Models\Advertise\Asset;
use App\Models\Advertise\AssetType;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CompressCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:compress';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'compress';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        Log::info('compress start');
        $assets = Asset::where('id', '>', 2000)->where('spec->size_per_second', '>', 450000)
            // ->limit(6)
            ->get();
        // dd( $assets->count(), app()->environment());
        $n = 0;
        foreach ($assets as $key => $asset) {
            if ($n >= 1) {
                break;
            }
            // dump($asset['hash'], md5_file(Storage::disk('local')->path($asset['spec']['file_path_compress'])));
            if (strpos($asset, 'mp4')) {
                $exist = Storage::disk('local')->exists($asset['file_path']);
                $ffprobe = FFProbe::create([
                    'ffmpeg.binaries' => env('FFMPEG_BIN_PATH', '/usr/local/bin/ffmpeg'),
                    'ffprobe.binaries' => env('FFPROBE_BIN_PATH', '/usr/local/bin/ffprobe')
                ]);
                $oldfile = Storage::disk('local')->path($asset['file_path']);
                if (!$exist) {
                    $save = Storage::disk('local')->put($asset['file_path'], file_get_contents($asset['url']));
                }
                // if (!isset($asset['spec']['bit_rate'])) {
                // dd($oldfile->getPath());
                // $video_info = $ffprobe->streams($oldfile)->videos()->first()->all();
                // dump($video_info);
                // $asset['spec'] =  array_merge($asset['spec'], [
                //     'bit_rate' => $video_info['bit_rate'],
                // ]);
                // }
                if (!isset($asset['spec']['size_per_second'])) {
                    $asset['spec'] =  array_merge($asset['spec'], [
                        'size_per_second' => round(filesize($oldfile) / round($asset['spec']['duration'], 1)),
                    ]);
                }
                if (!isset($asset['spec']['size'])) {
                    $asset['spec'] =  array_merge($asset['spec'], [
                        'size' => $this->fileSizeConvert(filesize($oldfile)),
                    ]);
                }
                $asset->save();
                // dump($asset->toArray());
                if (
                    !isset($asset['spec']['size_per_second_compress'])
                    // && app()->environment() == 'local'
                ) {
                    $file_name = date('Ymd') . time() . uniqid() . ".mp4";
                    $path = Storage::disk('local')->path('') . 'asset/';
                    $dir = 'asset/';
                    $newfile = $path . $file_name;
                    dump($asset->toArray());
                    exec("ffmpeg -y -i $oldfile -b:v 1200000 $newfile");
                    $upload = Storage::put($dir . $file_name, file_get_contents($newfile));
                    // dump($video_info['bit_rate'], $upload);
                    $asset['hash'] = md5_file($newfile);
                    $asset['url'] = Storage::url($dir . $file_name);
                    $asset['spec'] =  array_merge($asset['spec'], [
                        'size_per_second_compress' => round(filesize($newfile) / round($asset['spec']['duration'], 1)),
                        'size_compress' => $this->fileSizeConvert(filesize($newfile)),
                        'file_path_compress' => $dir . $file_name,
                    ]);
                    $asset->save();
                    $downloadfile = file_get_contents($asset['url']);
                    unset($downloadfile);
                    Log::info('compress' . $asset['id']);
                    // dd($asset->toArray());
                    $n++;
                }
            }
        }
        Log::info('compress end');
    }

    public function  fileSizeConvert($bytes)
    {
        $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );

        foreach ($arBytes as $arItem) {
            if ($bytes >= $arItem["VALUE"]) {
                $result = $bytes / $arItem["VALUE"];
                // $result = str_replace(".", ",", strval(round($result, 2))) . " " . $arItem["UNIT"];
                $result =  strval(round($result, 2)) . " " . $arItem["UNIT"];
                break;
            }
        }
        return $result;
    }
}
