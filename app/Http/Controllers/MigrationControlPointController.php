<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Utils\Geolocation;
use App\Models\Routes\RouteGoogle;
use Auth;
use App\Models\Company\Company;
use App\Models\Routes\Route;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MigrationControlPointController extends Controller
{
    const OLD_TABLES = [
        'companies' => 'empresa',
        'routes' => 'ruta',
        'users' => 'acceso',
        'vehicles' => 'crear_vehiculo',
        'control_points' => 'puntos_control_ruta'
    ];

    public function getControlPoints(Request $request)
    {
        $company = $request->get('company');
        $route = $request->get('route');

        if (Auth::user()->isAdmin()) $companies = Company::orderBy('short_name')->get();
        else abort(403);

        if ($company) $companies = $companies->where('id', $company);
        $companyRequest = $company;

        return view('migrations.cp', compact(['companies', 'companyRequest', 'route']));
    }

    public function uploadKmz(Request $request)
    {
        $route = Route::find($request->get('route'));
        if (!$route) dd("Route selected doesn't exists");

        $fileName = $route->id . '_' . str_replace(' ', '', explode('.', $request->get('name'))[0]) . '.kmz';

        Storage::disk('google')->putFileAs('', $request->file('kmz'), $fileName);

        $routeGoogle = $route->routeGoogle;
        if(!$routeGoogle)$routeGoogle = new RouteGoogle();
        $routeGoogle->id_ruta = $route->id;
        $routeGoogle->url = Storage::disk('google')->url($fileName);
        $route->url = $routeGoogle->url;

        $routeGoogle->save();
        $route->save();

        return redirect(route('migrate-cp', ['company' => $request->get('company')]));
    }

    public function compare(Route $route)
    {
        $control_points = DB::table(MigrationController::OLD_TABLES['control_points'])->where('id_ruta', '=', $route->id)->orderBy('orden')->get();
        dd($control_points->toArray());
    }

    public function downloadKmz(Route $route)
    {
        return response()->download($route->url);
    }

    public function migrateTable($table)
    {
        $client = new Client(['base_uri' => new Uri(config('app.url') . '/api/v1/migrations/')]);

        return $client->get($table)->getBody()->getContents();
    }

    public function calibrateRoute(Route $route, $apply)
    {

        $client = new Client(['base_uri' => new Uri(config('gps.server.url'))]);

        $apply = $apply == "true" ? "&apply=true" : "";

        return $client->get("api/control-points?api-action=calibrate&route=$route->id" . $apply)->getBody()->getContents();
    }

    public function exportCoordinates(Route $route, Request $request)
    {
        $coordinates = Geolocation::getRouteCoordinates($route->url);
        $content = "";
        foreach ($coordinates as $coordinate) {
            $coordinate = (object)$coordinate;
            if ($request->get('with-extras')) $content .= "$coordinate->index > $coordinate->latitude, $coordinate->longitude > $coordinate->distance\n";
            else $content .= "$coordinate->latitude, $coordinate->longitude\n";

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
