<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\MigrationController;
use App\Services\API\Apps\MyRouteService;
use App\Services\API\Apps\PCWProprietaryService;
use App\Services\API\Apps\PCWTrackService;
use App\Services\API\Web\PCWPassengersService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class APIController extends Controller
{
    /**
     * API for mobile apps
     *
     * @param $appName
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function app($appName, Request $request)
    {
        switch ($appName) {
            case 'app-my-route':
                return MyRouteService::serve($request);
                break;

            case 'app-pcw-track':
                return PCWTrackService::serve($request);
                break;

            case 'app-pcw-proprietary':
                return PCWProprietaryService::serve($request);
                break;
            default:
                abort(403);
                break;
        }
    }

    /**
     * API for web process
     *
     * @param $api
     * @param $service
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function web($api, $service, Request $request)
    {
        switch ($api) {
            case 'passengers':
                return PCWPassengersService::serve($service, $request);
                break;

            case 'migrations':
                $migrationController = new MigrationController();
                switch ($request->get('action')) {
                    case 'vehicles':
                        $migrationController->migrateVehicles($request);
                        break;
                }
                break;
            default:
                abort(403);
                break;
        }
    }
}
