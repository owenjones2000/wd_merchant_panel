<?php

namespace App\Jobs;

use App\Models\Advertise\Asset;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class CompressVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $inputVideo = public_path('a.mp4');
        $outputVideo = public_path('a31.mp4');
        $output = [];
        exec("ffmpeg -i $inputVideo  -crf 30  $outputVideo", $output);
        Log::info('exec');
    }
}
