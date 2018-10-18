<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 12/10/2018
 * Time: 06:16 PM
 */

namespace App\Services\pcwserviciosgps\reports\routes;

use App\ControlPoint;
use App\ControlPointTimeReport;
use App\DispatchRegister;
use App\Route;

class ControlPointService
{
    /**
     * Gets all control point time report of a route and date
     *
     * @param Route $route
     * @param $dateReport
     * @return ControlPointTimeReport[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    function allControlPointTimeReport(Route $route, $dateReport)
    {
        $dispatchRegisters = DispatchRegister::active()
            ->where('date', '=', $dateReport)
            ->where('route_id', '=', $route->id)
            ->orderBy('departure_time')
            ->get();

        return ControlPointTimeReport::whereIn('dispatch_register_id', $dispatchRegisters->pluck('id'))
            ->orderBy('dispatch_register_id')
            ->get();
    }

    /**
     * Gets control point time report of a dispatch register
     *
     * @param DispatchRegister $dispatchRegister
     * @return ControlPointTimeReport[]|\Illuminate\Support\Collection
     */
    function controlPointReportsByDispatchRegister(DispatchRegister $dispatchRegister)
    {
        return ControlPointTimeReport::where('dispatch_register_id', $dispatchRegister->id)
            ->orderBy('date_created')
            ->get();
    }

    /**
     * Gets control point time with delay criterion
     *
     * @param DispatchRegister $dispatchRegister
     * @return \Illuminate\Support\Collection
     */
    function controlPointReportWithDelay(DispatchRegister $dispatchRegister)
    {
        $controlPointReportWithDelay = collect([]);
        $route = $dispatchRegister->route;

        $configParamsRoute = collect(config('report.consolidated.controlPoints.withDelay'))->where('routeId', $route->id)->first();

        $controlPointReports = $this->controlPointReportsByDispatchRegister($dispatchRegister);

        if ($configParamsRoute) {
            $controlPointsIDToDetect = ((object)$configParamsRoute)->controlPoints;

            foreach ($controlPointsIDToDetect as $controlPointParam) {
                $controlPointParam = (object)$controlPointParam;
                $controlPoint = ControlPoint::find($controlPointParam->id);
                $controlPointReport = $controlPointReports->where('control_point_id', $controlPointParam->id)->first();
                if ($controlPoint && $controlPointReport) {
                    if ($controlPointReport->timem > $controlPointParam->maxTime) {
                        $controlPointReportWithDelay->push((object)[
                            'controlPointName' => $controlPoint->name,
                            'timeReport' => $controlPointReport->timem,
                            'maxTime' => $controlPointParam->maxTime,
                        ]);
                    }
                }
            }
        }

        return $controlPointReportWithDelay;
    }
}