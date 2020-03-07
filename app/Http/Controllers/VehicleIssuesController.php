<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Users\User;
use App\Models\Vehicles\CurrentVehicleIssue;
use App\Models\Vehicles\Vehicle;
use App\Models\Vehicles\VehicleIssue;
use App\Models\Vehicles\VehicleIssueType;
use App\Services\Auth\PCWAuthService;
use App\Services\Operation\Vehicles\NoveltyService;
use Auth;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class VehicleIssuesController extends Controller
{
    /**
     * @var PCWAuthService
     */
    private $auth;
    /**
     * @var NoveltyService
     */
    private $novelty;

    /**
     * VehicleIssuesController constructor.
     * @param PCWAuthService $auth
     * @param NoveltyService $novelty
     */
    public function __construct(PCWAuthService $auth, NoveltyService $novelty)
    {
        $this->auth = $auth;
        $this->novelty = $novelty;
    }

    /**
     * @return Factory|View
     */
    public function index()
    {
        $access = $this->auth->access();

        $companies = $access->companies;
        $vehicles = $access->company->vehicles;

        return view('operation.vehicles.issues.index', compact(['companies', 'vehicles']));
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function show(Request $request)
    {
        $dateReport = $request->get('date-report');
        $vehicleReport = $request->get('vehicle-report');
        $sortDescending = $request->get('sort-desc');
        $company = $this->auth->getCompanyFromRequest($request);

        $report = $this->buildReport($company, $vehicleReport, $dateReport, $sortDescending);

        if ($request->get('export')) $this->novelty->export($report);

        return view('operation.vehicles.issues.show', compact('report'));
    }

    public function current(Company $company)
    {
        $vehicles = $company->activeVehicles;
        $currentVehiclesIssues = CurrentVehicleIssue::whereIn('vehicle_id', $vehicles->pluck('id'))->withActiveIssue()->get();

        return view('operation.vehicles.issues.current', compact('currentVehiclesIssues'));
    }

    /**
     * @param Company $company
     * @param $vehicleReport
     * @param $dateReport
     * @param bool $sortDescending
     * @return object
     */
    public function buildReport(Company $company, $vehicleReport, $dateReport, $sortDescending = false)
    {
        $vehicles = ($vehicleReport == 'all') ? $company->vehicles : $company->vehicles()->where('id', $vehicleReport)->get();

        $vehicleIssues = VehicleIssue::whereIn('vehicle_id', $vehicles->pluck('id'));
        if ($dateReport) $vehicleIssues = $vehicleIssues->whereDate('date', $dateReport);
        $vehicleIssues = $vehicleIssues->get()->sortBy('date', 0, $sortDescending);


        return (object)[
            'company' => $company,
            'vehicleReport' => $vehicleReport,
            'dateReport' => $dateReport,
            'vehicleIssues' => $vehicleIssues,
            'isNotEmpty' => $vehicleIssues->isNotEmpty(),
            'sortDescending' => $sortDescending,
        ];
    }

    /**
     * @param Vehicle $vehicle
     * @param Request $request
     * @return Factory|View
     */
    public function form(Vehicle $vehicle, Request $request)
    {
        $company = $vehicle->company;
        $drivers = $company->activeDrivers;

        $presetOutIssue = $request->get('preset-out-issue') == "true";

        return view('operation.vehicles.issues.formCreate', compact(['vehicle', 'drivers', 'presetOutIssue']));
    }

    /**
     * @param Company $company
     * @param Request $request
     * @throws Throwable
     */
    public function migrateOldReports(Company $company, Request $request)
    {
        $oldReports = collect(\DB::select("SELECT * FROM report_vehicle_status WHERE vehicle_id in (SELECT id FROM vehicles WHERE company_id = $company->id) AND date < '2020-03-05' ORDER BY date_time ASC"));
        // $oldReports = collect(\DB::select("SELECT * FROM report_vehicle_status WHERE vehicle_id in (SELECT id FROM vehicles WHERE id = 1333) AND date < '2020-03-05' ORDER BY date_time ASC"));

        $oldReportsByVehicles = $oldReports->groupBy('vehicle_id');
        foreach ($oldReportsByVehicles as $vehicleId => $oldReportsByVehicle) {
            $vehicle = Vehicle::find($vehicleId);

            $vehicleIsActive = true;
            $vehicleIsInRepair = false;

            DB::statement("UPDATE current_vehicle_issues SET issue_type_id = 3 WHERE vehicle_id = $vehicleId");

            $init = false;
            $firstInit = true;

            foreach ($oldReportsByVehicle->sortBY('id') as $old) {
                $issueTypeId = VehicleIssueType::UPDATE;
                $forceOut = false;

                $observations = $old->observations;

                switch ($old->status) {
                    case 'EN TALLER':
                        $vehicleIsInRepair = true;
                        $issueTypeId = VehicleIssueType::IN;
                        $init = true;
                        break;
                    case 'EN TRANSITO':
                        $forceOut = true;
                        $vehicleIsInRepair = false;
                        $issueTypeId = VehicleIssueType::OUT;
                        break;
                    case 'DESACTIVADO':
                        $init = true;
                        $vehicleIsActive = false;
                        $issueTypeId = VehicleIssueType::IN;
                        $observations = __('Vehicle')." desactivado. $observations";
                        break;
                    case 'ACTIVADO':
                        $vehicleIsActive = true;
                        $issueTypeId = VehicleIssueType::OUT;
                        $observations = __('Vehicle')." activado. $observations";
                        break;
                }

                if ($init) {
                    if($firstInit){
                        $vehicle->active = true;
                        $vehicle->in_repair = false;
                        $firstInit = false;
                    }else{
                        $vehicle->active = $vehicleIsActive;
                        $vehicle->in_repair = $vehicleIsInRepair;
                    }

                    $this->novelty->create($vehicle, $issueTypeId, $observations, $forceOut, $vehicleIsInRepair, $old->date_time, $old->updated_user_id);
                }
            }
        }

        dump("Finished ".count($oldReports)." migrated");
    }


    /**
     * TODO: delete when list vehicle migrated to NE
     *
     * @param User $user
     * @param Vehicle $vehicle
     * @param Request $request
     * @return Factory|RedirectResponse|Redirector|View
     * @throws Throwable
     */
    public function createFromOldPlatform(User $user, Vehicle $vehicle, Request $request)
    {
        if (Auth::guest()) Auth::login($user, true);
        return $this->create($vehicle, $request);
    }

    /**
     * @param Vehicle $vehicle
     * @param Request $request
     * @return Factory|RedirectResponse|Redirector|View
     * @throws Throwable
     */
    public function create(Vehicle $vehicle, Request $request)
    {
        $issueTypeId = $request->get('issue_type_id');
        $observations = $request->get('observations');
        $forceOut = $request->get('force_out');
        $setInRepair = $request->get('set_in_repair') == "true";
        $transaction = $this->novelty->create($vehicle, $issueTypeId, $observations, $forceOut, $setInRepair);
        if ($transaction->success) {
            $request->session()->flash('message', $transaction->message);
            return view('operation.vehicles.issues.formConfirm', compact('currentIssue'));
        }

        $request->session()->flash('error', $transaction->message);
        return redirect(route('operation-vehicles-issues-form', ['vehicle' => $vehicle->id]));
    }
}
