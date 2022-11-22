<?php

namespace App\Services\Reports\Routes;

use App\Models\Company\Company;
use App\Services\Exports\Routes\RouteExportEPService;
use App\Services\Exports\Routes\RouteExportService;
use App\Services\Operation\Routes\Takings\RouteTakingsService;

class RouteService
{
    /**
     * @var DispatchRouteService
     */
    public $dispatch;

    /**
     * @var RouteExportService
     */
    public $export;

    /**
     * @var RouteTakingsService
     */
    public $takings;

    /**
     * RouteService constructor.
     * @param DispatchRouteService $dispatchService
     * @param RouteExportService $routeExportService
     */
    public function __construct(DispatchRouteService $dispatchService, RouteExportService $routeExportService)
    {
        $this->dispatch = $dispatchService;
        $this->export = $routeExportService;
    }

    function getExporter(Company $company)
    {
        if ($company->id === Company::EXPRESO_PALMIRA) return new RouteExportEPService();

        return new RouteExportService();
    }
}