<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 26/07/2017
 * Time: 10:23 PM
 */

namespace App\Http\Controllers\Utils;

class Geolocation
{
    /**
     * Get distance in meters from two coordinates in decimal
     *
     * @param $latitude1
     * @param $longitude1
     * @param $latitude2
     * @param $longitude2
     * @return int
     */
    public static function getDistance($latitude1, $longitude1, $latitude2, $longitude2)
    {
        $earth_radius = 6371;

        $dLat = deg2rad($latitude2 - $latitude1);
        $dLon = deg2rad($longitude2 - $longitude1);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * asin(sqrt($a));
        $d = $earth_radius * $c;

        return $d * 1000;
    }

    /**
     * Get Address from coordinates
     *
     * @param $latitude
     * @param $longitude
     * @return mixed
     */
    public static function getAddressFromCoordinates($latitude, $longitude)
    {
        if ($latitude == 0 || $longitude == 0) return 'Invalid Address';
        $url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=false&token=" . config('road.google_api_token');
        $response = file_get_contents($url);
        $json = collect(json_decode($response, true));
        $address = (object)collect($json->first())->first();
        $address = explode(',', $address->formatted_address);
        sleep(0.02);
        return $address[0];
    }

    /**
     * Get angle of C between three distances (a,b,c) through Law of Cosines
     *
     * @param $a
     * @param $b
     * @param $c
     * @return float
     */
    public static function getAngleC($a, $b, $c)
    {
        $argument = (pow($a, 2) + pow($b, 2) - pow($c, 2)) / (2 * $a * $b);
        if (abs($argument) > 1) return 180;/* Assumes on road */
        $angle_radians = acos($argument);
        return rad2deg($angle_radians);
    }

    /**
     * Get the angle C for threshold distance from route road
     *
     * @param $threshold_distance
     * @param $a
     * @param $b
     * @return float
     */
    public static function getThresholdAngleC($threshold_distance, $a, $b)
    {
        return rad2deg(acos($threshold_distance / $a) + acos($threshold_distance / $b));
    }
}