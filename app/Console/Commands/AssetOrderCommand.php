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

class AssetOrderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:asset {action=order}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'asset';

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

        $action = $this->argument('action');
        if ($action == 'order') {
            Log::info('order start');
            $assets = Asset::whereNull('ad_id')
                // ->whereNull('spec->size')
                ->get();
            foreach ($assets as $key => $asset) {
                
                if (strpos($asset->url, 'mp4') || strpos($asset->url, 'jpg') || strpos($asset->url, 'png')) {
                    
                    $exist = Storage::disk('local')->exists($asset['file_path']);
                    // $oldfile = Storage::disk('local')->path($asset['file_path']);
                    if ($exist) {
                        $delete = Storage::disk('local')->delete($asset['file_path']);
                        dump($delete);
                        dump('old');
                    }

                    if (isset($asset['spec']['file_path_compress'])) {
                        $newExist = Storage::disk('local')->exists($asset['spec']['file_path_compress']);
                        if ($newExist) {
                            $delete = Storage::disk('local')->delete($asset['spec']['file_path_compress']);
                            dump($delete);
                            dump('new');
                        }
                    }
               
                    $asset->delete();
                    Log::info('delete' . $asset['id']);
                    dump('delete');
                    dump($asset->toArray());

                }

            }
            Log::info('detect end');
        }
    }

}
