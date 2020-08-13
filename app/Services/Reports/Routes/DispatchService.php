<?php

namespace App\Services\Reports\Routes;

use App\Exports\Routes\TakingsExport;
use App\Http\Controllers\Utils\StrTime;
use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use Illuminate\Support\Collection;

class DispatchService
{
    /**
     * @var Company
     */
    public $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @param $initialDate
     * @param null $finalDate
     * @param null $route
     * @param null $vehicle
     * @param string $type
     * @return DispatchRegister[] | Collection
     */
    function getTurns($initialDate, $finalDate = null, $route = null, $vehicle = null, $type = 'completed')
    {
        $dr = DispatchRegister::whereCompanyAndDateRangeAndRouteIdAndVehicleId($this->company, $initialDate, $finalDate, $route, $vehicle)->type($type)
            ->get();

        return $dr->map(function (DispatchRegister $dr) {
            return $dr->getAPIFields();
        })->sortBy(function ($dr) {
            return "$dr->date.$dr->id";
        })->values();
    }

    /**
     * @param $initialDate
     * @param null $finalDate
     * @param null $route
     * @param null $vehicle
     * @param string $type
     * @return object
     */
    public function getTakingsReport($initialDate, $finalDate = null, $route = null, $vehicle = null, $type = 'completed')
    {
        $dispatchRegisters = $this->getTurns($initialDate, $finalDate, $route, $vehicle, $type);

        $totalObservations = "";

        foreach ($dispatchRegisters as $d) {
            $observations = $d->takings->observations;
            if ($observations) $totalObservations .= ($finalDate ? "\n$d->date " : ''). __('Round trip') . " $d->roundTrip: $observations. ";
        }

        $totals = [
            'passengers' => $dispatchRegisters->sum(function ($d) {
                return $d->passengers->recorders->count;
            }),
            'totalProduction' => $dispatchRegisters->sum(function ($d) {
                return $d->takings->totalProduction;
            }),
            'control' => $dispatchRegisters->sum(function ($d) {
                return $d->takings->control;
            }),
            'fuel' => $dispatchRegisters->sum(function ($d) {
                return $d->takings->fuel;
            }),
            'fuelGallons' => $dispatchRegisters->sum(function ($d) {
                return $d->takings->fuelGallons;
            }),
            'bonus' => $dispatchRegisters->sum(function ($d) {
                return $d->takings->bonus;
            }),
            'others' => $dispatchRegisters->sum(function ($d) {
                return $d->takings->others;
            }),
            'netProduction' => $dispatchRegisters->sum(function ($d) {
                return $d->takings->netProduction;
            }),
            'routeTime' => StrTime::segToStrTime($dispatchRegisters->sum(function ($d) {
                return StrTime::toSeg($d->routeTime);
            })),
            'hasInvalidCounts' => $dispatchRegisters->filter(function ($d) {
                return $d->passengers->recorders->count < 0;
            })->count(),
            'observations' => $totalObservations
        ];

        $averages = [
            'passengers' => intval($dispatchRegisters->average(function ($d) {
                return $d->passengers->recorders->count;
            })),
            'totalProduction' => $dispatchRegisters->average(function ($d) {
                return $d->takings->totalProduction;
            }),
            'control' => $dispatchRegisters->average(function ($d) {
                return $d->takings->control;
            }),
            'fuel' => $dispatchRegisters->average(function ($d) {
                return $d->takings->fuel;
            }),
            'fuelGallons' => $dispatchRegisters->average(function ($d) {
                return $d->takings->fuelGallons;
            }),
            'bonus' => $dispatchRegisters->average(function ($d) {
                return $d->takings->bonus;
            }),
            'others' => $dispatchRegisters->average(function ($d) {
                return $d->takings->others;
            }),
            'netProduction' => $dispatchRegisters->average(function ($d) {
                return $d->takings->netProduction;
            }),
            'routeTime' => StrTime::segToStrTime($dispatchRegisters->average(function ($d) {
                return StrTime::toSeg($d->routeTime);
            })),
        ];

        return (object)[
            'report' => $dispatchRegisters,
            'totals' => $totals,
            'averages' => $averages,
        ];
    }

    /**
     * Export and store report to excel format
     *
     * @param $data
     * @param bool $download
     * @return string
     */
    function exportTakingsReport($data, $download = true)
    {
        $file = new TakingsExport($data);

        if ($download) return $file->download();

        $path = "exports/routes/takings/$file->fileName";
        $file->store($path);

        return $path;
    }
}