<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 4/07/2017
 * Time: 5:25 PM
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Route Distance Threshold
    |--------------------------------------------------------------------------
    |
    | Defines the distance threshold that checks if vehicle is off road
    | The distance threshold should be specified in meters
    |
    */

    'route_distance_threshold' => 50,

    /*
    |--------------------------------------------------------------------------
    | Distance Threshold for check seat coordinates | ONLY TAXCENTRAL
    |--------------------------------------------------------------------------
    |
    | Defines the distance threshold that checks if active/inactive location is on road
    | The distance threshold should be specified in meters
    |
    */

    'seat_distance_threshold' => 100,

    /*
    |--------------------------------------------------------------------------
    | Route Sampling Radius
    |--------------------------------------------------------------------------
    |
    | Defines the radius distance to take the coordinates samples from kml file for Law of Cosines process
    | The radius distance should be specified in meters
    |
    */

    'route_sampling_radius' => 800,

    /*
    |--------------------------------------------------------------------------
    | Route Sampling Area
    |--------------------------------------------------------------------------
    |
    | Defines the side of square that define for sampling filter route coordinates
    | This distance should be calculated from decode distance between two geolocation points
    |
    */
    'route_sampling_area' => 0.005,

    /*
    |--------------------------------------------------------------------------
    | Google Api token for google maps
    |--------------------------------------------------------------------------
    |
    */
    'google_api_token' => 'AIzaSyCNNGhrjaS4zQFwWVfazS2600h9hV-QpSA',
    //'google_api_token' => 'AIzaSyC_BkN0qt9j8gUEe7GdW2Bxu3vgQPTrSP8',

    /*
    |--------------------------------------------------------------------------
    | Threshold for invalid recorder counter per day
    |--------------------------------------------------------------------------
    |
    | Defines the max value for a valid recorder counter total per day
    |
    */
    'max_recorder_counter_per_day_threshold' => 10000,

    /*
    |--------------------------------------------------------------------------
    | Threshold for invalid recorder counter per vehicle    |
    |--------------------------------------------------------------------------
    |
    | Defines the max value for a valid recorder counter total per vehicle
    |
    */
    'max_recorder_counter_per_vehicle_threshold' => 1000,

    /*
    |--------------------------------------------------------------------------
    | Threshold for report a vehicle as parked   |
    |--------------------------------------------------------------------------
    |
    | Defines the time for report a vehicle as parked
    |
    */
    'time_parked_vehicle_threshold' => '00:10:00',
];