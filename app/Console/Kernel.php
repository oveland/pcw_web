<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Carbon\Carbon;

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
            $yesterday = Carbon::yesterday()->toDateString();
//            $schedule->command('track:map --company=17')->everyMinute()->between('04:30', '21:00');

            $schedule->command('telescope:prune')->daily();

            /******************************************   4G   ***********************************************/

            $schedule->command('Sync4G:sync-photos --imei=352557104802940')->everyMinute()->runInBackground(); // Vehicle 8419 EP
            $schedule->command('rocket:count --vehicle-plate=WHW-596 --pa=3 --pr=20 ')->everyThreeHours()->runInBackground();
            $schedule->command("rocket:count --vehicle-plate=WHW-596 --pa=3 --pr=20 --date=$yesterday")->everyThreeHours()->runInBackground(); // Para procesar conteos de despacho de ayer. Aplica para rutas largas

            $schedule->command('Sync4G:sync-photos --imei=352557104743888')->everyMinute()->runInBackground(); // vehicle 2667 EP
            $schedule->command('rocket:count --vehicle-plate=KUK-593 --pa=3 --pr=20 ')->cron('*/47 * * * *')->runInBackground();
            /******************************************   3G   ***********************************************/


            $schedule->command('syrus:sync-photos --imei=357042066532541')->everyMinute()->runInBackground(); // Vehicle 001 Transpubenza
            $schedule->command('rocket:count --vehicle-plate=TST-001 --pa=3 --pr=5 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557100781619')->everyMinute()->runInBackground(); // Vehicle 02 Aeropuerto
            $schedule->command('rocket:count --vehicle-plate=SKR-579 --pa=2 --pr=5 ')->cron('*/47 * * * *')->runInBackground();


            /*********************************EXPRESO PALMIRA*********************************************/
            $schedule->command('syrus:sync-photos --imei=352557104744456')->everyMinute()->runInBackground(); // Vehicle  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=TJW-466 --pa=10 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104727915')->everyMinute()->runInBackground(); // Vehicle 5961 Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=TJW-676 --pa=5 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104790723')->everyMinute()->runInBackground(); // Vehicle 2819 Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=SPK385 --pa=3 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557100774424')->everyMinute()->runInBackground(); // Vehicle 6841 Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-185 --pa=5 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104710044')->everyMinute()->runInBackground(); // Vehicle electric  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=EP-001 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104744951')->everyMinute()->runInBackground(); // Vehicle 2203  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ESY-699 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104791531')->everyMinute()->runInBackground(); // Vehicle 2223  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ESY-708 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104743177')->everyMinute()->runInBackground(); // Vehicle 2221  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ESY-707 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104743375')->everyMinute()->runInBackground(); // Vehicle 2227  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ESY-710 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104787364')->everyMinute()->runInBackground(); // Vehicle 2225  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ESY-709 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104743441')->everyMinute()->runInBackground(); // Vehicle 2215  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ESY-704 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104735884')->everyMinute()->runInBackground(); // Vehicle 6005  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-677 --pa=10 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104791572')->everyMinute()->runInBackground(); // Vehicle 6001  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-674 --pa=10 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104727170')->everyMinute()->runInBackground(); // Vehicle 6003  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-676 --pa=10 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104792299')->everyMinute()->runInBackground(); // Vehicle 6007  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-678 --pa=10 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104743839')->everyMinute()->runInBackground(); // Vehicle 6009  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-680 --pa=2 --pr=50 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104709301')->everyMinute()->runInBackground(); // Vehicle 6011  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-673 --pa=2 --pr=50 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104791028')->everyMinute()->runInBackground(); // Vehicle 6015  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-679 --pa=10 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104791564')->everyMinute()->runInBackground(); // Vehicle 6601  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-563 --pa=10 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104787240')->everyMinute()->runInBackground(); // Vehicle 6603  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-569 --pa=2 --pr=50 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104743243')->everyMinute()->runInBackground(); // Vehicle 6609  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-572 --pa=2 --pr=50 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104743052')->everyMinute()->runInBackground(); // Vehicle 6611  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-573 --pa=2 --pr=50 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104727600')->everyMinute()->runInBackground(); // Vehicle 6605  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-570 --pa=2 --pr=50 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104743219')->everyMinute()->runInBackground(); // Vehicle 6607  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-571 --pa=2 --pr=50 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104787257')->everyMinute()->runInBackground(); // Vehicle 1741  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=WHV-817 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104743383')->everyMinute()->runInBackground(); // Vehicle 1739  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=WHV-813 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104787414')->everyMinute()->runInBackground(); // Vehicle 2729  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=SPK-391 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104787075')->everyMinute()->runInBackground(); // Vehicle 4837  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=KUL-888 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104792257')->everyMinute()->runInBackground(); // Vehicle 2697  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=KUK-632 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104790533')->everyMinute()->runInBackground(); // Vehicle 6821  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-149 --pa=10 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104791127')->everyMinute()->runInBackground(); // Vehicle 6825  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-151 --pa=10 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104744845')->everyMinute()->runInBackground(); // Vehicle 6823  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-150 --pa=10 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104743201')->everyMinute()->runInBackground(); // Vehicle 2739  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=KUK-565 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104791168')->everyMinute()->runInBackground(); // Vehicle 2661  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=KUL-983 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104743714')->everyMinute()->runInBackground();  // Vehicle 2691  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=KUK-643 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104736338')->everyMinute()->runInBackground(); // Vehicle 2741 expreso palmira
            $schedule->command('rocket:count --vehicle-plate=SPK-388 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104787356')->everyMinute()->runInBackground(); // Vehicle 6619  expreso palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-812 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104791069')->everyMinute()->runInBackground(); // Vehicle  expreso palmira 6617
            $schedule->command('rocket:count --vehicle-plate=ETK-811 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104791234')->everyMinute()->runInBackground(); // Vehicle  expreso palmira 6621
            $schedule->command('rocket:count --vehicle-plate=ETK-813 --pa=2 --pr=50 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104743391')->everyMinute()->runInBackground(); // Vehicle  expreso palmira 6623
            $schedule->command('rocket:count --vehicle-plate=ETK-814 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104787208')->everyMinute()->runInBackground(); // Vehicle  expreso palmira 2735
            $schedule->command('rocket:count --vehicle-plate=KUL-706 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104744696')->everyMinute()->runInBackground(); // Vehicle  expreso palmira 6615
            $schedule->command('rocket:count --vehicle-plate=ETK-810 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104791192')->everyMinute()->runInBackground(); // Vehicle  expreso palmira 2219
            $schedule->command('rocket:count --vehicle-plate=ESY-706 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104743128')->everyMinute()->runInBackground(); // Vehicle  expreso palmira 6025
            $schedule->command('rocket:count --vehicle-plate=ETK-963 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104473528')->everyMinute()->runInBackground(); // Vehicle  expreso palmira 2217
            $schedule->command('rocket:count --vehicle-plate=ESY-705 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104743094')->everyMinute()->runInBackground(); // Vehicle  expreso palmira 2211
            $schedule->command('rocket:count --vehicle-plate=ESY-703 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104812519')->everyMinute()->runInBackground(); // Vehicle  expreso Palmira 2209
            $schedule->command('rocket:count --vehicle-plate=ESY-702 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104755908')->everyMinute()->runInBackground(); // Vehicle  expreso Palmira 2767
            $schedule->command('rocket:count --vehicle-plate=KUL-890 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557100790404')->everyMinute()->runInBackground(); // Vehicle  expreso Palmira 2205
            $schedule->command('rocket:count --vehicle-plate=ESY-700 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104790814')->everyMinute()->runInBackground(); // Vehicle 2201  expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ESY-698 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104744456')->everyMinute()->runInBackground(); // Vehicle 7701  expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETL-006 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104709772')->everyMinute()->runInBackground(); // Vehicle 6027  expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-964 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104525327')->everyMinute()->runInBackground(); // Vehicle 6019  expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-959 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104787182')->everyMinute()->runInBackground(); // Vehicle 6029  expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-958 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104743722')->everyMinute()->runInBackground(); // Vehicle 6017  expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-957 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104789717')->everyMinute()->runInBackground(); // Vehicle 6021  expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-960 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104802981')->everyMinute()->runInBackground(); // Vehicle 6033  expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-965 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104840932')->everyMinute()->runInBackground(); // Vehicle 6023  expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-962 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104746204')->everyMinute()->runInBackground(); // Vehicle 2843  expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=WHW-763 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104727915')->everyMinute()->runInBackground(); // Vehicle 6031  expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ETK-961 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104485597')->everyMinute()->runInBackground(); // Vehicle 2693  expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=KUK-591 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104788420')->everyMinute()->runInBackground(); // Vehicle 2681  expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=KUK-644 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104727287')->everyMinute()->runInBackground(); // Vehicle 2657  expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=KUL-982 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104727246')->everyMinute()->runInBackground(); // Vehicle 1515  expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=WHU-522 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            //$schedule->command('syrus:sync-photos --imei=352557104743714')->everyMinute()->runInBackground(); // Vehicle 2679  expreso Palmira
            //$schedule->command('rocket:count --vehicle-plate=SPK-386 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104506590')->everyMinute()->runInBackground(); // Vehicle 2207  expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=ESY-701 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104477982')->everyMinute()->runInBackground(); // Vehicle 2663  expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=KUL-981 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104790913')->everyMinute()->runInBackground(); // Vehicle 2755  expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=KUK-595 --pa=2 --pr=10 ')->cron('*/47 * * * *')->runInBackground();


            /**********************************VALLEDUPAR*************************************************/
            
            $schedule->command('syrus:sync-photos --imei=352557104511442')->everyMinute()->runInBackground(); // Vehicle 5017 Valledupar
            $schedule->command('rocket:count --vehicle-plate=FXS-240 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557100775223')->everyMinute()->runInBackground(); // Vehicle 5000 Valledupar GPS antiguo 1 camara
            $schedule->command('syrus:sync-photos --imei=352557104789550')->everyMinute()->runInBackground(); // Vehicle 5000 Valledupar 3 camaras
            $schedule->command('rocket:count --vehicle-plate=WCY-762 --pa=2 --pr=8 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104506194')->everyMinute()->runInBackground(); // Vehicle 5005 Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-768 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104487122')->everyMinute()->runInBackground(); // Vehicle 5006 Valledupar
            $schedule->command('rocket:count --vehicle-plate=WYC-769 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104504983')->everyMinute()->runInBackground(); // Vehicle 5008 Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-770 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104506293')->everyMinute()->runInBackground(); // Vehicle 5009 Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-771 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557100803918')->everyMinute()->runInBackground(); // Vehicle 5015 Valledupar
            $schedule->command('rocket:count --vehicle-plate=WNL-372 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557100775694')->everyMinute()->runInBackground(); // Vehicle 5001 Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-763 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104510469')->everyMinute()->runInBackground(); // Vehicle 6060 2 camaras Valledupar
            $schedule->command('rocket:count --vehicle-plate=GIM-079 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104513166')->everyMinute()->runInBackground(); // Vehicle 6040 2 camaras  Valledupar
            $schedule->command('rocket:count --vehicle-plate=GIM-077 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557100791261')->everyMinute()->runInBackground(); // Vehicle 6070 2 camaras  Valledupar
            $schedule->command('rocket:count --vehicle-plate=GIM-080 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104818375')->everyMinute()->runInBackground(); // Vehicle 6050 2 camaras  Valledupar
            $schedule->command('rocket:count --vehicle-plate=GIM-078 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104826311')->everyMinute()->runInBackground(); // Vehicle 5018   Valledupar
            $schedule->command('rocket:count --vehicle-plate=FXS-241 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104483022')->everyMinute()->runInBackground(); // Vehicle 5002   Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-764 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104507192')->everyMinute()->runInBackground(); // Vehicle 5004   Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-766 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104510162')->everyMinute()->runInBackground(); // Vehicle 5020   Valledupar
            $schedule->command('rocket:count --vehicle-plate=FXS-261 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104505592')->everyMinute()->runInBackground(); // Vehicle 5007   Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-767 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104511103')->everyMinute()->runInBackground(); // Vehicle 5013   Valledupar
            $schedule->command('rocket:count --vehicle-plate=WNL-369 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104474138')->everyMinute()->runInBackground(); // Vehicle 5019   Valledupar
            $schedule->command('rocket:count --vehicle-plate=FXS-260 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104501203')->everyMinute()->runInBackground(); // Vehicle 5014   Valledupar
            $schedule->command('rocket:count --vehicle-plate=WNL-370 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557100804700')->everyMinute()->runInBackground(); // Vehicle 7001   Valledupar
            $schedule->command('rocket:count --vehicle-plate=TJW-190 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104504736')->everyMinute()->runInBackground(); // Vehicle 7003   Valledupar
            $schedule->command('rocket:count --vehicle-plate=TJW-192 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104489797')->everyMinute()->runInBackground(); // Vehicle 5003   Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-765 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();

            $schedule->command('syrus:sync-photos --imei=352557104504751')->everyMinute()->runInBackground(); // Vehicle 5011   Valledupar
            $schedule->command('rocket:count --vehicle-plate=WCY-829 --pa=2 --pr=20 ')->cron('*/47 * * * *')->runInBackground();


            /**********************************TRANSPUBENZA*************************************************/

            $schedule->command('syrus:sync-photos --imei=352557103568914')->everyMinute()->runInBackground();
            $schedule->command('syrus:sync-photos --imei=352557104776755')->everyMinute()->runInBackground();
            $schedule->command('syrus:sync-photos --imei=352557104819522')->everyMinute()->runInBackground();
            $schedule->command('syrus:sync-photos --imei=352557104516367')->everyMinute()->runInBackground();
            $schedule->command('syrus:sync-photos --imei=352557104455657')->everyMinute()->runInBackground();
            $schedule->command('syrus:sync-photos --imei=352557104535367')->everyMinute()->runInBackground();
            $schedule->command('syrus:sync-photos --imei=352557104519197')->everyMinute()->runInBackground();
            $schedule->command('syrus:sync-photos --imei=352557103567171')->everyMinute()->runInBackground();
            $schedule->command('syrus:sync-photos --imei=352557100819765')->everyMinute()->runInBackground();
            $schedule->command('syrus:sync-photos --imei=352557104534444')->everyMinute()->runInBackground();
            $schedule->command('syrus:sync-photos --imei=352557104456432')->everyMinute()->runInBackground();
            $schedule->command('syrus:sync-photos --imei=352557104525376')->everyMinute()->runInBackground();
            $schedule->command('syrus:sync-photos --imei=352557104524023')->everyMinute()->runInBackground();
            $schedule->command('syrus:sync-photos --imei=352557103602077')->everyMinute()->runInBackground();
            $schedule->command('syrus:sync-photos --imei=352557104447290')->everyMinute()->runInBackground();
            $schedule->command('syrus:sync-photos --imei=352557104517878')->everyMinute()->runInBackground();
            $schedule->command('syrus:sync-photos --imei=352557104524049')->everyMinute()->runInBackground();
            $schedule->command('syrus:sync-photos --imei=352557104552792')->everyMinute()->runInBackground();
            $schedule->command('syrus:sync-photos --imei=352557104790517')->everyMinute()->runInBackground();


            //================================================================================
            //======================== Sync data passengers from EP ==========================
            //================================================================================
            $schedule->command('lm:sync --company=39')->everyThreeHours();

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
