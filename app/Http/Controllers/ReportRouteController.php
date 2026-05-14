<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Utils\StrTime;
use App\Models\Routes\Route;
use App\Models\Vehicles\LastLocation;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\Report;
use App\Models\Vehicles\Location;
use App\Models\Vehicles\Vehicle;
use App\Services\Auth\PCWAuthService;
use App\Services\Reports\Routes\RouteService;
use App\Traits\CounterByRecorder;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use function foo\func;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Exports\Routes\RouteReportExportAcomulated;
use DB;

class ReportRouteController extends Controller
{
    /**
     * @var RouteService
     */
    private $routeService;
    /**
     * @var PCWAuthService
     */
    private $authService;

    /**
     * ReportRouteController constructor.
     * @param PCWAuthService $authService
     * @param RouteService $routeService
     */
    public function __construct(PCWAuthService $authService, RouteService $routeService)
    {

        $this->routeService = $routeService;
        $this->authService = $authService;
    }

    /**
     * @return Factory|View
     */
    public function index()
    {
        $accessProperties = $this->authService->getAccessProperties();
        $companies = $accessProperties->companies;
        $vehicles = $accessProperties->vehicles;
        return view('reports.route.route.index', compact(['companies', 'vehicles']));
    }

    /**
     * @return Factory|View
     */
    public function ts()
    {
        $accessProperties = $this->authService->getAccessProperties();
        $companies = $accessProperties->companies;
        $vehicles = $accessProperties->vehicles;
        return view('reports.route.route.ts', compact(['companies', 'vehicles']));
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function show(Request $request)
    {
        $dateTimeRequest = $request->get('date-report');
        $dateTimeEndRequest = $request->get('date-end-report');
        $spreadsheetReport = $request->get('spreadsheet-report');
        $travelIdReport = $request->get('travel-id-report');

        $dateTimeRequestArray = collect(explode(' ', $dateTimeRequest));
        $dateTimeEndRequestArray = collect(explode(' ', $dateTimeEndRequest));

        $company = $this->authService->getCompanyFromRequest($request);
        $dateReport = $dateTimeRequestArray->get(0);
        $withEndDate = $request->get('with-end-date');
        $dateEndReport = $withEndDate ? $dateTimeEndRequestArray->get(0) : null;
        $typeReport = $request->get('type-report');
        $routeReport = $request->get('route-report');
        $vehicleReport = $request->get('vehicle-report');
        $completedTurns = $request->get('completed-turns');
        $activeTurns = $request->get('active-turns');
        $cancelledTurns = $request->get('cancelled-turns');
        $noTakenTurns = $request->get('no-taken-turns');
        $exportFiCS = $request->get('export-FICS');


        $timeReport = $request->get('time-range-report');
        //$timeRange = collect(explode(';', $timeReport));
        //$initialTime = $timeRange->get(0);
        //$finalTime = $timeRange->get(1);

        $initialTime = $dateTimeRequestArray->get(1);
        $finalTime = $dateTimeEndRequestArray->get(1);

        $onlyLastLap = $request->get('last-laps');

        if ($routeReport == 'none') return $this->showReportWithOutRoute($request);

        if ($spreadsheetReport) {
            $dispatchRegistersByVehicles = $this->routeService->dispatch->allBySpreadsheet($spreadsheetReport);
            if($dispatchRegistersByVehicles->count()) $company = $dispatchRegistersByVehicles->first()->first()->vehicle->company;
        } elseif ($travelIdReport) {
            $dispatchRegistersByVehicles = $this->routeService->dispatch->allByTravelId($travelIdReport);
            if($dispatchRegistersByVehicles->count()) $company = $dispatchRegistersByVehicles->first()->first()->vehicle->company;
        } else {
            $dispatchRegistersByVehicles = $this->routeService->dispatch->allByVehicles($company, $dateReport, $dateEndReport, $routeReport, $vehicleReport, $completedTurns, $noTakenTurns, $initialTime, $finalTime, $activeTurns, $cancelledTurns, ['drObservations', 'dispatcherVehicle.route', 'routeTakings', 'photos', 'vehicle.cameras']);
        }


        if ($onlyLastLap) {
            $dispatchRegistersByVehicles = $dispatchRegistersByVehicles->mapWithKeys(function ($drs, $vehicleId) {
                return [$vehicleId => collect([$drs->last()])];
            });
            $typeReport = 'ungroup';
        }

        $reportsByVehicle = collect([]);
        foreach ($dispatchRegistersByVehicles as $vehicleId => $dispatchRegistersByVehicle) {
            $reportsByVehicle->put($vehicleId, CounterByRecorder::reportByVehicle($vehicleId, $dispatchRegistersByVehicle));
        }

        switch ($typeReport) {
            case 'group-vehicles':
                if ($request->get('export')) return $this->routeService->getExporter($company)
                    ->groupedRouteReport($dispatchRegistersByVehicles, $dateReport, null, false, $exportFiCS);

                $view = 'reports.route.route.routeReportByVehicle';
                break;
            default:
                if ($request->get('export')) $this->routeService->getExporter($company)->ungroupedRouteReport($dispatchRegistersByVehicles, $dateReport);
                $view = 'reports.route.route.routeReportByAll';
                break;

        }

        $activeRoutes = $company->activeRoutes;
        $activeVehicles = $company->activeVehicles;

        return view($view, compact([
            'dispatchRegistersByVehicles',
            'reportsByVehicle',
            'company',
            'dateReport',
            'dateEndReport',
            'dateTimeRequest',
            'dateTimeEndRequest',
            'dateEndReport',
            'withEndDate',
            'routeReport',
            'vehicleReport',
            'typeReport',
            'completedTurns',
            'activeTurns',
            'cancelledTurns',
            'timeReport',
            'spreadsheetReport',
            'travelIdReport',
            'activeRoutes',
            'activeVehicles'
        ]));
    }

    public function showReportWithOutRoute(Request $request)
    {
        $company = $this->authService->getCompanyFromRequest($request);
        $dateReport = $request->get('date-report');
//        $thresholdKm = $request->get('threshold-km');
        $vehicleReport = $request->get('vehicle-report');
//        $completedTurns = $request->get('completed-turns');
        $timeRange = collect(explode(';', $request->get('time-range-report')));
        $initialTime = $timeRange->get(0);
        $finalTime = $timeRange->get(1);

        if ($vehicleReport && $vehicleReport != 'all') $vehiclesId = [$vehicleReport];
        else $vehiclesId = $company->activeVehicles->pluck('id');

        $from = Carbon::now();

        $locations = Location::forDate($dateReport)->with(['vehicle', 'dispatchRegister'])
            ->whereBetween('date', ["$dateReport $initialTime:00", "$dateReport $finalTime:59"])
            ->whereIn('vehicle_id', $vehiclesId)
            ->get()
            ->filter(function (Location $l) {
                return !$l->dispatchRegister || !$l->dispatchRegister->isActive();
            });

//        dd(Carbon::now()->diffAsCarbonInterval($from)->forHumans());

        $dispatchRegisters = DispatchRegister::where('date', '=', $dateReport)
            ->whereBetween('departure_time', ["$initialTime:00", "$finalTime:00"])
            ->whereIn('vehicle_id', $locations->pluck('vehicle_id'))
            ->active()
            ->orderBy('departure_time')
            ->get();

        $locations = $locations->whereNotIn('vehicle_id', $dispatchRegisters->pluck('vehicle_id'));

        $from1 = Carbon::now();

        $vehiclesData = $locations->groupBy('vehicle_id')->mapWithKeys(function ($l, $vehicleId) {
            $l = collect($l)->sortBy('date');
            $first = $l->first();
            $last = $l->last();
            $kmInTimeRange = $last->current_mileage - $first->current_mileage;

            return [$vehicleId => (object)compact(['first', 'last', 'kmInTimeRange'])];
        });

//        dd('locas > ' ,Carbon::now()->diffAsCarbonInterval($from1)->forHumans())

        $vehiclesData = $vehiclesData->filter(function ($d) {
            return $d->kmInTimeRange >= 1000;
        });

        $timeRange = (object)[
            'initial' => intval(round(StrTime::toSeg($initialTime) / 5)),
            'final' => intval(round(StrTime::toSeg($finalTime) / 5)),
        ];

        return view('reports.route.route.withOutRoute', compact(['vehiclesData', 'company', 'dateReport', 'timeRange']));
    }

    /**
     * @param DispatchRegister $dispatchRegister
     * @param $locationId
     * @param Request $request
     * @return string
     */
    public function chartView(DispatchRegister $dispatchRegister, $locationId, Request $request)
    {
        return view('reports.route.route.general', compact(['dispatchRegister', 'locationId']));
    }

    /**
     * @param DispatchRegister $dispatchRegister
     * @param Request $request
     * @return JsonResponse
     */
    public function chart(DispatchRegister $dispatchRegister, Request $request)
    {
        if ($dispatchRegister->locations()->count() < 100) { // TODO: While fix bug on chart map view when report loads fast
            sleep(2);
        }
        $centerOnLocation = ($request->get('centerOnLocation')) ? Location::find(($request->get('centerOnLocation'))) : null;
        return response()->json($this->routeService->dispatch->locationsReports($dispatchRegister, $centerOnLocation));
    }

    /**
     * Gets table logs for calculated reports
     *
     * @param DispatchRegister $dispatchRegister
     * @return Factory|View
     */
    public function getReportLog(DispatchRegister $dispatchRegister)
    {
        $reports = Report::where('dispatch_register_id', $dispatchRegister->id)
            ->with('location')
            ->orderBy('date')
            ->get();

        $locationsReports = $this->routeService->dispatch->locationsReports($dispatchRegister);

        return view('reports.route.route.templates._tableReportLog', compact(['reports', 'locationsReports']));
    }

    /**
     * @param Request $request
     * @return Factory|View|string
     * @throws GuzzleException
     */
    public
    function ajax(Request $request)
    {
        switch ($request->get('option')) {
            case 'loadRoutes':
                return (new GeneralController())->loadSelectRoutes($request);

                /*$company = Auth::user()->isAdmin() ? $request->get('company') : Auth::user()->company->id;
                $routes = $company != 'null' ? Route::active()->where('company_id', '=', $company)->orderBy('name', 'asc')->get() : [];
                return view('partials.selects.routes', compact('routes'));*/
                break;
            case 'executeDAR':
                ini_set('MAX_EXECUTION_TIME', 0);
                set_time_limit(0);
                $dispatchRegisterId = $request->get('dispatchRegisterId');

                $client = new Client();
                $response = $client->request('GET', config('gps.server.url') . "/autoDispatcher/processDispatchRegister/$dispatchRegisterId?sync=true", ['timeout' => 0]);

                return $response->getBody();
                break;
            case 'addDR':
                $response = (object)[
                    'success' => true,
                    'message' => __('Dispatch register created successfully'),
                    'id' => null
                ];

                $vehicle = Vehicle::find($request->get('dr-vehicle-id') ?? 0); // TODO: implements in form

                if(!$vehicle) {
                    $response->success = false;
                    $response->message = __('Vehicle not found');
                    return collect($response)->toJson();
                }

                $route = Route::find($request->get('dr-route-id'));

                if(!$route) {
                    $response->success = false;
                    $response->message = __('Route not found');
                    return collect($response)->toJson();
                }

                $date = $request->get('dr-date');
                $departureTime = $request->get('dr-departure-time');
                $arrivalTime = $request->get('dr-arrival-time');

                $spreadSheetNumber = $request->get('dr-sp-number');
                $spreadSheetPassengers = $request->get('dr-sp-passengers');
                $visualPassengers = $request->get('dr-visual-passengers');

                $userId = auth()->user()->id;

                $insert = \DB::select("
                    INSERT INTO registrodespacho 
                        (fecha, hora, h_reg_despachado, h_reg_llegada, tipo_dia, id_ruta, id_despacho, id_empresa, n_vehiculo, n_placa, observaciones, cancelado, registradora_salida, registradora_llegada, n_turno, ignore_trigger, h_llegada_prog, updated_user_id)
                    VALUES 
                        ('$date', '$departureTime', '$departureTime', '$arrivalTime', 'habil', $route->id, $route->dispatch_id, $vehicle->company_id, '$vehicle->number', '$vehicle->plate', 'Terminó', FALSE, 0, 0, 1, TRUE,
                        '$departureTime' :: TIME :: INTERVAL + (SELECT get_route_total_time_from_dispatch_time('$date $departureTime' :: TIMESTAMP, $route->id)) :: INTERVAL,
                         $userId
                        ) 
                    RETURNING id_registro
                ");

                $dispatchRegister = DispatchRegister::find(collect($insert)->first()->id_registro ?? 0);

                if(!$dispatchRegister) {
                    $response->success = false;
                    $response->message = 'Dispatch register did not created in database';
                    return collect($response)->toJson();
                }

                $response->id = $dispatchRegister->id;

                if($spreadSheetNumber) {
                    $drObs = $dispatchRegister->getObservation('spreadsheet_passengers');
                    $drObs->observation = $spreadSheetNumber;
                    $drObs->value = $spreadSheetPassengers;
                    $drObs->user()->associate(auth()->user());
                    $drObs->save();
                }

                if($visualPassengers && $response->success) {
                    $drObs = $dispatchRegister->getObservation('end_recorder');
                    $drObs->observation = $spreadSheetNumber;
                    $drObs->value = $visualPassengers;
                    $drObs->user()->associate(auth()->user());
                    $drObs->save();
                }

                return collect($response)->toJson();
                break;
            default:
                return "Nothing to do";
                break;
        }
    }
    public function exportAcomulated(Request $request)
    {
        // Get parameters from request
        $companyId = $request->get('company-report') ?? 39;
        $dateReport = $request->get('date-report');
        $routeIdsInput = $request->get('route_ids'); // Asumiendo que el parámetro se llama route_ids

        // Validate required fields
        if (empty($dateReport)) {
            return back()->with('error', __('La fecha es obligatoria'));
        }

        // Get route IDs
        $routeIdsArray = [279, 280, 276, 275]; // Default routes
        if (!empty($routeIdsInput) && $routeIdsInput !== 'all' && $routeIdsInput !== 'null') {
            $routeIdsArray = explode(',', $routeIdsInput);
        }

        // Get report data
        $reportData = $this->getRouteDispatchReport($dateReport, $companyId, $routeIdsArray);



        // Instanciar tu clase preparadora de datos
        $exportProcessor = new RouteReportExportAcomulated($reportData);

        // Generate filename with date
        $filename = 'reporte_despachos_' . str_replace('-', '_', $dateReport); // Nombre de archivo sin extensión aún

        // Export to Excel usando sintaxis v2.1
        return Excel::create($filename, function($excel) use ($exportProcessor,$dateReport) {

            $excel->sheet($exportProcessor->getTitle(), function($sheet) use ($exportProcessor, $dateReport) {

                $mainTitle = 'RUTA PALMIRA';
                $sheet->mergeCells('A1:B1');
                $sheet->row(1, [$mainTitle]);
                $sheet->row(1, function($row) {
                    $row->setFontWeight('bold');
                    $row->setFontColor('#000000');
                    $row->setBackground('#4472C4');
                    $row->setAlignment('center');
                });
                $dateExport = $dateReport;
                $sheet->mergeCells('A2:B2');
                $sheet->row(2, ['FECHA: ' . $dateReport]);
                $sheet->row(2, function($row) {
                    $row->setFontWeight('bold');
                    $row->setFontColor('#000000');
                    $row->setBackground('#4472C4');
                    $row->setAlignment('center');
                });

                $sheet->row(3, $exportProcessor->getHeadings());

                $sheet->row(3, function($row) {
                    $row->setFontWeight('bold');
                    $row->setFontColor('#000000');
                    $row->setBackground('#4472C4');
                    $row->setAlignment('center');
                });

                // 3. Añadir los datos
                $dataToExport = $exportProcessor->getExportData();


                // Solo añadir filas si dataToExport no está vacío o si no es la fila placeholder
                // La clase RouteReportExportAcomulated ahora asegura que si no hay datos,
                // getExportData() devuelve [['', '']] para que las cabeceras tengan el ancho correcto.
                // Si solo quieres cabeceras y ningún dato (ni siquiera fila vacía) si no hay resultados:
                if (!empty($dataToExport)) {
                    // ...
                    $rowIndex = 4; // Empezamos en la fila 3, ya que 1 es el título y 2 es la cabecera

                    foreach ($dataToExport as $rowData) {
                        $sheet->row($rowIndex, $rowData);

                        $sheet->row($rowIndex, function($row) {
                            $row->setAlignment('center');
                            // Si querés vertical también:
                            // $row->setValignment('center');
                        });

                        $rowIndex++;
                    }
                } else {
                    dd('NO EXISTEN DATOS'); // <--- O AQUÍ
                }


                // 4. Aplicar bordes a todas las celdas con contenido
                // Es importante hacer esto DESPUÉS de añadir todos los datos
                $highestRow = $sheet->getHighestDataRow(); // Obtiene la última fila con datos
                $highestColumn = $sheet->getHighestDataColumn(); // Obtiene la última columna con datos

                if ($highestRow > 0) { // Solo si hay alguna fila (al menos cabeceras)
                    $sheet->setBorder("A1:{$highestColumn}{$highestRow}", 'thin');
                }

                // 5. Zebra striping para filas de datos (desde la fila 2)
                if ($highestRow > 1) { // Solo si hay más que la cabecera
                    for ($i = 2; $i <= $highestRow; $i++) {
                        if ($i % 2 == 0) { // Filas pares del Excel (segunda fila de datos, cuarta, etc.)
                            $sheet->row($i, function($row) {
                                $row->setBackground('#DDEBF7');
                            });
                        }
                    }
                }

                // 6. ShouldAutoSize (Ajustar ancho de columnas automáticamente)
                $sheet->setAutoSize(true);
                // O para columnas específicas si es necesario:
                // $sheet->setAutoSize(['A', 'B']);
            });

        })->download('xlsx'); // Especifica la extensión aquí
    }

    /**
     * Get the route dispatch report data
     *
     * @param string $dateReport
     * @param int $companyId
     * @param array $routeIds
     * @return array
     */
    private function getRouteDispatchReport($dateReport, $companyId, $routeIds)
    {
        // Asegurarse que los IDs de ruta sean numéricos para evitar inyección SQL
        $sanitizedRouteIds = array_map('intval', $routeIds);
        if (empty($sanitizedRouteIds)) {
            // Si después de sanitizar no quedan IDs, o no se proporcionaron,
            // podrías devolver un array vacío o manejarlo como un error.
            // Para este ejemplo, si está vacío, la consulta fallará o devolverá 0 resultados,
            // lo cual está bien si las cabeceras aún se muestran.
            // Si es un requisito que siempre haya rutas, añade una validación.
            // Por ahora, si está vacío, la cláusula IN será IN() que puede dar error en algunos SQL.
            // Es mejor asegurarse que no esté vacío, o construir la query condicionalmente.
            // Por simplicidad, asumimos que $routeIds siempre tendrá valores válidos.
            // Si $routeIds puede estar vacío y eso es válido, la query debe manejarlo.
            // Una forma simple es no incluir la cláusula AND r.id_ruta IN si $sanitizedRouteIds está vacío,
            // pero eso cambiaría la lógica del reporte.
            // Por ahora, si $sanitizedRouteIds está vacío, la query SQL con IN () fallará.
            // Una solución simple si se permite vacío es `AND (1=0 OR r.id_ruta IN (...))`
            // o simplemente no añadir esa parte del WHERE si no hay routeIds.
            // Por ahora, asumimos que $routeIds siempre tiene elementos.
            if (empty($sanitizedRouteIds)) {
                return []; // Devolver vacío si no hay rutas válidas, para evitar error en IN()
            }
        }

        $results = DB::select("
        SELECT
            r.n_vehiculo,
            COUNT(*) AS cantidad_despachos
        FROM registrodespacho r
            LEFT JOIN dr_observations obs_spreadsheet
                   ON r.id_registro = obs_spreadsheet.dispatch_register_id
                      AND obs_spreadsheet.field = 'spreadsheet_passengers'
            LEFT JOIN dr_observations obs_registradora
                   ON r.id_registro = obs_registradora.dispatch_register_id
                      AND obs_registradora.field = 'registradora_llegada'
            LEFT JOIN routes rt
                   ON r.id_ruta = rt.id
        WHERE
            r.fecha = ?
          AND r.id_empresa = ?
          AND r.cancelado = FALSE
          AND r.observaciones = 'Terminó'
          AND r.id_ruta IN (" . implode(',', $sanitizedRouteIds) . ") /* Usar los IDs sanitizados */
        GROUP BY r.n_vehiculo
        ORDER BY cantidad_despachos ASC
    ", [$dateReport, $companyId]);
        // Format for Excel
        $formattedData = [];
        foreach ($results as $row) {
            $formattedData[] = [
                'Vehículo' => $row->n_vehiculo,
                'Cantidad de Despachos' => $row->cantidad_despachos
                // No necesitas más campos si tu reporte solo tiene estos dos.
                // Si tu query devolviera más campos y los quisieras, añádelos aquí.
            ];
        }

        return $formattedData;
    }

}
