<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Utils\Geolocation;
use App\OffRoad;
use Illuminate\Http\Request;
use Image;

class GeolocationController extends Controller
{
    /**
     * @param $latitdue
     * @param $longitude
     * @return mixed
     */
    public function getAddressFromCoordinates(OffRoad $offRoad)
    {
        sleep(1); // Because google (Free layer) only lets 50 request/second
        return Geolocation::getAddressFromCoordinates($offRoad->latitude, $offRoad->longitude);
    }

    public function getImageFromCoordinate(OffRoad $offRoad)
    {
        $route = $offRoad->dispatchRegister->route;
        $routeCoordinates = RouteReportController::getRouteCoordinates($route->url);
        $nearestRouteCoordinates = $this->filterNearestRouteCoordinates($offRoad, $routeCoordinates);

        $routePath = "path=color:0x0000ff";
        foreach ($nearestRouteCoordinates as $nearestRouteCoordinate){
            $routePath .= '|'.$nearestRouteCoordinate['latitude'].','.$nearestRouteCoordinate['longitude'];
        }

        $url = "https://maps.googleapis.com/maps/api/staticmap?size=500x200&maptype=roadmap\&center=$offRoad->latitude,$offRoad->longitude&zoom=16&$routePath&markers=size:mid%7Ccolor:0xCC2701|$offRoad->latitude,$offRoad->longitude&key=" . config('road.google_api_token');

        $image = Image::make($url);
        return $image->response('jpg');
    }

    /**
     * @param $location
     * @param $route_coordinates
     * @return array|\Illuminate\Support\Collection
     */
    private function filterNearestRouteCoordinates($location, $route_coordinates)
    {
        $location_latitude = $location->latitude;
        $location_longitude = $location->longitude;
        //dump($location_latitude.', '.$location_longitude);
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
