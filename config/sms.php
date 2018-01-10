<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 09/01/2018
 * Time: 09:25 AM
 */

return [

    /*
    |--------------------------------------------------------------------------
    | SMS Api id
    |--------------------------------------------------------------------------
    |
    | SMS api id with www.hablame.co
    |
    */

    'api_id' => env('SMS_API_ID'),

    /*
    |--------------------------------------------------------------------------
    | SMS Api Key
    |--------------------------------------------------------------------------
    |
    | SMS api key with www.hablame.co
    |
    */

    'api_key' => env('SMS_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Back Days For Send SMS
    |--------------------------------------------------------------------------
    |
    | Set the days for gps in NO REPORT state
    |
    */

    'back_days_for_send_sms' => env('BACK_DAYS_FOR_SEND_SMS',10),/*

    |--------------------------------------------------------------------------
    | Back Time For Send SMS
    |--------------------------------------------------------------------------
    |
    | Set the time for gps in NO REPORT state
    |
    */

    'back_time_for_send_sms' => env('BACK_TIME_FOR_SEND_SMS','00:10:00'),
];