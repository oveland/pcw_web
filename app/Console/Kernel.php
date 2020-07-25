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
        /* Commands for vehicles */
        Commands\LogParkedVehiclesCommand::class,
        Commands\DatabaseManageMarkersReportsCommand::class,
        Commands\GPSRestartCommand::class,
        Commands\GPSCheckStatusCommand::class,
        Commands\GPSCheckServerCommand::class,

        /* Commands for SMS */
        Commands\SMSSendReportCommand::class,
        Commands\SMSSendProprietaryReportCommand::class,

        /* Commands for Database */
        Commands\DatabaseMigrations::class,
        Commands\DatabaseManageOLDRoutinesCommand::class,

        /* Commands for Mails */
        Commands\EventsReportMailCommand::class,
        Commands\DispatchReportMailCommand::class,
        Commands\ConsolidatedPassengerReportMailCommand::class,
        Commands\ManagementReportMailCommand::class,

        /* Commands for DAR (Automatic Route Detection) */
        Commands\DARCommand::class,

        /* Commands for routes and dispatch registers */
        Commands\CloseDispatchRegistersCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('log:parked-vehicles')->everyMinute();

        $schedule->command('db:pcw-migrations')->dailyAt('00:00');
        $schedule->command('db:manage-markers-reports')->dailyAt('02:00');
        $schedule->command('db:manage-old-routines')->dailyAt('03:00');


        $schedule->command('gps:restart')->dailyAt('12:00');
        $schedule->command('gps:check-status')->everyMinute();
        $schedule->command('gps:check-server')->everyFiveMinutes();

        /* Route report for TUPAL (CompanyId = 28) */
        //$schedule->command('mail-routes:events --company=28 --prod=true')->dailyAt('04:00');
        //$schedule->command('mail-routes:dispatches --company=28 --prod=true')->dailyAt('04:00');

        /* Reports for ALAMEDA */
        $schedule->command('mail-routes:events --company=14')->dailyAt('08:00');
        //$schedule->command('mail-routes:dispatches --company=14 --prod=true')->dailyAt('04:00');
//        $schedule->command('mail-passengers:consolidated --company=14')->dailyAt('08:10');

        //$schedule->command('dar:run')->dailyAt('03:00');

        /* Close the fake dispatch registers */
        $schedule->command('dispatch-registers:close')->dailyAt('00:05');
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
