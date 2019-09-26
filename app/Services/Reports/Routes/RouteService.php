<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 27/11/2018
 * Time: 7:35 PM
 */

namespace App\Services\Reports\Routes;

use App\Services\Exports\Routes\RouteExportService;

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
     * RouteService constructor.
     * @param DispatchRouteService $dispatchService
     * @param RouteExportService $routeExportService
     */
    public function __construct(DispatchRouteService $dispatchService, RouteExportService $routeExportService)
    {
        $this->dispatch = $dispatchService;
        $this->export = $routeExportService;
    }
}