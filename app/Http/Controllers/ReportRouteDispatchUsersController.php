<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\Route;
use App\Services\Exports\PCWExporterService;
use App\Traits\CounterByRecorder;
use App\Models\Users\User;
use Auth;
use Illuminate\Http\Request;

class ReportRouteDispatchUsersController extends Controller
{
    /**
     * @var GeneralController
     */
    private $generalController;

    public function __construct(GeneralController $generalController)
    {
        $this->generalController = $generalController;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name', 'asc')->get();
        }
        return view('reports.route.dispatch-users.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $company = $this->generalController->getCompany($request);
        $dateReport = $request->get('date-report');

        $dispatchUsersReport = $this->buildDispatchUsersReport($company, $dateReport);
        //dd($dispatchUsersReport);

        if ($request->get('export')) $this->export($dispatchUsersReport);

        return view('reports.route.dispatch-users.show', compact(['dispatchUsersReport']));
    }

    /**
     * @param Company|null $company
     * @param $dateReport
     * @return object
     */
    public function buildDispatchUsersReport(Company $company = null, $dateReport)
    {
        $dispatchRegistersByUsers = DispatchRegister::active()
            ->whereIn('vehicle_id', $company->vehicles->pluck('id'))
            ->where('date', $dateReport)
            ->orderBy('id')
            ->get()
            ->groupBy('user_id');

        $reports = collect([]);
        foreach ($dispatchRegistersByUsers as $userId => $dispatchRegistersByUser) {
            $user = User::find($userId);
            $dispatchRegistersByVehicles = $dispatchRegistersByUser->groupBy('vehicle_id');
            $counterByRecorderByVehicles = collect([]);

            foreach ($dispatchRegistersByVehicles as $vehicleId => $dispatchRegisters) {
                $counterByRecorderByVehicles->put(
                    $vehicleId,
                    (object)[
                        'vehicleId' => $vehicleId,
                        'counter' => CounterByRecorder::report($dispatchRegisters, true)
                    ]);
            }

            $reports->put(
                $userId,
                (object)[
                    'user' => $user,
                    'totalDispatches' => $dispatchRegistersByUser->count(),
                    'dispatchRegistersByVehicles' => $dispatchRegistersByVehicles,
                    'counterByRecorderByVehicles' => $counterByRecorderByVehicles,
                    'totalVehiclesDispatched' => $dispatchRegistersByVehicles->count(),
                    'firstDispatchRegister' => $dispatchRegistersByUser->first(),
                    'lastDispatchRegister' => $dispatchRegistersByUser->last()

                ]
            );
        }

        $roundTripsReport = (object)[
            'company' => $company,
            'dateReport' => $dateReport,
            'reports' => $reports->sortBy('totalRoundTrips'),
        ];

        return $roundTripsReport;
    }

    /**
     * @param $roundTripsReport
     */
    public function export($roundTripsReport)
    {
        $dateReport = $roundTripsReport->dateReport;
        $reports = $roundTripsReport->reports;

        $dataExcel = array();
        foreach ($reports as $report) {
            $vehicle = $report->vehicle;
            $dataExcel[] = [
                __('N°') => count($dataExcel) + 1,                                      # A CELL
                __('Vehicle') => intval($vehicle->number),                              # B CELL
                __('Plate') => $vehicle->plate,                                         # C CELL
                __('Round trips') => intval($report->totalRoundTrips),                  # D CELL
            ];;
        }

        PCWExporterService::excel([
            'fileName' => __('Round trip report') . " $dateReport",
            'title' => __('Round trip report'),
            'subTitle' => $dateReport,
            'data' => $dataExcel,
            'type' => 'roundTripsVehicleReport'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public
    function ajax(Request $request)
    {
        switch ($request->get('option')) {
            case 'loadRoutes':
                $company = Auth::user()->isAdmin() ? $request->get('company') : Auth::user()->company->id;
                $routes = $company != 'null' ? Route::active()->where('company_id', '=', $company)->orderBy('name', 'asc')->get() : [];
                return view('reports.route.off-road.routeSelect', compact('routes'));
                break;
            default:
                return "Nothing to do";
                break;
        }
    }
}
