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
        if (config('app.env') == 'beta') {
//            $schedule->command('track:map --company=17')->everyMinute()->between('04:30', '21:00');

            $schedule->command('telescope:prune')->daily();

            $schedule->command('syrus:sync-photos --imei=357042066532541')->everyMinute()->between('04:00', '23:59'); // Vehicle 001 Transpubenza
            $schedule->command('rocket:count --vehicle-plate=TST-001 --pa=3 --pr=5')->everyTenMinutes();

            $schedule->command('syrus:sync-photos --imei=352557100790404')->everyMinute()->between('04:00', '23:59'); // Vehicle 2819 Palmira
            $schedule->command('rocket:count --vehicle-plate=SPK385 --pa=3 --pr=20')->everyTenMinutes();

            $schedule->command('syrus:sync-photos --imei=352557100781619')->everyMinute()->between('04:00', '23:59'); // Vehicle 02 Aeropuerto
            $schedule->command('rocket:count --vehicle-plate=SKR-579 --pa=1 --pr=5')->everyTenMinutes();

            $schedule->command('syrus:sync-photos --company=2')->everyTwoMinutes(); // Sync photos on Transpubenza
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
