<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 27/11/2018
 * Time: 7:35 PM
 */

namespace App\Services\Reports\Routes;

use App\Services\Exports\Routes\RouteExportService;
use App\Services\Reports\Routes\Takings\RouteTakingsService;

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
     * @param RouteTakingsService $takingsService
     */
    public function __construct(DispatchRouteService $dispatchService, RouteExportService $routeExportService, RouteTakingsService $takingsService)
    {
        $this->dispatch = $dispatchService;
        $this->export = $routeExportService;
        $this->takings = $takingsService;
    }
}