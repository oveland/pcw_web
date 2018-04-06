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

    'back_days_for_send_sms' => env('BACK_DAYS_FOR_SEND_SMS',10),

    /*
    |--------------------------------------------------------------------------
    | Back Time For Send SMS
    |--------------------------------------------------------------------------
    |
    | Set the time for gps in NO REPORT state
    |
    */

    'back_time_for_send_sms' => env('BACK_TIME_FOR_SEND_SMS','00:10:00'),

    /*
    |--------------------------------------------------------------------------
    |
    |--------------------------------------------------------------------------
    |
    | Max string length for SMS AT Commands
    |
    */

    'sms_max_length_for_gps' => env('SMS_MAX_LENGTH_FOR_GPS',140),

    /*
    |--------------------------------------------------------------------------
    | Start time for send RESET command
    |--------------------------------------------------------------------------
    |
    | Start time for send RESET command via SMS for down GPS
    |
    */

    'sms_reset_start_at' => env('SMS_RESET_START_AT','05:00'),

    /*
    |--------------------------------------------------------------------------
    | End time for send RESET command
    |--------------------------------------------------------------------------
    |
    | End time for send RESET command via SMS for down GPS
    |
    */

    'sms_reset_end_at' => env('SMS_RESET_END_AT','19:00'),

    /*
     *
    */
    'sms_vehicle_report' => env('SMS_VEHICLE_REPORT'),

    /*
     *
    */
    'sms_vehicle_sim' => env('SMS_VEHICLE_SIM'),
];