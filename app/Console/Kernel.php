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


        $schedule->command('gps:restart')->hourly()->between(config('sms.sms_reset_start_at'),config('sms.sms_reset_end_at'));
        $schedule->command('gps:check-status')->everyMinute();

        //$schedule->command('sms:send-report')->cron(config('sms.sms_cron_report'));
        //$schedule->command('sms:send-proprietary-report')->cron(config('sms.sms_cron_proprietary_report'));


        $schedule->command('send-mail:consolidated')->dailyAt('04:00');
        $schedule->command('dar:run')->dailyAt('04:20');
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
