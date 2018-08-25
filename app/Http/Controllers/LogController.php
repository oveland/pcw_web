<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogController extends Controller
{
    public function registerGPS(Request $request){

        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $orientation = $request->get('orientation');
        $timestamp = $request->get('timestamp');
        $vehicle = $request->get('vehicle');
        $type = $request->get('type');

        $query = "INSERT INTO app_log_gps (latitude, longitude, orientation, timestamp, vehicle, type) values ($latitude, $longitude, $orientation, $timestamp, $vehicle, $type)";

        $insert = \DB::insert($query);
        return response()->json([
          'success' => $insert
        ]);
    }
}
