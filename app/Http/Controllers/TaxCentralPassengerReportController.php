<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Utils\StrTime;
use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use App\Models\Passengers\HistorySeat;
use App\Http\Controllers\Utils\Geolocation;
use App\Models\Routes\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class TaxCentralPassengerReportController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name')->get();
        }
        return view('reports.passengers.taxcentral.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $dateReport = $request->get('date-report');
        $routeId = $request->get('route-report');
        $company = Auth::user()->isAdmin() ? Company::find($request->get('company-report')) : Auth::user()->company;
        $vehiclesForCompany = $company->activeVehicles->pluck('plate');

        $dispatchRegister = null;
        $location_dispatch = null;

        if ($routeId != 'all') {
            $roundTripDispatchRegisters = DispatchRegister::where('date', '=', $dateReport)
                ->where('route_id', '=', $routeId)//->where('status','=',DispatchRegister::COMPLETE)
                ->orderBy('round_trip')->get()->groupBy('round_trip');

            return view('reports.passengers.taxcentral.passengersReportByRoute', compact('roundTripDispatchRegisters'));
        }
        //$historySeats = $historySeats->whereBetween('active_time',[$dateReport.' '.$dispatchRegister->departure_time,$dateReport.' '.$dispatchRegister->arrival_time_scheduled]);

        $historySeats = HistorySeat::whereIn('plate', $vehiclesForCompany)
            ->where('date', '=', $dateReport)
            ->get()->sortBy('active_time');

        if ($request->get('export')) return $this->export($historySeats, $company, $dateReport, null);
        return view('reports.passengers.taxcentral.passengersReportByAll', compact('historySeats'));
    }

    public function showByDispatch(DispatchRegister $dispatchRegister, Request $request)
    {
        $route = $dispatchRegister->route;
        $routeCoordinates = Geolocation::getRouteCoordinates($route->url);

        $dispatchArrivaltime = (DispatchRegister::COMPLETE == $dispatchRegister->status) ? $dispatchRegister->arrival_time : $dispatchRegister->arrival_time_scheduled;
        $dispatchArrivaltime = ($dispatchArrivaltime > "23:59:59") ? "23:59:59" : $dispatchArrivaltime;

        $initialTimeRange = StrTime::subStrTime($dispatchRegister->departure_time, '00:30:00');
        $finalTimeRange = StrTime::addStrTime(($dispatchRegister->canceled ? $dispatchRegister->time_canceled : $dispatchArrivaltime), '00:30:00');

        $historySeats = HistorySeat::where('plate', $dispatchRegister->vehicle->plate)
            ->where('date', '=', $dispatchRegister->date)
            ->whereBetween('time',[$initialTimeRange,$finalTimeRange])
            ->get()->sortBy('active_time');

        $routeDistance = $dispatchRegister->route->distance * 1000;

        foreach ($historySeats as $historySeat) {
            if ($historySeat->complete == 1) {
                //$busyDistance = $this->getBusyKm($historySeat, $routeCoordinates);                
                $historySeat->active_km = $historySeat->active_km < $dispatchRegister->start_odometer ? 0 : ($historySeat->active_km - $dispatchRegister->start_odometer);

                $historySeat->inactive_km = $historySeat->inactive_km - $dispatchRegister->start_odometer;
                $historySeat->inactive_km = $historySeat->inactive_km < $routeDistance ? $historySeat->inactive_km : $routeDistance;

                $historySeat->busy_km = $historySeat->inactive_km - $historySeat->active_km;
            }
        }
        
        if ($request->get('export')) return $this->export($historySeats, $dispatchRegister->route->company, $dispatchRegister->date, $dispatchRegister);

        return view('reports.passengers.taxcentral.passengersReport', compact(['historySeats', 'dispatchRegister', 'dispatchArrivaltime']));
    }

    /**
     * Calculate distance for active seats
     *
     * @param $historySeat
     * @param $route_coordinates
     * @return object
     */
    public function getBusyKm($historySeat, $route_coordinates)
    {
        $found_active_seat_location = false;
        $found_inactive_seat_location = false;
        $active_km = 0;
        $inactive_km = 0;

        foreach ($route_coordinates as $index => $route_coordinate) {
            $route_latitude = $route_coordinate->latitude;
            $route_longitude = $route_coordinate->longitude;

            if ($index > 0) {
                $prev_route_latitude = $route_coordinates[$index - 1]->latitude;
                $prev_route_longitude = $route_coordinates[$index - 1]->longitude;
                $prev_distance = Geolocation::getDistance($route_latitude, $route_longitude, $prev_route_latitude, $prev_route_longitude);

                /* Process active seat locations */
                if (!$found_active_seat_location) {
                    $radius_distance_active_seat_location = Geolocation::getDistance($historySeat->active_latitude, $historySeat->active_longitude, $route_latitude, $route_longitude);
                    if ($radius_distance_active_seat_location <= config('road.seat_distance_threshold')) {
                        $found_active_seat_location = true;
                    } else if ($radius_distance_active_seat_location < config('road.route_sampling_radius')) {
                        $a = (double)$radius_distance_active_seat_location;
                        $b = (double)Geolocation::getDistance($historySeat->active_latitude, $historySeat->active_longitude, $prev_route_latitude, $prev_route_longitude);
                        $c = (double)$prev_distance;
                        $angle = Geolocation::getAngleC($a, $b, $c);
                        $thresholdAngle = Geolocation::getThresholdAngleC(config('road.seat_distance_threshold'), $a, $b);
                        if ($angle >= $thresholdAngle) {
                            $found_active_seat_location = true;
                        }
                    }
                    $active_km += $prev_distance;
                }

                /* Process inactive seat locations */
                if (!$found_inactive_seat_location) {
                    $radius_distance_inactive_seat_location = Geolocation::getDistance($historySeat->inactive_latitude, $historySeat->inactive_longitude, $route_latitude, $route_longitude);
                    if ($radius_distance_inactive_seat_location <= config('road.seat_distance_threshold')) {
                        $found_inactive_seat_location = true;
                    } else if ($radius_distance_inactive_seat_location < config('road.route_sampling_radius')) {
                        $a = (double)$radius_distance_inactive_seat_location;
                        $b = (double)Geolocation::getDistance($historySeat->inactive_latitude, $historySeat->inactive_longitude, $prev_route_latitude, $prev_route_longitude);
                        $c = (double)$prev_distance;
                        $angle = Geolocation::getAngleC($a, $b, $c);
                        $thresholdAngle = Geolocation::getThresholdAngleC(config('road.seat_distance_threshold'), $a, $b);

                        if ($angle >= $thresholdAngle) {
                            $found_inactive_seat_location = true;
                        }
                    }
                    $inactive_km += $prev_distance;

                    if( $active_km < $inactive_km ){
                        $found_inactive_seat_location = false;
                    }
                }
            }
        }

        return (object)[
            'active_km' => $active_km,
            'inactive_km' => $inactive_km,
            'busy_km' => $inactive_km - $active_km,
        ];
    }

    public function export($historySeats, $company, $dateReport, $dispatchRegister)
    {
        $data = [];
        $totalKm = 0;
        $number = 1;
        foreach ($historySeats as $historySeat) {
            $km = $historySeat->busy_km / 1000;
            if ($historySeat->inactive_time) $totalKm += $km;
            $data[] = [
                'N°' => $number++,
                __('Vehicle') => $historySeat->plate,
                __('Seat') => $historySeat->seat,
                __('Event active time') => $historySeat->active_time ? date('H:i:s', strtotime(explode(" ", $historySeat->active_time)[1])) : __('Still busy'),
                __('Event inactive time') => $historySeat->inactive_time ? date('H:i:s', strtotime(explode(" ", $historySeat->inactive_time)[1])) : __('Still busy'),
                __('Active time') => $historySeat->inactive_time ? date('H:i:s', strtotime($historySeat->busy_time)) : __('Still busy'),
                __('Active kilometers') => $historySeat->inactive_time ? $km : __('Still busy'),
            ];
        }

        $dataExport = (object)[
            'fileName' => __('Passengers_Report_') . str_replace(' ', '_', $company->name) . '.' . str_replace('-', '', $dateReport),
            'header' => [__('Passengers Report') . ' ' . $company->name . '. ' . ($dispatchRegister ? $dispatchRegister->route->name : '') . '. ' . $dateReport],
            'data' => $data,
            'totalKm' => [__('Total Km: ') . ' ' . number_format($totalKm, 2, ',', '.')],
            'routeTotalKm' => [__('Route distance') . ' Km: ' . number_format(Route::find(158)->distance, 2, ',', '.')]
        ];

        Excel::create($dataExport->fileName, function ($excel) use ($dataExport) {
            /* INFO DOCUMENT */
            $excel->setTitle(__('Passengers Report'));
            $excel->setCreator(__('PCW Ditech Integradores Tecnológicos'))->setCompany(__('PCW Ditech Integradores Tecnológicos'));
            $excel->setDescription(__('Report travel time and travel distance for vehicle seats'));

            /* FIRST SHEET */
            $excel->sheet(__('PCW Report'), function ($sheet) use ($dataExport) {
                $totalRowsHeader = 4;
                $totalRows = count($dataExport->data) + $totalRowsHeader;

                $sheet->fromArray($dataExport->data);
                $sheet->prependRow($dataExport->routeTotalKm);
                $sheet->prependRow($dataExport->totalKm);
                $sheet->prependRow($dataExport->header);

                /* GENEREAL STYLE */
                $sheet->setOrientation('landscape');
                $sheet->setFontFamily('Segoe UI Light');
                $sheet->setBorder('A1:G' . $totalRows, 'thin');
                $sheet->cells('A1:G' . $totalRows, function ($cells) {
                    $cells->setFontFamily('Segoe UI Light');
                });

                /* COLUMNS FORMAT */
                $sheet->setColumnFormat(array(
                    'D' => 'h:mm:ss',
                    'E' => 'h:mm:ss',
                    'F' => 'h:mm:ss',
                    'G' => '#,##0.00'
                ));
                $sheet->setAutoFilter("A$totalRowsHeader:G" . ($totalRows));

                /*  HEADER */
                $sheet->setHeight(1, 50);
                $sheet->mergeCells('A1:G1');
                $sheet->cells('A1:G1', function ($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('center');
                    $cells->setBackground('#0e6d62');
                    $cells->setFontColor('#eeeeee');
                    $cells->setFont(array(
                        'family' => 'Segoe UI Light',
                        'size' => '14',
                        'bold' => true
                    ));
                });

                /* HEADER TOTAL */
                $sheet->setHeight(2, 25);
                $sheet->mergeCells('A2:G2');
                $sheet->cells('A2:G2', function ($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('right');
                    $cells->setBackground('#0d4841');
                    $cells->setFontColor('#eeeeee');
                    $cells->setFont(array(
                        'family' => 'Segoe UI Light',
                        'size' => '12',
                        'bold' => true
                    ));
                });

                /* HEADER TOTAL ROUTE KM */
                $sheet->setHeight(2, 25);
                $sheet->mergeCells('A3:G3');
                $sheet->cells('A3:G3', function ($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('right');
                    $cells->setBackground('#0d4841');
                    $cells->setFontColor('#eeeeee');
                    $cells->setFont(array(
                        'family' => 'Segoe UI Light',
                        'size' => '12',
                        'bold' => true
                    ));
                });

                /* HEADER COLUMNS */
                $sheet->setHeight(3, 40);
                $sheet->cells("A$totalRowsHeader:G$totalRowsHeader", function ($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('center');
                    $cells->setBackground('#0d4841');
                    $cells->setFontColor('#eeeeee');
                    $cells->setFont(array(
                        'family' => 'Segoe UI Light',
                        'size' => '12',
                        'bold' => true
                    ));
                });
            });
        })->export('xlsx');
    }

    public function showHistorySeat(HistorySeat $historySeat)
    {
        return $historySeat;
    }
}
