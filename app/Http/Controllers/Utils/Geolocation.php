<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 26/07/2017
 * Time: 10:23 PM
 */

namespace App\Http\Controllers\Utils;

use App\Http\Controllers\ReportRouteController;
use GuzzleHttp\Client;
use Image;
use ZipArchive;

class Geolocation
{
    /**
     * Get route coordinates from google kmz file
     *
     * @param $url
     * @return \Illuminate\Support\Collection
     */
    public
    static function getRouteCoordinates($url)
    {
        $milliseconds = round(microtime(true) * 1000);
        $dir_name = "ziptmp$milliseconds";
        $file = 'doc.kml';

        $ext = pathinfo($url, PATHINFO_EXTENSION);
        $temp = tempnam(sys_get_temp_dir(), $ext);
        copy($url, $temp);

        $zip = new ZipArchive();
        if ($zip->open($temp, ZIPARCHIVE::CREATE) === TRUE) {
            $zip->extractTo($dir_name);
            $zip->close();
        }

        $data = file_get_contents($dir_name . '/' . $file);

        unlink($temp);
        array_map('unlink', glob("$dir_name/*.*"));
        chmod($dir_name, 0777);
        rmdir($dir_name);

        $dataXML = simplexml_load_string($data);
        $documents = $dataXML->Document->Folder;
        $documents = $documents->Placemark ? $documents : $dataXML->Document;

        /* Extract coordinates for xml file */
        $routeCoordinates = collect();
        $prevLatitude = null;
        $prevLongitude = null;
        $distance = 0;
        $indexCoordinate = 0;
        foreach ($documents as $document) {
            foreach ($document->Placemark as $placemark) {
                $routeCoordinatesXML = explode(' ', trim($placemark->LineString->coordinates));
                foreach ($routeCoordinatesXML as $index => $routeCoordinate) {
                    $array_coordinates = collect(explode(',', trim($routeCoordinate)));

                    if ($array_coordinates->count() > 2) {
                        list($longitude, $latitude, $angle) = explode(',', trim($routeCoordinate));
                        $latitudeRoute = doubleval($latitude);
                        $longitudeRoute = doubleval($longitude);

                        $distance += ($prevLatitude && $prevLongitude) ? self::getDistance($prevLatitude, $prevLongitude, $latitudeRoute, $longitudeRoute) : 0;
                        $routeCoordinates->push((object)[
                            'index' => $indexCoordinate,
                            'latitude' => $latitudeRoute,
                            'longitude' => $longitudeRoute,
                            'distance' => intval($distance)
                        ]);
                        $indexCoordinate++;
                        $prevLatitude = $latitudeRoute;
                        $prevLongitude = $longitudeRoute;
                    }
                }
            }
        }

        return $routeCoordinates;
    }

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
        $earth_radius = 6372.8;
        $dLat = deg2rad($latitude2 - $latitude1);
        $dLon = deg2rad($longitude2 - $longitude1);
        $latitude1 = deg2rad($latitude1);
        $latitude2 = deg2rad($latitude2);

        $a = sin($dLat / 2) * sin($dLat / 2) + sin($dLon / 2) * sin($dLon / 2) * cos($latitude1) * cos($latitude2);
        $c = 2 * asin(sqrt($a));

        return $earth_radius * $c * 1000;
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
        $address = __('Unavailable');
        if ($latitude == 0 || $longitude == 0 || abs(intval($latitude)) > 5 || abs(intval($longitude)) > 90 ) return $address."*";
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&key=" . config('road.google_api_token');

        try {
            $client = new Client();
            $response = $client->get($url)->getBody()->getContents();
            $data = (object)json_decode($response, true);
            $result = (object) collect($data->results)->first();

            $address = collect(explode(",", $result->formatted_address))->take(3)->implode(",") ;
        } catch (\Exception $e) {
        }

        return $address;
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
        $routeCoordinates = Geolocation::getRouteCoordinates($route->url);
        $nearestRouteCoordinates = self::filterNearestRouteCoordinates($location, $routeCoordinates);

        $routePath = "path=color:0x0000ff";
        foreach ($nearestRouteCoordinates as $nearestRouteCoordinate) {
            $routePath .= '|' . $nearestRouteCoordinate->latitude . ',' . $nearestRouteCoordinate->longitude;
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
    public static function filterNearestRouteCoordinates($location, $route_coordinates)
    {
        $location = (object) $location;
        $route_coordinates = (object) $route_coordinates;

        $location_latitude = $location->latitude;
        $location_longitude = $location->longitude;
        $threshold = config('road.route_sampling_area');

        $threshold_location = (object)[
            'la_up' => $location_latitude + $threshold,
            'la_down' => $location_latitude - $threshold,
            'lo_up' => $location_longitude + $threshold,
            'lo_down' => $location_longitude - $threshold
        ];

        $route_coordinates = collect($route_coordinates);
        $route_coordinates = $route_coordinates->filter(function ($value, $key) use ($threshold_location) {
            return
                $value->latitude > $threshold_location->la_down && $value->latitude < $threshold_location->la_up &&
                $value->longitude > $threshold_location->lo_down && $value->longitude < $threshold_location->lo_up;
        })->values();

        return $route_coordinates;
    }

    public static function findNearestCoordinateFromLocation($location, $routeCoordinates)
    {
        $nearestRouteCoordinate = [];
        $location = (object) $location;
        $routeCoordinates = (object) $routeCoordinates;
        $minRadius = 10000;
        foreach ($routeCoordinates as $routeCoordinate){
            $radius = self::getDistance(
                $routeCoordinate->latitude, $routeCoordinate->longitude,
                $location->latitude, $location->longitude
            );
            if($radius < $minRadius){
                $nearestRouteCoordinate = $routeCoordinate;
                $minRadius = $radius;
            }
        }
        return $nearestRouteCoordinate;
    }
}