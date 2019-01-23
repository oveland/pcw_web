<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Utils\Geolocation;
use Auth;
use App\Models\Company\Company;
use App\Models\Routes\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MigrationControlPointController extends Controller
{
    const OLD_TABLES = [
        'companies' => 'empresa',
        'routes' => 'ruta',
        'users' => 'acceso',
        'vehicles' => 'crear_vehiculo',
        'control_points' => 'puntos_control_ruta'
    ];

    public function getControlPoints()
    {
        if( Auth::user()->isAdmin() )$companies = Company::whereActive(true)->orderBy('short_name')->get();
        else $companies = [Auth::user()->company];
        return view('migrations.cp',compact('companies'));
    }

    public function compare(Route $route)
    {
        $control_points = DB::table(MigrationController::OLD_TABLES['control_points'])->where('id_ruta','=',$route->id)->orderBy('orden')->get();
        dd($control_points->toArray());
    }

    public function exportCoordinates(Route $route, Request $request)
    {
        $coordinates = Geolocation::getRouteCoordinates($route->url);
        $content = "";
        foreach ($coordinates as $coordinate){
            $coordinate = (object) $coordinate;
            if( $request->get('with-extras') )$content.="$coordinate->index > $coordinate->latitude, $coordinate->longitude > $coordinate->distance\n";
            else $content.="$coordinate->index ($coordinate->distance m) > $coordinate->latitude, $coordinate->longitude\n";

        }

        $filename = "Coordinates $route->name.txt";
        // Set headers necessary to initiate a download of the text file, with the specified name
        $headers = array(
            'Content-Type' => 'plain/txt',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            'Content-Length' => strlen($content),
        );

        return \Response::make($content, 200, $headers);
    }
}
