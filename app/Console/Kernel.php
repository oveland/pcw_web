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


            $schedule->command('syrus:sync-photos --imei=357042066532541')->everyFiveMinutes()->between('00:00', '23:59'); // Vehicle 001 Transpubenza
            $schedule->command('rocket:count --vehicle-plate=TST-001 --pa=3 --pr=5')->everyTenMinutes();

            $schedule->command('syrus:sync-photos --imei=352557100781619')->everyFiveMinutes()->between('00:00', '23:59'); // Vehicle 02 Aeropuerto
            $schedule->command('rocket:count --vehicle-plate=SKR-579 --pa=2 --pr=5')->everyTenMinutes();


            /*********************************EXPRESO PALMIRA*********************************************/
            $schedule->command('syrus:sync-photos --imei=352557104744456')->everyTwoMinutes()->between('00:00', '23:59'); // Vehicle  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=TJW-466 --pa=10 --pr=20')->everyTenMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104727915')->everyFiveMinutes()->between('00:00', '23:59'); // Vehicle 5961 Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=TJW-676 --pa=5 --pr=20')->everyTenMinutes();

            $schedule->command('syrus:sync-photos --imei=352557100790404')->everyFiveMinutes()->between('00:00', '23:59'); // Vehicle 2819 Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=SPK385 --pa=3 --pr=20')->everyTenMinutes();

            $schedule->command('syrus:sync-photos --imei=352557100774424')->everyTwoMinutes()->between('00:00', '23:59'); // Vehicle 6841 Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-185 --pa=5 --pr=20')->everyTenMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104710044')->everyTwoMinutes()->between('00:00', '23:59'); // Vehicle electric  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=EP-001 --pa=2 --pr=2')->everyTenMinutes();


            /**********************************VALLEDUPAR*************************************************/

            // $schedule->command('syrus:sync-photos --imei=352557100791261')->everyMinute()->between('00:00', '23:59'); // Vehicle 9011 Valledupar - vehiculo sale de circulacion
            // $schedule->command('rocket:count --vehicle-plate=SMN-884 --pa=2 --pr=10')->everyTenMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104511442')->everyFiveMinutes()->between('00:00', '23:59'); // Vehicle 5017 Valledupar
            $schedule->command('rocket:count --vehicle-plate=FXS-240 --pa=2 --pr=10')->everyTenMinutes();

            $schedule->command('syrus:sync-photos --imei=352557100775223')->everyFiveMinutes()->between('00:00', '23:59'); // Vehicle 5000 Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-762 --pa=2 --pr=10')->everyTenMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104506194')->everyFiveMinutes()->between('00:00', '23:59'); // Vehicle 5005 Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-768 --pa=2 --pr=10')->everyTenMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104487122')->everyFiveMinutes()->between('00:00', '23:59'); // Vehicle 5006 Valledupar
            $schedule->command('rocket:count --vehicle-plate=WYC-769 --pa=2 --pr=10')->everyTenMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104504983')->everyFiveMinutes()->between('00:00', '23:59'); // Vehicle 5008 Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-770 --pa=2 --pr=10')->everyTenMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104506293')->everyFiveMinutes()->between('00:00', '23:59'); // Vehicle 5009 Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-771 --pa=2 --pr=10')->everyTenMinutes();

            $schedule->command('syrus:sync-photos --imei=352557100803918')->everyFiveMinutes()->between('00:00', '23:59'); // Vehicle 5015 Valledupar
            $schedule->command('rocket:count --vehicle-plate=WNL-372 --pa=2 --pr=10')->everyTenMinutes();

            $schedule->command('syrus:sync-photos --imei=352557100775694')->everyFiveMinutes()->between('00:00', '23:59'); // Vehicle 5001 Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-763 --pa=2 --pr=10')->everyTenMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104510469')->everyFiveMinutes()->between('00:00', '23:59'); // Vehicle 6060 2 camaras Valledupar
            $schedule->command('rocket:count --vehicle-plate=GIM-079 --pa=2 --pr=10')->everyTenMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104513166')->everyFiveMinutes()->between('00:00', '23:59'); // Vehicle 6040 2 camaras  Valledupar
            $schedule->command('rocket:count --vehicle-plate=GIM-077 --pa=2 --pr=10')->everyTenMinutes();

            $schedule->command('syrus:sync-photos --imei=352557100791261')->everyFiveMinutes()->between('00:00', '23:59'); // Vehicle 6070 2 camaras  Valledupar
            $schedule->command('rocket:count --vehicle-plate=GIM-080 --pa=2 --pr=10')->everyTenMinutes();

            $schedule->command('syrus:sync-photos --company=2')->everyTenMinutes(); // Sync photos on Transpubenza

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
