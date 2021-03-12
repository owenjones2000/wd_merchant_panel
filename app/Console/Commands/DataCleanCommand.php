<?php

namespace App\Console\Commands;

use App\Models\Advertise\Asset;
use App\Models\Advertise\AssetType;
use Carbon\Carbon;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DataCleanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:data-clean  {day=8}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'clean';

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
        $day = $this->argument('day');
        Log::info('clean start');
        $date = Carbon::now()->subDays($day)->format('Ym/d');
        // $directory_output12 = 'wudiapps/stay-prediction-pipeline/lucky-money/12h-2d-stay/predset-emr-output/' . $date;
        // $directory_output24 = 'wudiapps/stay-prediction-pipeline/lucky-money/24h-2d-stay/predset-emr-output/' . $date;
        // $directory_outpredresult12 = 'wudiapps/stay-prediction-pipeline/lucky-money/12h-2d-stay/predresult/' . $date;
        // $directory_outpredresult24 = 'wudiapps/stay-prediction-pipeline/lucky-money/24h-2d-stay/predresult/' . $date;
        $dirArray = [
            'wudiapps/stay-prediction-pipeline/lucky-money/12h-2d-stay/predset-emr-output/' . $date,
            'wudiapps/stay-prediction-pipeline/lucky-money/24h-2d-stay/predset-emr-output/' . $date,
            'wudiapps/stay-prediction-pipeline/lucky-money/12h-2d-stay/predresult/' . $date,
            'wudiapps/stay-prediction-pipeline/lucky-money/24h-2d-stay/predresult/' . $date,
        ];

        dump($dirArray);
        foreach ($dirArray as $key => $value) {
            $delete = Storage::deleteDirectory($value);
            dump($delete);
            Log::info('delete dir');
            Log::info($value);
            Log::info($delete);
        }
        // $directories = Storage::directories();
        // $directories = Storage::allDirectories();
        // $directories = Storage::files('log');
        // $deletefile = Storage::delete();
        // $delete = Storage::deleteDirectory('log');
        // $directory1 = Storage::makeDirectory('test123');
        // dump($directories);
        // dump($deletefile);

        Log::info('clean end');
    }
}
