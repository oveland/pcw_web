<?php

namespace App\Services\Reports\Routes;

use App\Exports\Routes\TakingsExport;
use App\Exports\Routes\TakingsGroupedExport;
use App\Exports\Routes\TakingsTotalsExport;
use App\Http\Controllers\Utils\StrTime;
use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use Carbon\Carbon;
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
     * @param string $typeTurns
     * @return DispatchRegister[] | Collection
     */
    function getTurns($initialDate, $finalDate = null, $route = null, $vehicle = null, $typeTurns = 'completed')
    {
        $dr = DispatchRegister::whereCompanyAndDateRangeAndRouteIdAndVehicleId($this->company, $initialDate, $finalDate, $route, $vehicle)->type($typeTurns)
            ->with('route')
            ->with('vehicle')
            ->with('routeTakings')
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
     * @param bool $onlyTotals
     * @return object | array
     */
    public function getTakingsReport($initialDate, $finalDate = null, $route = null, $vehicle = null, $type = 'detailed', $onlyTotals = false)
    {
        switch ($type) {
            case 'totals':
            case 'grouped':
                $turns = $this->getTurns($initialDate, $finalDate, $route, $vehicle, 'takings');

                $turns = $turns->sortBy(function ($dr) {
                    return $dr->vehicle->number . "$dr->id";
                })->values();

                if ($turns->isNotEmpty()) {
                    $turnsByVehicles = $turns->groupBy('vehicle_id');
                    $report = collect([]);
                    foreach ($turnsByVehicles as $vehicleId => $turnsByVehicle) {
                        $reportByDate = collect([]);
                        $turnsByDates = $turnsByVehicle->groupBy('date');

                        foreach ($turnsByDates as $date => $turnsByDate) {
                            $reportByDate->put($date, $this->getData($turnsByDate, $onlyTotals));
                        }
                        $report->put($vehicleId, $reportByDate->toArray());
                    }

                    return $report->toArray();
                }
                break;
            default:
                $turns = $this->getTurns($initialDate, $finalDate, $route, $vehicle, 'takings');

                if ($turns->isNotEmpty()) {
                    return $this->getData($turns, $onlyTotals);
                }
                break;
        }

        return null;
    }

    /**
     * @param Collection | array $dispatchRegisters
     * @param bool $onlyTotals
     * @return object
     */
    private function getData($dispatchRegisters, $onlyTotals = false)
    {
        return (object)[
            'options' => [
                'showRecorders' => $this->company->hasRecorderCounter(),
                'showSensor' => $this->company->hasSeatSensorCounter(),
            ],
            'report' => $onlyTotals ? [] : $dispatchRegisters,
            'totals' => $this->getTotals($dispatchRegisters),
            'averages' => $onlyTotals ? [] : $this->getAverages($dispatchRegisters),
        ];
    }

    /**
     * @param Collection $dispatchRegisters
     * @return array
     */
    private function getTotals($dispatchRegisters)
    {
        $totalObservations = "";
        $withRoundTrip = $dispatchRegisters->first()->date != $dispatchRegisters->last()->date;

        foreach ($dispatchRegisters as $d) {
            $observations = $d->takings->observations;
            if ($observations) $totalObservations .= ($withRoundTrip ? "\n$d->date " : '') . __('Round trip') . " $d->roundTrip: $observations. ";
        }

        return [
            'passengers' => (object)[
                'recorders' => (object)[
                    'count' => $dispatchRegisters->sum(function ($d) {
                        return $d->passengers->recorders->count;
                    })
                ],
                'sensor' => [
                    'count' => $dispatchRegisters->sum(function ($d) {
                        return $d->passengers->sensor->count;
                    }),
                    'tariff' => (object)[
                        'a' => (object)[
                            'totalCharge' => $dispatchRegisters->sum(function ($d) {
                                return $d->passengers->sensor->tariff->a->totalCharge;
                            }),
                            'totalCounted' => $dispatchRegisters->sum(function ($d) {
                                return $d->passengers->sensor->tariff->a->totalCounted;
                            })
                        ],
                        'b' => (object)[
                            'totalCharge' => $dispatchRegisters->sum(function ($d) {
                                return $d->passengers->sensor->tariff->b->totalCharge;
                            }),
                            'totalCounted' => $dispatchRegisters->sum(function ($d) {
                                return $d->passengers->sensor->tariff->b->totalCounted;
                            })
                        ],
                    ]
                ]
            ],
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
            'advance' => $dispatchRegisters->sum(function ($d) {
                return $d->takings->advance;
            }),
            'balance' => $dispatchRegisters->sum(function ($d) {
                return $d->takings->balance;
            }),
            'passengersAdvance' => $dispatchRegisters->sum(function ($d) {
                return $d->takings->passengersAdvance;
            }),
            'passengersBalance' => $dispatchRegisters->sum(function ($d) {
                return $d->takings->passengersBalance;
            }),
            'routeTime' => StrTime::segToStrTime($dispatchRegisters->sum(function ($d) {
                return StrTime::toSeg($d->routeTime);
            })),
            'hasInvalidCounts' => $dispatchRegisters->filter(function ($d) {
                return $d->passengers->recorders->count < 0;
            })->count(),
            'observations' => $totalObservations
        ];
    }

    /**
     * @param Collection $dispatchRegisters
     * @return array
     */
    private function getAverages($dispatchRegisters)
    {
        return [
            'passengers' => (object)[
                'recorders' => (object)[
                    'count' => intval($dispatchRegisters->average(function ($d) {
                        return $d->passengers->recorders->count;
                    }))
                ],
                'sensor' => (object)[
                    'count' => intval($dispatchRegisters->average(function ($d) {
                        return $d->passengers->sensor->count;
                    })),
                    'tariff' => (object)[
                        'a' => (object)[
                            'totalCharge' => $dispatchRegisters->average(function ($d) {
                                return $d->passengers->sensor->tariff->a->totalCharge;
                            }),
                            'totalCounted' => intval($dispatchRegisters->average(function ($d) {
                                return $d->passengers->sensor->tariff->a->totalCounted;
                            }))
                        ],
                        'b' => (object)[
                            'totalCharge' => $dispatchRegisters->average(function ($d) {
                                return $d->passengers->sensor->tariff->b->totalCharge;
                            }),
                            'totalCounted' => intval($dispatchRegisters->average(function ($d) {
                                return $d->passengers->sensor->tariff->b->totalCounted;
                            }))
                        ],
                    ]
                ]
            ],
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
            'advance' => $dispatchRegisters->average(function ($d) {
                return $d->takings->advance;
            }),
            'passengersAdvance' => $dispatchRegisters->average(function ($d) {
                return $d->takings->passengersAdvance;
            }),
            'passengersBalance' => $dispatchRegisters->average(function ($d) {
                return $d->takings->passengersBalance;
            }),
            'balance' => $dispatchRegisters->average(function ($d) {
                return $d->takings->balance;
            }),
            'routeTime' => StrTime::segToStrTime($dispatchRegisters->average(function ($d) {
                return StrTime::toSeg($d->routeTime);
            })),
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
        $params = $data->params;

        switch ($params->type) {
            case 'totals':
                $file = new TakingsTotalsExport($data);
                if ($download) return $file->download();
                break;
            case 'grouped':
                $file = new TakingsGroupedExport($data);
                if ($download) return $file->download();
                break;
            default:
                $file = new TakingsExport($data);
                if ($download) return $file->download();
                break;
        }

        $path = "exports/routes/takings/$file->fileName";
        $file->store($path);

        return $path;
    }
}