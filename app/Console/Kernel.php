<?php

namespace App\Console;

use App\Console\Commands\CompressCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command(CompressCommand::class)->everyFiveMinutes()->runInBackground()->withoutOverlapping();
        // $schedule->command(CompressCommand::class,['compress'])->cron('*/20 1-10 * * *')->runInBackground()->withoutOverlapping();
        $schedule->command(CompressCommand::class,['compress'])->everyTenMinutes()->runInBackground()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
