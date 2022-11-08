<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\MigrationController;
use App\Services\API\Apps\MyRouteService;
use App\Services\API\Apps\PCWProprietaryService;
use App\Services\API\Apps\PCWTrackService;
use App\Services\API\Web\Reports\PCWRouteService;
use App\Services\API\Web\Reports\APIReportService;
use App\Services\API\Web\PCWPassengersService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class APIController extends Controller
{
    protected $apiReportService;
    protected $passengersService;
    protected $routeService;

    /**
     * APIController constructor.
     * @param APIReportService $apiReportService
     * @param PCWPassengersService $passengersService
     */
    public function __construct(APIReportService $apiReportService, PCWPassengersService $passengersService, PCWRouteService $routeService)
    {
        $this->apiReportService = $apiReportService;
        $this->passengersService = $passengersService;
        $this->routeService = $routeService;
    }


    /**
     * API for mobile apps
     *
     * @param $appName
     * @param Request $request
     * @return JsonResponse
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
     * @return JsonResponse | string
     */
    public function web($api, $service, Request $request)
    {
        switch ($api) {
            case 'passengers':
                return $this->passengersService->serve($service, $request);
                break;

            case 'routes':
                return $this->routeService->serve($service, $request);
                break;

            case 'reports':
                return $this->apiReportService->serve($service, $request);
                break;

            case 'migrations':
                $migrationController = new MigrationController();
                switch ($service) {
                    case 'companies':
                        $migrationController->migrateCompanies($request);
                        break;
                    case 'routes':
                        $migrationController->migrateRoutes($request);
                        break;
                    case 'dispatches':
                        $migrationController->migrateDispatches($request);
                        break;
                    case 'users':
                        $migrationController->migrateUsers($request);
                        break;
                    case 'vehicles':
                        $migrationController->migrateVehicles($request);
                        break;
                    case 'control-points':
                        $migrationController->migrateControlPoints($request);
                        break;
                    case 'fringes':
                        $migrationController->migrateFringes($request);
                        break;
                    case 'control-point-times':
                        $migrationController->migrateControlPointTimes($request);
                        break;
                    case 'all':
                        $migrationController->migrateCompanies($request);
                        $migrationController->migrateRoutes($request);
                        $migrationController->migrateDispatches($request);
                        $migrationController->migrateUsers($request);
                        $migrationController->migrateVehicles($request);
                        $migrationController->migrateControlPoints($request);
                        $migrationController->migrateFringes($request);
                        $migrationController->migrateControlPointTimes($request);
                        break;
                }
                break;
            default:
                return response()->json([
                    'error' => true,
                    'message' => 'Not found'
                ]);
                break;
        }
    }
}
