<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 12/10/2018
 * Time: 06:16 PM
 */

namespace App\Services\Reports\Routes;

use App\Models\Company\Company;
use App\Models\Routes\ControlPoint;
use App\Models\Routes\ControlPointTime;
use App\Models\Routes\ControlPointTimeReport;
use App\Models\Routes\DispatchRegister;
use App\Http\Controllers\Utils\StrTime;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use Illuminate\Support\Collection;

class ControlPointService
{
    /**
     * @param Company $company
     * @param Route $route
     * @param string $vehicleReport
     * @param $dateReport
     * @param bool $ascendant
     * @return Collection
     */
    function buildReportsByControlPoints(Company $company, Route $route, $vehicleReport = 'all', $dateReport, $ascendant = true)
    {
        $dispatchRegisters = DispatchRegister::active()->whereCompanyAndDateAndRouteIdAndVehicleId($company, $dateReport, $route->id, $vehicleReport)
            ->orderByDesc('departure_time')
            ->get();

        $allReportsByControlPoints = ControlPointTimeReport::whereIn('dispatch_register_id', $dispatchRegisters->pluck('id'))->get();

        if ($ascendant) {
            $allReportsByControlPoints = $allReportsByControlPoints->sortBy(function (ControlPointTimeReport $report) {
                return $report->dispatchRegister->departure_time;
            });
        } else {
            $allReportsByControlPoints = $allReportsByControlPoints->sortByDesc(function (ControlPointTimeReport $report) {
                return $report->dispatchRegister->departure_time;
            });
        }

        $reportsByDispatchRegister = $allReportsByControlPoints->groupBy('dispatch_register_id');

        $reportsByControlPoints = collect([]);
        foreach ($reportsByDispatchRegister as $dispatchRegisterId => $reportByDispatchRegister) {
            $dispatchRegister = DispatchRegister::find($dispatchRegisterId);
            $reportsByControlPoints->push($this->buildControlPointReportsByDispatchRegister($dispatchRegister, $reportByDispatchRegister));
        }

        return $reportsByControlPoints;
    }


    /**
     * @param DispatchRegister $dispatchRegister
     * @param Collection $reportByDispatchRegister
     * @return object
     */
    public function buildControlPointReportsByDispatchRegister(DispatchRegister $dispatchRegister, $reportByDispatchRegister)
    {
        $controlPoints = $dispatchRegister->route->controlPoints;
        $vehicle = $dispatchRegister->vehicle;
        $driver = $dispatchRegister->driver;
        $driverName = $dispatchRegister->driverName();

        $departureTime = $dispatchRegister->departure_time;
        $arrivalTime = $dispatchRegister->arrival_time;
        $arrivalTimeScheduled = $dispatchRegister->arrival_time_scheduled;

        $reportsByControlPoint = collect([]);
        foreach ($controlPoints as $controlPoint) {
            $first = $controlPoint->id == $controlPoints->first()->id;
            $last = $controlPoint->id == $controlPoints->last()->id;

            $controlPointTime = ControlPointTime::where('control_point_id', $controlPoint->id)
                ->where('fringe_id', $dispatchRegister->departure_fringe_id)
                ->get()->first();

            $hasReport = false;
            $scheduledControlPointTime = $controlPointTime ? StrTime::addStrTime($departureTime, $controlPointTime->time_from_dispatch) : '--:--:--';
            $measuredControlPointTime = '--:--:--';
            $difference = '--:--:--';
            $statusColor = '';
            $statusText = '';
            $timeScheduled = '00:00:00';
            $timeMeasured = '00:00:00';

            $controlPointTimeReport = $reportByDispatchRegister
                ->sortBy(function (ControlPointTimeReport $report) {
                    return $report->dispatchRegister->departure_time;
                })
                ->where('control_point_id', $controlPoint->id)
                ->first();

            if ($controlPointTimeReport || $first || ($last && $dispatchRegister->complete() && $dispatchRegister->arrival_time_scheduled)) {
                $statusColor = 'lime';
                $statusText = __('on time');
                $hasReport = true;
                if ($first) { // On first control point take 'At time' status
                    $scheduledControlPointTime = $departureTime;
                    $measuredControlPointTime = $departureTime;
                } else if ($last && $dispatchRegister->complete()) { // On last control point take the dispatch's times
                    $scheduledControlPointTime = $arrivalTimeScheduled;
                    $measuredControlPointTime = $arrivalTime;

                    $timeScheduled = StrTime::subStrTime($arrivalTimeScheduled, $departureTime);
                    $timeMeasured = StrTime::subStrTime($arrivalTime, $departureTime);
                } else { // On middle control points calculates the params report with the interpolation process
                    $controlPointTime = ControlPointTime::where('control_point_id', $controlPoint->id)
                        ->where('fringe_id', $dispatchRegister->departure_fringe_id)
                        ->get()->first();

                    if ($controlPointTime && intval($controlPointTimeReport->distancem)) {
                        $timeScheduled = $controlPointTime->time_from_dispatch;
                        $timeMeasured = StrTime::segToStrTime(
                            StrTime::toSeg($controlPointTimeReport->timem) * intval($controlPoint->distance_from_dispatch) / intval($controlPointTimeReport->distancem)
                        );
                    } else {
                        $timeScheduled = $controlPointTimeReport->timep;
                        $timeMeasured = $controlPointTimeReport->timem;
                    }

                    $scheduledControlPointTime = StrTime::addStrTime($departureTime, $timeScheduled);
                    $measuredControlPointTime = StrTime::addStrTime($departureTime, $timeMeasured);
                }

                if (StrTime::subStrTime($measuredControlPointTime, $scheduledControlPointTime) > '00:01:00') {
                    $isFast = StrTime::timeAGreaterThanTimeB($scheduledControlPointTime, $measuredControlPointTime);
                    $statusColor = $isFast ? 'primary' : 'danger';
                    $statusText = __($isFast ? 'fast' : 'slow');
                }

                $difference = StrTime::difference($measuredControlPointTime, $scheduledControlPointTime);
            }

            $departureFringe = $dispatchRegister->departureFringe;

            $reportsByControlPoint->put($controlPoint->id, (object)[
                'first' => $first,
                'last' => $last,
                'controlPointId' => $controlPoint->id,
                'dispatchRegisterId' => $dispatchRegister->id,
                'fringeName' => $departureFringe ? $departureFringe->name : "--:--",
                'controlPoint' => $controlPoint,
                'hasReport' => $hasReport,
                'statusColor' => $statusColor,
                'statusText' => $statusText,
                'backgroundProfile' => $controlPointTimeReport->background_profile ?? '',
                'scheduledControlPointTime' => $scheduledControlPointTime,
                'measuredControlPointTime' => $measuredControlPointTime,
                'timeScheduled' => $timeScheduled,
                'timeMeasured' => $timeMeasured,
                'timep' => $controlPointTimeReport->timep ?? '--:--:--',
                'timem' => $controlPointTimeReport->timem ?? '--:--:--',
                'difference' => $difference
            ]);
        }

        return (object)[
            'dispatchRegister' => $dispatchRegister,
            'vehicle' => $vehicle,
            'driver' => $driver,
            'driverName' => $driverName,
            'reportsByControlPoint' => $reportsByControlPoint,
        ];
    }

    /**
     * Gets control point time with delay criterion
     *
     * @param DispatchRegister $dispatchRegister
     * @return Collection
     */
    function reportWithDelay(DispatchRegister $dispatchRegister)
    {
        $controlPointReportWithDelay = collect([]);
        $route = $dispatchRegister->route;

        $configParamsRoutesControlPointsWithDelay = collect(config('report.routes.controlPoints.withDelay'))->where('routeId', $route->id)->first();

        if ($configParamsRoutesControlPointsWithDelay) {
            $controlPointsToDetect = ((object)$configParamsRoutesControlPointsWithDelay)->controlPoints;

            $controlPointReports = $this->buildControlPointReportsByDispatchRegister($dispatchRegister, $dispatchRegister->controlPointTimeReports);

            foreach ($controlPointsToDetect as $controlPointToDetect) {
                $controlPointToDetect = (object)$controlPointToDetect;
                $controlPoint = ControlPoint::find($controlPointToDetect->id);
                if ($controlPoint) {
                    $controlPointReport = $controlPointReports->reportsByControlPoint
                        ->where('controlPointId', $controlPoint->id)
                        ->first();

                    if ($controlPointReport) {
                        if ($controlPointReport->timeMeasured > $controlPointToDetect->maxTime) {

                            $controlPointReportWithDelay->push((object)[
                                'controlPointName' => $controlPoint->name,
                                'report' => $controlPointReport,
                                'maxTime' => $controlPointToDetect->maxTime,
                            ]);
                        }
                    }
                }
            }
        }

        return $controlPointReportWithDelay;
    }
}