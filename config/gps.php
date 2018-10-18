<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 07/02/2018
 * Time: 09:25 AM
 */

return [

    'server' => [
        'url' => env('GPS_SERVER_URL', 'http://localhost:8181'),
        'urlAPI' => env('GPS_SERVER_URL', 'http://localhost:8181')."/api"
    ],

    /*
    |--------------------------------------------------------------------------
    | Check time for NO report on GPS with status Power ON
    |--------------------------------------------------------------------------
    */
    'gps_time_for_NO_report_power_ON' => env('GPS_TIME_FOR_NO_REPORT_POWER_ON','00:05:00'),

    /*
    |--------------------------------------------------------------------------
    | Check time for NO report on GPS with status Power OFF
    |--------------------------------------------------------------------------
    */
    'gps_time_for_NO_report_power_OFF' => env('GPS_TIME_FOR_NO_REPORT_POWER_OFF','00:15:00'),


];