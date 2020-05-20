<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 19/05/2020
 * Time: 01:48 PM
 */

namespace App\Services\API\Web\DB;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\MigrationController;
use App\Services\API\Web\Contracts\APIWebInterface;

class APIMigrationsService implements APIWebInterface
{
    /**
     * @var Request
     */
    private $request;
    private $service;

    public function __construct($service)
    {
        $this->request = request();
        $this->service = $service ?? $this->request->get('action');
    }

    /**
     * @return JsonResponse
     */
    public function serve(): JsonResponse
    {
        $migrationController = new MigrationController();
        switch ($this->service) {
            case 'companies':
                $migrationController->migrateCompanies($this->request);
                break;
            case 'routes':
                $migrationController->migrateRoutes($this->request);
                break;
            case 'dispatches':
                $migrationController->migrateDispatches($this->request);
                break;
            case 'users':
                $migrationController->migrateUsers($this->request);
                break;
            case 'vehicles':
                $migrationController->migrateVehicles($this->request);
                break;
            case 'control-points':
                $migrationController->migrateControlPoints($this->request);
                break;
            case 'fringes':
                $migrationController->migrateFringes($this->request);
                break;
            case 'control-point-times':
                $migrationController->migrateControlPointTimes($this->request);
                break;
            case 'all':
                $migrationController->migrateCompanies($this->request);
                $migrationController->migrateRoutes($this->request);
                $migrationController->migrateDispatches($this->request);
                $migrationController->migrateUsers($this->request);
                $migrationController->migrateVehicles($this->request);
                $migrationController->migrateControlPoints($this->request);
                $migrationController->migrateFringes($this->request);
                $migrationController->migrateControlPointTimes($this->request);
                break;
            default:
                return response()->json([
                    'error' => true,
                    'message' => 'Invalid action serve'
                ]);
                break;
        }

        return response()->json();
    }
}