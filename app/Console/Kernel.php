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

            /*********************************************************  5G   ***********************************************/

            //nuevo comando de sincronizacion para 5G, por empresa 
            $schedule->command('sync5GV2:sync-photos --empresa=39')->hourly()->runInBackground();
            $schedule->command('sync5GV2:sync-photos --empresa=39')->dailyAt('23:50')->runInBackground();

            $schedule->command('sync5GV2:sync-photos --empresa=41')->hourly()->runInBackground();
            $schedule->command('sync5GV2:sync-photos --empresa=41')->dailyAt('23:50')->runInBackground();

            // Comando para procesar fotos 5G cada 5 horas areas
            $schedule->command('rocket:process-5g-photos --company=39')->cron('0 */5 * * *')->runInBackground();
            $schedule->command('rocket:process-5g-photos --company=41')->cron('0 */5 * * *')->runInBackground();

            // Comando para conteo 5G V2 con servidor privado (7am, 10am, 6pm, 11:30pm)
            $schedule->command('rocket:count-5g-v2')->dailyAt('07:00')->runInBackground();
            $schedule->command('rocket:count-5g-v2')->dailyAt('10:00')->runInBackground();
            $schedule->command('rocket:count-5g-v2')->dailyAt('18:00')->runInBackground();
            $schedule->command('rocket:count-5g-v2')->dailyAt('23:30')->runInBackground();
            

      /*    $schedule->command('sync5G:sync-photos --imei=352557104469351')->everyThirtyMinutes()->runInBackground();//8311
            $schedule->command('sync5G:sync-photos --imei=35255710446935112')->everyThirtyMinutes()->runInBackground(); //8311
            $schedule->command('sync5G:sync-photos --imei=35255710446935113')->everyThirtyMinutes()->runInBackground(); //8311*/


           // $schedule->command('sync5G:sync-photos --imei=352557104840254')->cron('30 7,12,18,23 * * *')->runInBackground(); //8353
            //$schedule->command('sync5G:sync-photos --imei=3525571048401234')->cron('30 7,12,18,23 * * *')->runInBackground(); //8353
            //$schedule->command('sync5G:sync-photos --imei=3525571048405678')->cron('30 7,12,18,23 * * *')->runInBackground(); //8353
           /* $schedule->command('rocket:count --vehicle-plate=
            --pa=3 --pr=50')
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();
            $schedule->command("rocket:count --vehicle-plate=NDK-683 --pa=3 --pr=50 --date={$yesterday}")
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();*/



            /* $schedule->command('sync5G:sync-photos --imei=352557104808657')->everyThirtyMinutes()->runInBackground(); //8507
             $schedule->command('sync5G:sync-photos --imei=35255710480865812')->everyThirtyMinutes()->runInBackground(); //8507
             $schedule->command('sync5G:sync-photos --imei=35255710480865412')->everyThirtyMinutes()->runInBackground(); //8507
             $schedule->command('rocket:count --vehicle-plate=LXS-893 --pa=3 --pr=50')
                 ->cron('30 23,5,10,15 * * *')
                 ->runInBackground();
             $schedule->command("rocket:count --vehicle-plate=LXS-893 --pa=3 --pr=50 --date={$yesterday}")
                 ->cron('30 23,5,10,15 * * *')
                 ->runInBackground();*/


           /* $schedule->command('sync5G:sync-photos --imei=352557104478824')->everyThirtyMinutes()->runInBackground(); //8515
            $schedule->command('sync5G:sync-photos --imei=3525571044788231')->everyThirtyMinutes()->runInBackground(); //8515
            $schedule->command('sync5G:sync-photos --imei=3525571044788225')->everyThirtyMinutes()->runInBackground(); //8515
            $schedule->command('rocket:count --vehicle-plate=LXS-898 --pa=3 --pr=50')
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();
            $schedule->command("rocket:count --vehicle-plate=LXS-898 --pa=3 --pr=50 --date={$yesterday}")
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();*/
            
         /*   $schedule->command('sync5G:sync-photos --imei=352557104466142')->everyThirtyMinutes()->runInBackground(); //8505
            $schedule->command('sync5G:sync-photos --imei=3525571044661423')->everyThirtyMinutes()->runInBackground(); //8505
            $schedule->command('sync5G:sync-photos --imei=3525571044661424')->everyThirtyMinutes()->runInBackground(); //8505*/
            /*$schedule->command('rocket:count --vehicle-plate=LXS-892 --pa=3 --pr=50')
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();
            $schedule->command("rocket:count --vehicle-plate=LXS-892 --pa=3 --pr=50 --date={$yesterday}")
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();*/


            //$schedule->command('sync5G:sync-photos --imei=352557104476224')->everyThirtyMinutes()->runInBackground(); //8501
            //$schedule->command('sync5G:sync-photos --imei=3525571044762241')->everyThirtyMinutes()->runInBackground(); //8501
            //$schedule->command('sync5G:sync-photos --imei=3525571044762242')->everyThirtyMinutes()->runInBackground(); //8501
           /* $schedule->command('rocket:count --vehicle-plate=LXS-896 --pa=3 --pr=50')
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();
            $schedule->command("rocket:count --vehicle-plate=LXS-896 --pa=3 --pr=50 --date={$yesterday}")
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();*/

          /*  $schedule->command('sync5G:sync-photos --imei=352557104746667')->everyThirtyMinutes()->runInBackground(); //8253
            $schedule->command('sync5G:sync-photos --imei=3525571047466723')->everyThirtyMinutes()->runInBackground(); //8253
            $schedule->command('sync5G:sync-photos --imei=35255710474667986')->everyThirtyMinutes()->runInBackground(); //8253
            $schedule->command('rocket:count --vehicle-plate=WNQ-351 --pa=3 --pr=50')
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();
            $schedule->command("rocket:count --vehicle-plate=WNQ-351 --pa=3 --pr=50 --date={$yesterday}")
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();*/

            //$schedule->command('sync5G:sync-photos --imei=352557104772119')->everyThirtyMinutes()->runInBackground(); //8517
            //$schedule->command('sync5G:sync-photos --imei=352557104722222')->everyThirtyMinutes()->runInBackground(); //8517
            //$schedule->command('sync5G:sync-photos --imei=3525571047111111')->everyThirtyMinutes()->runInBackground(); //8517
          /*  $schedule->command('rocket:count --vehicle-plate=LXS-903 --pa=3 --pr=50')
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();
            $schedule->command("rocket:count --vehicle-plate=LXS-903 --pa=3 --pr=50 --date={$yesterday}")
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();*/

           /* $schedule->command('sync5G:sync-photos --imei=352557104481877')->everyThirtyMinutes()->runInBackground(); //8509
            $schedule->command('sync5G:sync-photos --imei=3525571044818772')->everyThirtyMinutes()->runInBackground(); //8509
            $schedule->command('sync5G:sync-photos --imei=3525571044818773')->everyThirtyMinutes()->runInBackground(); //8509
            $schedule->command('rocket:count --vehicle-plate=LXS-894 --pa=3 --pr=50')
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();
            $schedule->command("rocket:count --vehicle-plate=LXS-894 --pa=3 --pr=50 --date={$yesterday}")
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();*/

            //$schedule->command('sync5G:sync-photos --imei=352557104813640')->everyThirtyMinutes()->runInBackground(); //2907
            $schedule->command('Sync4G:sync-photos --imei=352557104813640')->everyMinute()->runInBackground(); //2907
            $schedule->command('Sync4G:sync-photos --imei=0970000093')->everyMinute()->runInBackground(); //1997

           /* $schedule->command('rocket:count --vehicle-plate=NYK-137 --pa=2 --pr=50')
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();
            $schedule->command("rocket:count --vehicle-plate=NYK-137 --pa=2 --pr=50 --date={$yesterday}")
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();*/

            //$schedule->command('sync5G:sync-photos --imei=352557104466217')->everyMinute()->runInBackground(); //8331
            //$schedule->command('sync5G:sync-photos --imei=3525571044662171')->everyMinute()->runInBackground(); //8331
            //$schedule->command('sync5G:sync-photos --imei=3525571044662172')->everyMinute()->runInBackground(); //8331
            /*$schedule->command('rocket:count --vehicle-plate=WHW-596 --pa=3 --pr=50')
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();
            $schedule->command("rocket:count --vehicle-plate=WHW-596 --pa=3 --pr=50 --date={$yesterday}")
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();*/  

            $schedule->command('Sync4G:sync-photos --imei=352557104788503')->everyMinute()->runInBackground(); // Vehicle 8217 EP
            $schedule->command('Sync4G:sync-photos --imei=3525571047885031')->everyMinute()->runInBackground(); // Vehicle 8217 EP
            //$schedule->command('rocket:count --vehicle-plate=WHW-157 --pa=3 --pr=100 ')->cron('30 23,5,10,15 * * *')->runInBackground();
            //$schedule->command("rocket:count --vehicle-plate=WHW-157 --pa=3 --pr=100 --date={$yesterday}")->cron('30 23,5,10,15 * * *')->runInBackground(); // Para procesar conteos de despacho de ayer. Aplica para rutas largas



            $schedule->command('sync5G:sync-photos --imei=352557104484632')->everyMinute()->runInBackground(); //8287
            $schedule->command('sync5G:sync-photos --imei=3525571044846321')->everyMinute()->runInBackground(); //8287
            $schedule->command('rocket:count --vehicle-plate=LLO-916 --pa=3 --pr=50')
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();
            $schedule->command("rocket:count --vehicle-plate=LLO-916 --pa=3 --pr=50 --date={$yesterday}")
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();


            $schedule->command('sync5G:sync-photos --imei=352557104486033')->everyMinute()->runInBackground(); //8283
            $schedule->command('sync5G:sync-photos --imei=3525571044860331')->everyMinute()->runInBackground(); //8283
           // $schedule->command('rocket:count --vehicle-plate=LLO-914 --pa=3 --pr=50')
               // ->cron('30 23,5,10,15 * * *')
             //   ->runInBackground();
          //  $schedule->command("rocket:count --vehicle-plate=LLO-914 --pa=3 --pr=50 --date={$yesterday}")
              //  ->cron('30 23,5,10,15 * * *')
             //   ->runInBackground();


            $schedule->command('sync5G:sync-photos --imei=352557104812691')->everyMinute()->runInBackground(); //8319 Prueba electricos
            $schedule->command('sync5G:sync-photos --imei=3525571048126912')->everyMinute()->runInBackground(); //TC1 Prueba electricos
            /*$schedule->command('rocket:count --vehicle-plate=NNV-011 --pa=3 --pr=50')
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();
            $schedule->command("rocket:count --vehicle-plate=NNV-011 --pa=3 --pr=50 --date={$yesterday}")
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();*/

            $schedule->command('sync5G:sync-photos --imei=352557104727600')->everyMinute()->runInBackground(); //6605
            $schedule->command('sync5G:sync-photos --imei=3525571047276001')->everyMinute()->runInBackground(); //6605
            $schedule->command('rocket:count --vehicle-plate=ETK-570 --pa=3 --pr=100 ')->everyMinute()->runInBackground();
            $schedule->command('rocket:count --vehicle-plate=ETK-570 --pa=3 --pr=50')
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();
            $schedule->command("rocket:count --vehicle-plate=ETK-570 --pa=3 --pr=50 --date={$yesterday}")
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();


            //$schedule->command('sync5G:sync-photos --imei=352557104834810')->hourly()->runInBackground(); // Vehicle 8511  Expreso Palmira
            //$schedule->command('sync5G:sync-photos --imei=352557104777777')->hourly()->runInBackground(); // Vehicle 8511  Expreso Palmira
            //$schedule->command('sync5G:sync-photos --imei=352557104123456789')->hourly()->runInBackground(); // Vehicle 8511  Expreso Palmira
            /*$schedule->command('rocket:count --vehicle-plate=LXS-895 --pa=3 --pr=50')
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();
            $schedule->command("rocket:count --vehicle-plate=LXS-895 --pa=3 --pr=50 --date={$yesterday}")
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();*/

            //$schedule->command('rocket:count --vehicle-plate=LXS-895 --pa=3 --pr=100 ')->everyThreeHours()->runInBackground();
            //$schedule->command("rocket:count --vehicle-plate=LXS-895 --pa=3 --pr=100 --date=$yesterday")->everyThreeHours()->runInBackground(); // Para procesar conteos de despacho de ayer. Aplica para rutas largas


             $schedule->command('sync5G:sync-photos --imei=352557104806420')->everyThirtyMinutes()->runInBackground(); // Vehicle 8339  Expreso Palmira
             $schedule->command('sync5G:sync-photos --imei=3525571048064201')->everyThirtyMinutes()->runInBackground(); // Vehicle 8339  Expreso Palmira
            //$schedule->command('rocket:count --vehicle-plate=NOK-250 --pa=3 --pr=100 ')->cron('30 23,5,10,15 * * *')->runInBackground();
            //$schedule->command("rocket:count --vehicle-plate=NOK-250 --pa=3 --pr=100 --date=$yesterday")->cron('30 23,5,10,15 * * *')->runInBackground(); // Para procesar conteos de despacho de ayer. Aplica para rutas largas


            $schedule->command('Sync4G:sync-photos --imei=352557104466092')->everyMinute()->runInBackground(); // Vehicle 8337  Expreso Palmira
            $schedule->command('Sync4G:sync-photos --imei=352557104777778')->everyMinute()->runInBackground(); // Vehicle 8337  Expreso Palmira
            $schedule->command('rocket:count --vehicle-plate=GEV-267 --pa=3 --pr=50')
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();
            $schedule->command("rocket:count --vehicle-plate=GEV-267 --pa=3 --pr=50 --date={$yesterday}")
                ->cron('30 23,5,10,15 * * *')
                ->runInBackground();


            /******************************************   4G   ***********************************************/

            // $schedule->command('Sync4G:sync-photos --imei=35255710402940')->everyMinute()->runInBackground(); // Vehicle 8419 EP
            //$schedule->command('rocket:count --vehicle-plate=WHW-596 --pa=3 --pr=100 ')->everyThreeHours()->runInBackground();
            //$schedule->command("rocket:count --vehicle-plate=WHW-596 --pa=3 --pr=100 --date=$yesterday")->everyThreeHours()->runInBackground(); // Para procesar conteos de despacho de ayer. Aplica para rutas largas

            $schedule->command('Sync4G:sync-photos --imei=352557104839116')->everyMinute()->runInBackground(); // Vehicle 8295 EP
            $schedule->command('rocket:count --vehicle-plate=WFR-266 --pa=3 --pr=100 ')->everyThreeHours()->runInBackground();
            $schedule->command("rocket:count --vehicle-plate=WFR-266 --pa=3 --pr=100 --date=$yesterday")->everyThreeHours()->runInBackground(); // Para procesar conteos de despacho de ayer. Aplica para rutas largas

            //$schedule->command('Sync4G:sync-photos --imei=352557104840536')->everyMinute()->runInBackground(); // vehicle 2685 EP

            $schedule->command('Sync4G:sync-photos --imei=352557104743888')->everyMinute()->runInBackground(); // vehicle 2667 EP
            //$schedule->command('rocket:count --vehicle-plate=KUK-593 --pa=3 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('Sync4G:sync-photos --imei=352557104840536')->everyMinute()->runInBackground(); // vehicle 2675 EP

            $schedule->command('Sync4G:sync-photos --imei=352557104723690')->everyMinute()->runInBackground(); // vehicle 2673  EP
            //$schedule->command('Sync4G:sync-photos --imei=351234567891011')->everyMinute()->runInBackground(); // vehicle 2673 2 DVR EP
            $schedule->command('rocket:count --vehicle-plate=KUK-485 --pa=3 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('Sync4G:sync-photos --imei=352557104831642')->everyMinute()->runInBackground(); // vehicle 8321  EP
            //$schedule->command('rocket:count --vehicle-plate=LLR-503 --pa=3 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('Sync4G:sync-photos --imei=352557104791564')->everyMinute()->runInBackground(); // vehicle 6601 EP
            $schedule->command('rocket:count --vehicle-plate=ETK-563 --pa=3 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('Sync4G:sync-photos --imei=352557104794196')->everyMinute()->runInBackground(); // vehicle 8503 EP
            //$schedule->command('rocket:count --vehicle-plate=LXS-891 --pa=3 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('Sync4G:sync-photos --imei=352557104808657')->everyMinute()->runInBackground(); // vehicle 2773 EP
            //$schedule->command('rocket:count --vehicle-plate=SSQ-806 --pa=3 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('Sync4G:sync-photos --imei=352557104839868')->everyMinute()->runInBackground(); // vehicle 1227 EP
            $schedule->command('rocket:count --vehicle-plate=LXU-030 --pa=3 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('Sync4G:sync-photos --imei=352557104788057')->everyMinute()->runInBackground(); // vehicle 2919 EP
            $schedule->command('rocket:count --vehicle-plate=NYK-142 --pa=3 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

           //$schedule->command('Sync4G:sync-photos --imei=352557104780641')->everyMinute()->runInBackground(); // vehicle 2907 EP
            //$schedule->command('rocket:count --vehicle-plate=NYK-137 --pa=3 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('Sync4G:sync-photos --imei=352557104807444')->everyMinute()->runInBackground(); // vehicle 2905 EP
            $schedule->command('rocket:count --vehicle-plate=NYK-136 --pa=3 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('Sync4G:sync-photos --imei=352557104746048')->everyMinute()->runInBackground(); // vehicle 2925 EP
            $schedule->command('rocket:count --vehicle-plate=NYK-212 --pa=3 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('Sync4G:sync-photos --imei=352557104841229')->everyMinute()->runInBackground(); // vehicle 2917 EP
            $schedule->command('rocket:count --vehicle-plate=NYK-141 --pa=3 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('Sync4G:sync-photos --imei=352557104452423')->everyMinute()->runInBackground(); // vehicle 2909 EP
            $schedule->command('rocket:count --vehicle-plate=NYK-138 --pa=3 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('Sync4G:sync-photos --imei=352557104840577')->everyMinute()->runInBackground(); // vehicle 2915 EP
            $schedule->command('rocket:count --vehicle-plate=NYK-140 --pa=3 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('Sync4G:sync-photos --imei=352557104749042')->everyMinute()->runInBackground(); // vehicle 2921 EP
            $schedule->command('rocket:count --vehicle-plate=NYK-143 --pa=3 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('Sync4G:sync-photos --imei=352557104485654')->everyMinute()->runInBackground(); // vehicle 2923 EP
            $schedule->command('rocket:count --vehicle-plate=NYK-211 --pa=3 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('Sync4G:sync-photos --imei=352557104765790')->everyMinute()->runInBackground(); // vehicle 2911 EP
            $schedule->command('rocket:count --vehicle-plate=NYK-139 --pa=3 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('Sync4G:sync-photos --imei=352557104788131')->everyMinute()->runInBackground(); // vehicle 6837 EP
            $schedule->command('rocket:count --vehicle-plate=ETK-183 --pa=3 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('Sync4G:sync-photos --imei=0970000110')->everyMinute()->runInBackground(); // vehicle 2031 EP
            $schedule->command('rocket:count --vehicle-plate=WHU-494 --pa=3 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('Sync4G:sync-photos --imei=0700000114')->everyMinute()->runInBackground(); // vehicle 2027 EP
            $schedule->command('rocket:count --vehicle-plate=WHU-492 --pa=3 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('Sync4G:sync-photos --imei=0700000178')->everyMinute()->runInBackground(); // vehicle 1985 EP

            $schedule->command('Sync4G:sync-photos --imei=352557104838886')->everyMinute()->runInBackground(); // vehicle 2833 EP


            

            /******************************************   3G   ***********************************************/

            $schedule->command('sync3GV2:sync-photos')->everyFiveMinutes()->runInBackground();


            $schedule->command('rocket:count --vehicle-plate=TST-001 --pa=3 --pr=5 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=SKR-579 --pa=2 --pr=5 ')->cron('0 * * * *')->runInBackground();
            $schedule->command('rocket:count --vehicle-plate=SKR-579 --pa=2 --pr=5 ')
                ->cron('55 23 * * *')
                ->runInBackground();

            /*********************************EXPRESO PALMIRA*********************************************/
            

            //$schedule->command('rocket:count --vehicle-plate=KUL-884 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=KUK-612 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=KUL-704 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=TJW-466 --pa=10 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=SPK-387 --pa=5 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=SPK385 --pa=3 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-185 --pa=5 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=EP-001 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ESY-699 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ESY-708 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ESY-707 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ESY-710 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ESY-709 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ESY-704 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-677 --pa=10 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-674 --pa=10 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-676 --pa=10 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-678 --pa=10 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-680 --pa=2 --pr=50 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-673 --pa=2 --pr=50 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-679 --pa=10 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=ETK-563 --pa=10 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-569 --pa=2 --pr=50 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-572 --pa=2 --pr=50 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-573 --pa=2 --pr=50 ')->cron('0 */4 * * *')->runInBackground();

           // $schedule->command('rocket:count --vehicle-plate=ETK-570 --pa=2 --pr=50 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-571 --pa=2 --pr=50 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=WHV-817 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=WHV-813 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=SPK-391 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=KUL-888 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=KUK-632 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-149 --pa=10 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-151 --pa=10 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-150 --pa=10 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=KUK-565 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=KUL-983 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=KUK-643 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=SPK-388 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-812 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-811 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-813 --pa=2 --pr=50 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-814 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=KUL-705 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-810 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ESY-706 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-963 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ESY-705 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ESY-703 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ESY-702 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=KUL-890 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ESY-700 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ESY-698 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETL-006 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-964 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-959 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-958 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-957 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-960 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-965 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-962 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=KUL-886 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-961 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=KUK-591 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=KUK-644 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=KUL-982 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=WHU-522 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=SPK-389 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=SPK-386 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ESY-701 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=KUL-981 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=KUK-595 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=SPK-384 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=ETK-378 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=KUL-713 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=KUL-883 --pa=2 --pr=10 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=KUL-707 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=SPK-392 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=SPK-175 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=KUK-642 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=SPK-383 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=KUL-708 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            $schedule->command('rocket:count --vehicle-plate=KUM-722 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();


            /**********************************VALLEDUPAR*************************************************/

            //$schedule->command('rocket:count --vehicle-plate=FXS-240 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=WCY-762 --pa=2 --pr=8 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=WCY-768 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=WYC-769 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=WCY-770 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=WCY-771 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=WNL-372 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=WCY-828 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=WCY-763 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=GIM-079 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=GIM-077 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=GIM-080 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=GIM-078 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=FXS-241 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=WCY-764 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=WCY-766 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=FXS-261 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=WCY-767 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=WNL-369 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=FXS-260 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=WNL-370 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=TJW-190 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=TJW-192 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=WCY-765 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground();

            //$schedule->command('rocket:count --vehicle-plate=WCY-829 --pa=2 --pr=20 ')->cron('0 */4 * * *')->runInBackground(); pasa a 5004




            /**********************************TRANSPUBENZA*************************************************/



            //================================================================================
            //======================== Sync data passengers from EP ==========================
            //================================================================================
            $schedule->command("lm:sync --company=39 --date=$yesterday")->everyThreeHours();

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
