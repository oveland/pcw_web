<?php

namespace App\Http\Controllers;

use App\Services\Auth\PCWAuthService;
use App\Services\Exports\Average\ReportExportService;
use App\Services\Reports\Routes\RouteService;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AveragePassenger extends Controller
{
    /**
     * @var ReportExportService
     */
    private $reportExportService;
    /**
     * @var PCWAuthService
     */
    private $authService;

    /**
     * ReportRouteController constructor.
     * @param PCWAuthService $authService
     * @param RouteService $routeService
     * @param ReportExportService $reportExportService
     */
    public function __construct(PCWAuthService $authService, ReportExportService $reportExportService)
    {

        //$this->routeService = $routeService;
        $this->authService = $authService;
        $this->reportExportService = $reportExportService;
    }

    protected $reportExport;


    /**
     * @return Factory|View
     */

    function index()
    {
        $accessProperties = $this->authService->getAccessProperties();
        $companies = $accessProperties->companies;
        $company = $companies->where('id', 39)->first();
        return view('reports.route.average-passenger.index', compact(['company']));
    }

    function show(Request $request)
    {
        $company = $request->get('company-report');
        $route = $request->get('route-report');
        $timeReport = $request->get('time-report');
        $time = Carbon::createFromFormat('H:i', $timeReport);
        $timeStart = $time->copy()->subMinutes(10)->format('H:i');
        $timeEnd = $time->copy()->addMinutes(10)->format('H:i');
        $day = $request->get('day-of-week') ?? $request->get('special-day');

        if ($day == 9) {
            $startOfWeek = Carbon::now()
                ->subWeeks(1)
                ->startOfWeek();
            for ($i = 0; $i < 5; $i++) {
                $dates[] = $startOfWeek
                    ->copy()
                    ->addDays($i)
                    ->toDateString();
            }
        } else {
            for ($i = 1; $i <= 4; $i++) {
                $dates[] = Carbon::now()
                    ->subWeeks($i)
                    ->startOfWeek()
                    ->addDays($day - 1)
                    ->toDateString();
            }
        }
        /*
                echo "<br>";
                echo("Desde la hora : ".$timeStart);
                echo "<br>";
                echo("Hasta la hora : ".$timeEnd);
                echo "<br>";
                foreach ($dates as $date){
                    echo "<br>";
                    echo $date;
                }
                echo "<br>";*/


        $observations = DB::table('dispatch_registers as dr')
            ->join('dr_observations as dro', 'dr.id', '=', 'dro.dispatch_register_id')
            ->whereIn('dr.date', $dates)
            ->where('dr.route_id', $route)
            ->where('dr.canceled', false)
            ->where('dr.status', 'Terminó')
            ->whereBetween('dr.time', [$timeStart, $timeEnd])
            ->where('dro.field', 'registradora_llegada')
            ->pluck('dro.value');

        $averagePassengers = collect($observations)->avg();
        $averagePassengers = (int)round($averagePassengers);
        return view('reports.route.average-passenger.show', compact(
            'averagePassengers',
            'company',
            'route',
            'timeReport',
            'day',
            'timeStart',
            'timeEnd',
            'dates'
        ));
    }

    public function export(Request $request)
    {
        $dates = $this->calculateDates();
        $data = $this->getAveragePassengersByIntervals($dates);

        $this->reportExportService->export($data);
    }

    private function calculateDates()
    {
        $dates = [];
        $startOfWeek = Carbon::now()->subWeeks(1)->startOfWeek();
        for ($i = 0; $i < 5; $i++) {
            $dates['Dias Hábiles'][] = $startOfWeek->copy()->addDays($i)->toDateString();
        }

        for ($i = 1; $i <= 4; $i++) {
            $dates['Sábados'][] = Carbon::now()
                ->subWeeks($i)
                ->startOfWeek()
                ->addDays(5)
                ->toDateString();
        }

        for ($i = 1; $i <= 5; $i++) {
            $dates['Domingos'][] = Carbon::now()
                ->subWeeks($i)
                ->startOfWeek()
                ->addDays(6) // Domingo es el día 7 (0 = lunes, 6 = domingo)
                ->toDateString();
        }

        for ($i = 1; $i <= 5; $i++) {
            $dates['festivos'][] = Carbon::now()
                ->subWeeks($i)
                ->startOfWeek()
                ->addDays(6) // Festivos tratados como domingos
                ->toDateString();
        }

        return $dates;
    }

    private function getAveragePassengersByIntervals($dates)
    {
        $results = [];

        foreach ($dates as $type => $dateList) {
            foreach ($dateList as $date) {
                $startTime = Carbon::createFromTime(0, 0);
                $endTime = Carbon::createFromTime(23, 30);

                while ($startTime->lte($endTime)) {
                    $intervalStart = $startTime->format('H:i');
                    $intervalEnd = $startTime->copy()->addMinutes(60)->format('H:i');

                    $observations = DB::table('dispatch_registers as dr')
                        ->join('dr_observations as dro', 'dr.id', '=', 'dro.dispatch_register_id')
                        ->where('dr.date', $date)
                        ->where('dr.canceled', false)
                        ->where('dr.status', 'Terminó')
                        ->whereBetween('dr.time', [$intervalStart, $intervalEnd])
                        ->where('dro.field', 'registradora_llegada')
                        ->pluck('dro.value');

                    $average = collect($observations)->avg();

                    // Agregar los datos al grupo correspondiente
                    $results[$type][] = [
                        'fecha' => $date,
                        'intervalo' => "$intervalStart - $intervalEnd",
                        'promedio_pasajeros' => round($average),
                    ];

                    $startTime->addMinutes(60);
                }
            }
        }
        return $results;
    }
}
