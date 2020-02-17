<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 17/03/2018
 * Time: 10:29 PM
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Threshold for truncate speeding
    |--------------------------------------------------------------------------
    */
    'threshold_truncate_speeding' => 150,

    'maintenance' => [
        /*
        |--------------------------------------------------------------------------
        | Maintenance period days
        |--------------------------------------------------------------------------
        | Defines the period in days for scheduled maintenance of a vehicle
        */
        'period_in_days' => 15,

        /*
        |--------------------------------------------------------------------------
        | Maintenance default observations
        |--------------------------------------------------------------------------
        */
        'default_observations' => "En mantenimiento programado",

        /*
        |--------------------------------------------------------------------------
        | Check if should check assignable days.
        |   When is false all days are assignable
        |--------------------------------------------------------------------------
        */
        'check_assignable_days' => true,

        /*
        |--------------------------------------------------------------------------
        | Scheduled months
        |--------------------------------------------------------------------------
        | Defines the number of months to automatic scheduled maintenance
        */
        'scheduled_months' => 12,
    ]
];