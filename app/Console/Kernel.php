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


            $schedule->command('syrus:sync-photos --imei=357042066532541')->everyFiveMinutes(); // Vehicle 001 Transpubenza
            $schedule->command('rocket:count --vehicle-plate=TST-001 --pa=3 --pr=5')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557100781619')->everyFiveMinutes(); // Vehicle 02 Aeropuerto
            $schedule->command('rocket:count --vehicle-plate=SKR-579 --pa=2 --pr=5')->everyThirtyMinutes();


            /*********************************EXPRESO PALMIRA*********************************************/
            $schedule->command('syrus:sync-photos --imei=352557104744456')->everyFiveMinutes(); // Vehicle  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=TJW-466 --pa=10 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104727915')->everyFiveMinutes(); // Vehicle 5961 Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=TJW-676 --pa=5 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557100790404')->everyFiveMinutes(); // Vehicle 2819 Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=SPK385 --pa=3 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557100774424')->everyFiveMinutes(); // Vehicle 6841 Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-185 --pa=5 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104710044')->everyFiveMinutes(); // Vehicle electric  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=EP-001 --pa=2 --pr=2')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104744951')->everyFiveMinutes(); // Vehicle 2203  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ESY-699 --pa=2 --pr=10')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104791531')->everyFiveMinutes(); // Vehicle 2223  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ESY-708 --pa=2 --pr=5')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104743177')->everyFiveMinutes(); // Vehicle 2221  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ESY-707 --pa=2 --pr=5')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104743375')->everyFiveMinutes(); // Vehicle 2227  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ESY-710 --pa=2 --pr=5')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104787364')->everyFiveMinutes(); // Vehicle 2225  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ESY-709 --pa=2 --pr=5')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104743441')->everyFiveMinutes(); // Vehicle 2215  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ESY-709 --pa=2 --pr=5')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104735884')->everyFiveMinutes(); // Vehicle 6005  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-677 --pa=3 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104791572')->everyFiveMinutes(); // Vehicle 6001  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-674 --pa=3 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104727170')->everyFiveMinutes(); // Vehicle 6003  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-676 --pa=3 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104792299')->everyFiveMinutes(); // Vehicle 6007  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-678 --pa=3 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104743839')->everyFiveMinutes(); // Vehicle 6009  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-680 --pa=3 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104709301')->everyFiveMinutes(); // Vehicle 6011  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-673 --pa=3 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104791028')->everyFiveMinutes(); // Vehicle 6015  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-679 --pa=3 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104791564')->everyFiveMinutes(); // Vehicle 6601  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-563 --pa=3 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104787240')->everyFiveMinutes(); // Vehicle 6603  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-569 --pa=3 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104743243')->everyFiveMinutes(); // Vehicle 6609  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-572 --pa=3 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104743052')->everyFiveMinutes(); // Vehicle 6611  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-573 --pa=3 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104727600')->everyFiveMinutes(); // Vehicle 6605  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-570 --pa=3 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104743219')->everyFiveMinutes(); // Vehicle 6607  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-571 --pa=3 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104787257')->everyFiveMinutes(); // Vehicle 1741  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=WHV-817 --pa=3 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104743383')->everyFiveMinutes(); // Vehicle 1739  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=WHV-813 --pa=3 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104787414')->everyFiveMinutes(); // Vehicle 2729  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=SPK-391 --pa=1 --pr=10')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104787075')->everyFiveMinutes(); // Vehicle 4837  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=KUL-888 --pa=3 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104792257')->everyFiveMinutes(); // Vehicle 2697  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=KUK-632 --pa=2 --pr=10')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104790533')->everyFiveMinutes(); // Vehicle 6821  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-149 --pa=3 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104744845')->everyFiveMinutes(); // Vehicle 6823  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-150 --pa=3 --pr=20')->everyThirtyMinutes();

                $schedule->command('syrus:sync-photos --imei=352557104743201')->everyFiveMinutes(); // Vehicle 2739  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=KUK-565 --pa=1 --pr=10')->everyThirtyMinutes();

            /**********************************VALLEDUPAR*************************************************/

            $schedule->command('syrus:sync-photos --imei=352557100791261')->everyFiveMinutes(); // Vehicle 9011 Valledupar - vehiculo sale de circulacion
            $schedule->command('rocket:count --vehicle-plate=SMN-884 --pa=2 --pr=10')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104511442')->everyFiveMinutes(); // Vehicle 5017 Valledupar
            $schedule->command('rocket:count --vehicle-plate=FXS-240 --pa=2 --pr=20')->everyThirtyMinutes();

            //$schedule->command('syrus:sync-photos --imei=352557100775223')->everyFiveMinutes(); // Vehicle 5000 Valledupar GPS antiguo 1 camara
            $schedule->command('syrus:sync-photos --imei=352557104789550')->everyFiveMinutes(); // Vehicle 5000 Valledupar 3 camaras
            $schedule->command('rocket:count --vehicle-plate=WCY-762 --pa=2 --pr=8')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104506194')->everyFiveMinutes(); // Vehicle 5005 Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-768 --pa=2 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104487122')->everyFiveMinutes(); // Vehicle 5006 Valledupar
            $schedule->command('rocket:count --vehicle-plate=WYC-769 --pa=2 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104504983')->everyFiveMinutes(); // Vehicle 5008 Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-770 --pa=2 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104506293')->everyFiveMinutes(); // Vehicle 5009 Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-771 --pa=2 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557100803918')->everyFiveMinutes(); // Vehicle 5015 Valledupar
            $schedule->command('rocket:count --vehicle-plate=WNL-372 --pa=2 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557100775694')->everyFiveMinutes(); // Vehicle 5001 Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-763 --pa=2 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104510469')->everyFiveMinutes(); // Vehicle 6060 2 camaras Valledupar
            $schedule->command('rocket:count --vehicle-plate=GIM-079 --pa=2 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104513166')->everyFiveMinutes(); // Vehicle 6040 2 camaras  Valledupar
            $schedule->command('rocket:count --vehicle-plate=GIM-077 --pa=2 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557100791261')->everyFiveMinutes(); // Vehicle 6070 2 camaras  Valledupar
            $schedule->command('rocket:count --vehicle-plate=GIM-080 --pa=2 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104818375')->everyFiveMinutes(); // Vehicle 6050 2 camaras  Valledupar
            $schedule->command('rocket:count --vehicle-plate=GIM-078 --pa=2 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104826311')->everyFiveMinutes(); // Vehicle 5018   Valledupar
            $schedule->command('rocket:count --vehicle-plate=FXS-241 --pa=2 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104483022')->everyFiveMinutes(); // Vehicle 5002   Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-764 --pa=2 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104507192')->everyFiveMinutes(); // Vehicle 5004   Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-766 --pa=2 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104510162')->everyFiveMinutes(); // Vehicle 5020   Valledupar
            $schedule->command('rocket:count --vehicle-plate=FXS-261 --pa=2 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104505592')->everyFiveMinutes(); // Vehicle 5007   Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-767 --pa=2 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104511103')->everyFiveMinutes(); // Vehicle 5013   Valledupar
            $schedule->command('rocket:count --vehicle-plate=WNL-369 --pa=2 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104474138')->everyFiveMinutes(); // Vehicle 5019   Valledupar
            $schedule->command('rocket:count --vehicle-plate=FXS-260 --pa=2 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104501203')->everyFiveMinutes(); // Vehicle 5014   Valledupar
            $schedule->command('rocket:count --vehicle-plate=WNL-370 --pa=2 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557100804700')->everyFiveMinutes(); // Vehicle 7001   Valledupar
            $schedule->command('rocket:count --vehicle-plate=TJW-190 --pa=2 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104504736')->everyFiveMinutes(); // Vehicle 7003   Valledupar
            $schedule->command('rocket:count --vehicle-plate=TJW-192 --pa=2 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104489797')->everyFiveMinutes(); // Vehicle 5003   Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-765 --pa=2 --pr=20')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104504751')->everyFiveMinutes(); // Vehicle 5011   Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-829 --pa=2 --pr=20')->everyThirtyMinutes();


            /**********************************TRANSPUBENZA*************************************************/

            $schedule->command('syrus:sync-photos --imei=352557103568914')->everyFiveMinutes();
            $schedule->command('syrus:sync-photos --imei=352557104776755')->everyFiveMinutes();
            $schedule->command('syrus:sync-photos --imei=352557104819522')->everyFiveMinutes();
            $schedule->command('syrus:sync-photos --imei=352557104516367')->everyFiveMinutes();
            $schedule->command('syrus:sync-photos --imei=352557104455657')->everyFiveMinutes();
            $schedule->command('syrus:sync-photos --imei=352557104535367')->everyFiveMinutes();
            $schedule->command('syrus:sync-photos --imei=352557104519197')->everyFiveMinutes();
            $schedule->command('syrus:sync-photos --imei=352557103567171')->everyFiveMinutes();
            $schedule->command('syrus:sync-photos --imei=352557100819765')->everyFiveMinutes();
            $schedule->command('syrus:sync-photos --imei=352557104534444')->everyFiveMinutes();
            $schedule->command('syrus:sync-photos --imei=352557104456432')->everyFiveMinutes();
            $schedule->command('syrus:sync-photos --imei=352557104525376')->everyFiveMinutes();
            $schedule->command('syrus:sync-photos --imei=352557104524023')->everyFiveMinutes();
            $schedule->command('syrus:sync-photos --imei=352557103602077')->everyFiveMinutes();
            $schedule->command('syrus:sync-photos --imei=352557104447290')->everyFiveMinutes();
            $schedule->command('syrus:sync-photos --imei=352557104525327')->everyFiveMinutes();
            $schedule->command('syrus:sync-photos --imei=352557104517878')->everyFiveMinutes();
            $schedule->command('syrus:sync-photos --imei=352557104524049')->everyFiveMinutes();
            $schedule->command('syrus:sync-photos --imei=352557104477982')->everyFiveMinutes();
            $schedule->command('syrus:sync-photos --imei=352557104552792')->everyFiveMinutes();
            $schedule->command('syrus:sync-photos --imei=352557104790517')->everyFiveMinutes();

            /**********************************ARMENIA*************************************************/
            $schedule->command('syrus:sync-photos --imei=352557104525327')->everyFiveMinutes(); // Vehicle 62   ARMENIA
            $schedule->command('rocket:count --vehicle-plate=TJA-310 --pa=2 --pr=4')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104506590')->everyFiveMinutes(); // Vehicle 68   ARMENIA
            $schedule->command('rocket:count --vehicle-plate=TJA-234 --pa=2 --pr=4')->everyThirtyMinutes();

            /**********************************IBAGUE*************************************************/
            $schedule->command('syrus:sync-photos --imei=352557104743722')->everyFiveMinutes(); // Vehicle 027 IBAGUE
            $schedule->command('rocket:count --vehicle-plate=TGN-356 --pa=2 --pr=4')->everyThirtyMinutes();

            $schedule->command('syrus:sync-photos --imei=352557104755908')->everyFiveMinutes(); // Vehicle 041 IBAGUE
            $schedule->command('rocket:count --vehicle-plate=TJB-127 --pa=3 --pr=20')->everyThirtyMinutes();


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
