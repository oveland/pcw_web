<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (config('app.env') == 'local') {
            //$schedule->command('track:map --company=21')->everyMinute()->between('04:00', '22:00');
            //$schedule->command('track:map --company=21')->everyFiveMinutes()->between('22:00', '23:59');
            //$schedule->command('track:map --company=21')->everyFiveMinutes()->between('00:00', '04:00');

            $schedule->command('track:map --company=17')->everyMinute()->between('04:30', '21:00');
            //$schedule->command('track:map --company=17')->everyFiveMinutes()->between('22:00', '23:59');
            //$schedule->command('track:map --company=17')->everyFiveMinutes()->between('00:00', '04:00');


            $schedule->command('telescope:prune')->daily();

//            $schedule->command('rocket:take-photo --vehicle-plate=VCK-542')->cron('*/2 * * * *');
//                ->between('05:00', '22:00');
//            $schedule->command('rocket:release-busy --vehicle-plate=VCK-542')->hourly()->between('06:00', '18:00');

//            $schedule->command('rocket:take-photo --vehicle-plate=VQA-312')->cron('*/2 * * * *');
//                ->between('05:00', '22:00');
//            $schedule->command('rocket:release-busy --vehicle-plate=VQA-312')->hourly()->between('06:00', '18:00');

//            $schedule->command('rocket:take-photo --vehicle-plate=WDK-885')->cron('*/2 * * * *');
//                ->between('05:00', '22:00');
//            $schedule->command('rocket:release-busy --vehicle-plate=WDK-885')->hourly()->between('06:00', '18:00');

//            $schedule->command('rocket:take-photo --vehicle-plate=VPF-273')->cron('*/2 * * * *');
//                ->between('05:00', '22:00');
//            $schedule->command('rocket:release-busy --vehicle-plate=WDK-885')->hourly()->between('06:00', '18:00');

//            $schedule->command('rocket:take-photo --vehicle-plate=DEM-001')->cron('*/2 * * * *');
//            $schedule->command('rocket:take-photo --vehicle-plate=DEM-002')->cron('*/2 * * * *');

//            $schedule->command('rocket:take-photo --vehicle-plate=VCI-427')->cron('*/2 * * * *');
//                ->between('05:00', '19:00');

            $schedule->command('concox:take-photo --camera=3')->cron('*/2 * * * *')
                ->between('05:00', '19:00');

        } else {
            $schedule->command('log:parked-vehicles')->everyMinute();

            $schedule->command('db:pcw-migrations')->dailyAt('00:00');
            $schedule->command('db:manage-markers-reports')->dailyAt('02:00');
            $schedule->command('db:manage-old-routines')->dailyAt('03:00');


            $schedule->command('gps:restart')->dailyAt('12:00');
            $schedule->command('gps:check-status')->everyMinute();

            /* Route report for TUPAL (CompanyId = 28) */
            $schedule->command('send-mail:consolidated --company=28 --prod=true')->dailyAt('04:00');

            /* Reports for ALAMEDA */
            $schedule->command('send-mail:consolidated --company=14 --prod=true')->dailyAt('08:00');
            $schedule->command('send-mail:consolidated-passengers --company=14')->dailyAt('08:10');

            //$schedule->command('dar:run')->dailyAt('03:00');

            /* Close the fake dispatch registers */
            $schedule->command('dispatch-registers:close')->dailyAt('00:05');
        }
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
