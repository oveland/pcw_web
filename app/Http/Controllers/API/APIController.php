<?php

namespace App\Http\Controllers\API;

use App;
use App\Http\Controllers\MigrationController;
use App\Services\API\Web\Reports\APIReportService;
use App\Services\API\Web\PCWPassengersService;
use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class APIController extends Controller
{
    protected $apiReportService;
    protected $passengersService;

    /**
     * APIController constructor.
     * @param APIReportService $apiReportService
     * @param PCWPassengersService $passengersService
     */
    public function __construct(APIReportService $apiReportService, PCWPassengersService $passengersService)
    {
        $this->apiReportService = $apiReportService;
        $this->passengersService = $passengersService;
    }


    /**
     * @api v2.0
     *
     * Serve API on Version 2.0
     *
     * @example For API Web:    /v2/web/reports/control-points?foo=bar
     * @example For API Apps:   /v2/app/rocket?foo=bar
     *
     * @param $api
     * @param $name
     * @param $service
     * @return mixed
     * @throws BindingResolutionException
     */
    public function serve($api, $name, $service)
    {
        return App::makeWith("api.$api", compact(['name', 'service']))->serve();
    }

    /**
     * @api v1.0
     *
     * API for mobile apps
     *
     * @param $name
     * @return mixed|api.app
     * @throws BindingResolutionException
     */
    public function app($name)
    {
        return App::makeWith('api.app', compact('name'))->serve();
    }

    /**
     * TODO: Migrate to provider strategy and version 2.0
     *
     * API for web process
     *
     * @param $api
     * @param $service
     * @param Request $request
     * @return JsonResponse
     */
    public function web($api, $service, Request $request)
    {
        switch ($api) {
            case 'passengers':
                return $this->passengersService->serve($service, $request);
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
                abort(403);
                break;
        }
    }
}
