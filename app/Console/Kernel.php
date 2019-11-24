<?php

namespace App\Console;

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
      Commands\GetPrices::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('get:prices')->withoutOverlapping()->everyMinute();
        // $schedule->command('getPrices')->cron('10 * * * * *');
        /*$schedule->call(function () {
        })->everyMinute();*/
        // $schedule->call(Price::getNewPrice())->everyMinute()->sendOutputTo(storage_path('logs/output.log'));//->cron('10 * * * * *');
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
