<?php

namespace App\Console;

use Aws\Command;
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
        /* Commands for vehicles */
        Commands\LogParkedVehiclesCommand::class,
        Commands\DatabaseManageMarkersReportsCommand::class,
        Commands\GPSRestartCommand::class,
        Commands\GPSCheckStatusCommand::class,

        /* Commands for SMS */
        Commands\SMSSendReportCommand::class,
        Commands\SMSSendProprietaryReportCommand::class,

        /* Commands for Database */
        Commands\DatabaseManageOLDRoutinesCommand::class,

        /* Commands for Mails */
        Commands\ConsolidatedReportMailCommand::class,
        Commands\ConsolidatedPassengerReportMailCommand::class,

        /* Commands for DAR (Automatic Route Detection) */
        Commands\DARCommand::class
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

        $schedule->command('db:manage-markers-reports')->dailyAt('02:00');
        $schedule->command('db:manage-old-routines')->dailyAt('03:00');


        $schedule->command('gps:restart')->dailyAt('12:00');
        $schedule->command('gps:check-status')->everyMinute();

        $schedule->command('send-mail:consolidated')->dailyAt('08:00');
        $schedule->command('send-mail:consolidated-passengers --prod=true')->dailyAt('08:10');

        $schedule->command('dar:run')->dailyAt('02:00');
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
