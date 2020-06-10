<?php

namespace App\Console\Commands;

use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Illuminate\Console\Command;

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
        $inputVideo = public_path('a.mp4');
        $outputVideo = public_path('a31.mp4');
        $output = [];
        $video_info = $ffprobe->streams($outputVideo)->videos()->first()->all();dd($video_info);
        exec("ffmpeg -i $inputVideo -b 1000000 $outputVideo");
        // exec("ffmpeg -i $inputVideo -c:v libx264 -crf 30 -c:a aac $outputVideo");
        // exec("ffmpeg -i $inputVideo  -crf 30  $outputVideo", $output);
        return $outputVideo;
    }
}
