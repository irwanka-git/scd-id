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
        //
            Commands\ConvertImage64::class,
            Commands\ClearUserJawaban::class,
            Commands\SkoringMinatSMA::class,
            Commands\SkoringMinatMAN::class,
            Commands\SkoringMinatSMK::class,
            Commands\SkoringJurusanKuliah::class,
            Commands\SkoringPsikotesLengkap::class,
            Commands\SkoringMinatSMAVERSI2::class,
            Commands\SkoringJurusanKuliahVERSI2::class,
            Commands\SkoringTestIQdanEQ::class,
            Commands\TestingGETDATAAPI::class,
            Commands\SkoringMinatSMKV2::class,
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
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
