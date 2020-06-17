<?php

namespace App\Console\Commands;

use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test {function} {param1?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test';

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
        $p = $this->argument('function');
        $name = 'test' . $p;
        call_user_func([$this, $name]);
    }

    public function test1()
    {
        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => env('FFMPEG_BIN_PATH', '/usr/local/bin/ffmpeg'),
            'ffprobe.binaries' => env('FFPROBE_BIN_PATH', '/usr/local/bin/ffprobe')
        ]);
        $ffprobe = FFProbe::create([
            'ffmpeg.binaries' => env('FFMPEG_BIN_PATH', '/usr/local/bin/ffmpeg'),
            'ffprobe.binaries' => env('FFPROBE_BIN_PATH', '/usr/local/bin/ffprobe')
        ]);
        $inputVideo = storage_path('app/asset/2020050815889320075eb52da7e852b.png');
        $outputVideo = storage_path('app/asset/test26.png');
        $output = [];
        // ffprobe -hide_banner -v quiet -print_format json -show_format -show_streams
        // exec("ffmpeg -i $inputVideo -b 1000000 $outputVideo");
        // exec("ffmpeg -i $inputVideo -c:v libx264 -crf 30 -c:a aac $outputVideo");
        // exec("ffmpeg -i $inputVideo  -crf 30  $outputVideo", $output);
        // exec("ffmpeg  -hide_banner -i $inputVideo   -pix_fmt rgb24  $outputVideo");
        exec("ffmpeg  -i $inputVideo   -pix_fmt rgba  $outputVideo");
        return $outputVideo;
    }

    public function test2()
    {
        $res = DB::table('a_asset')->select('id', 'spec')->whereNull('spec->duration')->orderBy('spec->size', 'desc')->toSql();
        dd($res);
    }
}
