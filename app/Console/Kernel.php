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
        Commands\LogParkedVehicles::class,
        Commands\DatabaseManageLocationReports::class,
        Commands\DatabaseManageMarkersReports::class,
        Commands\GPSRestart::class,
        Commands\GPSCheckStatusCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('log:parked-vehicles')->everyMinute();

        $schedule->command('db:manage-location-reports')->dailyAt('01:00');
        $schedule->command('db:manage-markers-reports')->dailyAt('02:00');

        $schedule->command('gps:restart')->hourly()->between(config('sms.sms_reset_start_at'),config('sms.sms_reset_end_at'));

        $schedule->command('gps:check-status')->everyMinute();
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
