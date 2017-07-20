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
    | Google Api token for google maps
    |--------------------------------------------------------------------------
    |
    */
    'google_api_token' => 'AIzaSyC_BkN0qt9j8gUEe7GdW2Bxu3vgQPTrSP8'
];