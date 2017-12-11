<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 26/07/2017
 * Time: 10:23 PM
 */

namespace App\Http\Controllers\Utils;

use App\Http\Controllers\RouteReportController;
use Image;

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

        sleep(1);

        $json = collect(json_decode($response, true));
        $address = (object)collect($json->first())->first();
        try {
            $address = explode(',', $address->formatted_address);
        } catch (\Exception $e) {
            return "No disponible";
        }
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

    public static function getImageRouteWithANearLocation($route, $location)
    {
        $routeCoordinates = RouteReportController::getRouteCoordinates($route->url);
        $nearestRouteCoordinates = self::filterNearestRouteCoordinates($location, $routeCoordinates);

        $routePath = "path=color:0x0000ff";
        foreach ($nearestRouteCoordinates as $nearestRouteCoordinate) {
            $routePath .= '|' . $nearestRouteCoordinate['latitude'] . ',' . $nearestRouteCoordinate['longitude'];
        }

        $url = "https://maps.googleapis.com/maps/api/staticmap?size=500x200&maptype=roadmap\&center=$location->latitude,$location->longitude&zoom=16&$routePath&markers=size:mid%7Ccolor:0xCC2701|$location->latitude,$location->longitude&key=" . config('road.google_api_token');

        $image = Image::make($url);
        return $image->response('jpg');
    }

    /**
     * @param $latitude
     * @param $longitude
     * @return mixed
     */
    public static function getImageLocationFromCoordinates($latitude, $longitude)
    {
        $routePath = "path=color:0x0000ff";
        $url = "https://maps.googleapis.com/maps/api/staticmap?size=500x200&maptype=roadmap\&center=$latitude,$longitude&zoom=16&$routePath&markers=size:mid%7Ccolor:0xCC2701|$latitude,$longitude&key=" . config('road.google_api_token');

        $image = Image::make($url);
        return $image->response('jpg');
    }

    /**
     * @param $location
     * @param $route_coordinates
     * @return array|\Illuminate\Support\Collection
     */
    private static function filterNearestRouteCoordinates($location, $route_coordinates)
    {
        $location_latitude = $location->latitude;
        $location_longitude = $location->longitude;
        $threshold = config('road.route_sampling_area');

        $threshold_location = [
            'la_up' => $location_latitude + $threshold,
            'la_down' => $location_latitude - $threshold,
            'lo_up' => $location_longitude + $threshold,
            'lo_down' => $location_longitude - $threshold
        ];

        $route_coordinates = collect($route_coordinates);
        $route_coordinates = $route_coordinates->filter(function ($value, $key) use ($threshold_location) {
            return
                $value['latitude'] > $threshold_location['la_down'] && $value['latitude'] < $threshold_location['la_up'] &&
                $value['longitude'] > $threshold_location['lo_down'] && $value['longitude'] < $threshold_location['lo_up'];
        })->values();

        return $route_coordinates;
    }
}