<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Utils\Geolocation;
use App\Models\Routes\Route;
use DB;
use Illuminate\Http\Request;

class CalibrationControlPointsController extends Controller
{
    public function calibrate(Request $request)
    {
        $route = Route::find($request->get('route'));

        if ($route) {
            if ($request->get('paintKmz')) $this->paintKmz($route);
            else {
                $controlPointsCalibrated = $this->process($route);
                $this->printCalibration($route, $controlPointsCalibrated);
                $updated = $this->saveCalibratedControlPoints($controlPointsCalibrated);
                dump("Registers updated: $updated of ".count($controlPointsCalibrated));
                if ($request->get('migrate')){
                    dump("**** Auto migrating control points...");
                    $migrationController = new MigrationController();
                    $migrationController->migrateControlPoints();
                }
            }
        } else {
            dd('No route found');
        }
    }

    public function process(Route $route)
    {
        $controlPoints = $route->controlPoints->sortBy('order');
        $routeCoordinates = collect(Geolocation::getRouteCoordinates($route->url));

        $controlPointsCalibrated = collect([]);
        foreach ($routeCoordinates as $index => $routeCoordinate) {
            $nextControlPoint = $controlPoints->first();
            $distanceCP = null;
            if ($nextControlPoint) {
                $distanceCP = Geolocation::getDistance(
                    $routeCoordinate->latitude, $routeCoordinate->longitude,
                    $nextControlPoint->latitude, $nextControlPoint->longitude
                );
                if ($distanceCP < 300) {
                    $controlPointLocation = [
                        'latitude' => $nextControlPoint->latitude,
                        'longitude' => $nextControlPoint->longitude,
                    ];
                    // Take only the next 50 route coordinates from kmz
                    $nearestNextRouteCoordinates = $routeCoordinates->filter(function ($value, $i) use ($index) {
                        return $i >= $index && $i <= ($index + 50);
                    });

                    $nearestRouteCoordinate = Geolocation::findNearestCoordinateFromLocation($controlPointLocation, $nearestNextRouteCoordinates);

                    $controlPointsCalibrated->put(
                        $nextControlPoint->id,
                        (object)[
                            'id' => $nextControlPoint->id,
                            'name' => $nextControlPoint->name,
                            'latitude' => $nearestRouteCoordinate->latitude,
                            'longitude' => $nearestRouteCoordinate->longitude,
                            'distanceFromDispatch' => intval($nearestRouteCoordinate->distance),
                        ]
                    );
                    $controlPoints->shift();
                }

            }
        }
        return $controlPointsCalibrated;
    }

    public function saveCalibratedControlPoints($controlPointsCalibrated)
    {
        $updates = 0;
        $prevControlPointCalibrated = null;
        $lastControlPointID = $controlPointsCalibrated->last()->id;
        $distanceNextPoint = 0;
        foreach ($controlPointsCalibrated as $controlPointID => $controlPointCalibrated) {
            if ($prevControlPointCalibrated) {
                $distanceNextPoint = intval($controlPointCalibrated->distanceFromDispatch - $prevControlPointCalibrated->distanceFromDispatch);
                $update = $this->executeUpdateControlPoint(
                    $prevControlPointCalibrated->id,
                    $prevControlPointCalibrated->latitude,
                    $prevControlPointCalibrated->longitude,
                    $distanceNextPoint,
                    $prevControlPointCalibrated->distanceFromDispatch
                );
                $updates += $update ? 1 : 0;
            }
            $prevControlPointCalibrated = $controlPointCalibrated;

            if( $controlPointID == $lastControlPointID ){
                $update = $this->executeUpdateControlPoint(
                    $prevControlPointCalibrated->id,
                    $prevControlPointCalibrated->latitude,
                    $prevControlPointCalibrated->longitude,
                    $distanceNextPoint,
                    $prevControlPointCalibrated->distanceFromDispatch
                );
                $updates += $update ? 1 : 0;
            }
        }

        return $updates;
    }

    public function executeUpdateControlPoint($id, $latitude, $longitude, $distanceNextPoint, $distanceFromDispatch)
    {
        return DB::update("
                    UPDATE puntos_control_ruta SET 
                    lat = $latitude, 
                    lng = $longitude, 
                    distancia_punto_siguiente = $distanceNextPoint, 
                    distancia_desde_despacho = $distanceFromDispatch 
                    WHERE secpuntos_control_ruta = $id
                ");
    }

    public function paintKmz(Route $route)
    {
        $coordinates = Geolocation::getRouteCoordinates($route->url);
        $this->printCoordinates($coordinates);
    }

    public function printCoordinates($coordinates)
    {
        $content = "";
        foreach ($coordinates as $coordinate) {
            $coordinate = (object)$coordinate;
            $content .= "$coordinate->latitude, $coordinate->longitude<br>";
        }
        echo($content);
    }

    public function printCalibration($route, $controlPointsCalibrated)
    {
        $routeCoordinates = Geolocation::getRouteCoordinates($route->url);
        $content = "";
        foreach ($routeCoordinates as $routeCoordinate) {
            $found = "";
            $controlPointCalibrated = $controlPointsCalibrated->where('distanceFromDispatch', intval($routeCoordinate->distance))->first();

            if ($controlPointCalibrated) {
                $found = ">> $controlPointCalibrated->name | $controlPointCalibrated->latitude, $controlPointCalibrated->longitude";
            }
            $content .= "<pre style='margin: 0'>$routeCoordinate->latitude, &#9;$routeCoordinate->longitude &#9;-> " . intval($routeCoordinate->distance) . "&#9; $found</pre>";
        }
        echo($content);
    }
}
