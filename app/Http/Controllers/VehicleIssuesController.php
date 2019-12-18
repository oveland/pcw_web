<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Users\User;
use App\Models\Vehicles\CurrentVehicleIssue;
use App\Models\Vehicles\Vehicle;
use App\Models\Vehicles\VehicleIssue;
use App\Models\Vehicles\VehicleIssueType;
use App\Services\Auth\PCWAuthService;
use App\Services\PCWExporterService;
use Auth;
use Illuminate\Http\Request;

class VehicleIssuesController extends Controller
{
    /**
     * @var PCWAuthService
     */
    private $auth;

    /**
     * ReportRouteController constructor.
     * @param PCWAuthService $auth
     */
    public function __construct(PCWAuthService $auth)
    {
        $this->auth = $auth;
    }

    public function index()
    {
        $access = $this->auth->access();

        $companies = $access->companies;
        $vehicles = $access->vehicles;

        return view('operation.vehicles.issues.index', compact(['companies', 'vehicles']));
    }

    public function show(Request $request)
    {
        $dateReport = $request->get('date-report');
        $vehicleReport = $request->get('vehicle-report');
        $company = $this->auth->getCompanyFromRequest($request);

        $report = $this->buildReport($company, $vehicleReport, $dateReport);

        if ($request->get('export')) $this->export($report);

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
     * @return object
     */
    public function buildReport(Company $company, $vehicleReport, $dateReport)
    {
        $vehicles = ($vehicleReport == 'all') ? $company->activeVehicles : $company->activeVehicles()->where('id', $vehicleReport)->get();

        $vehicleIssues = VehicleIssue::whereDate('date', $dateReport)
            ->whereIn('vehicle_id', $vehicles->pluck('id'))
            ->get()->sortBy('date');

        return (object)[
            'company' => $company,
            'vehicleReport' => $vehicleReport,
            'dateReport' => $dateReport,
            'vehicleIssues' => $vehicleIssues
        ];
    }

    /**
     * Export report to excel format
     *
     * @param $report
     */
    public function export($report)
    {
        $vehicleIssuesGroups = $report->vehicleIssues->groupBy('issue_uid');

        $dataExcel = array();
        foreach ($vehicleIssuesGroups as $issueUid => $vehicleIssuesGroup){
            $issueIn = VehicleIssue::where('issue_uid', $issueUid)->where('issue_type_id', VehicleIssueType::IN)->get()->first();
            $dateIn = $issueIn ? $issueIn->date : null;

            foreach ($vehicleIssuesGroup->sortBy('date') as $issue) {
                $vehicle = $issue->vehicle;
                $type = $issue->type;
                $user = $issue->user;
                $driver = $issue->driver;

                $duration = $type->id == VehicleIssueType::OUT ? ($dateIn ? $issue->date->diffAsCarbonInterval($dateIn, false)->forHumans() : __('Greater than one day') ) : null;

                $dataExcel[] = [
                    __('Vehicle') => $vehicle->number,                      # A CELL
                    __('Date') => $issue->date->toDateTimeString(),          # B CELL
                    __('Type') => $type->name.($duration ? "\n$duration" : ""),                              # C CELL
                    __('Vehicle issue') => $issue->observations,            # D CELL
                    __('Driver') => $driver ? $driver->fullName() : "",     # E CELL
                    __('User') => $user->name,                              # F CELL
                ];
            }
        }

        PCWExporterService::excel([
            'fileName' => __('Vehicle issues')." ".__('Vehicles') . " $report->dateReport",
            'title' => __('Vehicle issues')." ".__('Vehicles') . " $report->dateReport",
            'subTitle' => __('Vehicle issues')." ".__('Vehicles'),
            'data' => $dataExcel
        ]);
    }

    public function form(Vehicle $vehicle, Request $request)
    {
        $company = $vehicle->company;
        $drivers = $company->activeDrivers;
        return view('operation.vehicles.issues.formCreate', compact(['vehicle', 'drivers']));
    }

    public function create(Vehicle $vehicle, Request $request)
    {
        $transaction = \DB::transaction(function () use ($vehicle, $request) {
            $success = false;
            $message = "";

            $currentIssue = $vehicle->getCurrentIssue();

            $currentIssue->issue_type_id = $currentIssue->readyForIn() ? VehicleIssueType::IN : $request->get('issue_type_id');
            $currentIssue->generateUid();

            $currentIssue->observations = $request->get('observations');

            $issue = new VehicleIssue($currentIssue->toArray());


            if ($currentIssue->save() && $issue->save()) {
                $success = true;
                $message = __('Issue registered successfully') . ". ";
            } else {
                if ($currentIssue->save()) $message .= __('Error in registering issue') . ". ";
                if ($currentIssue->save()) $message .= __('Error in registering Current issue') . ". ";
            }

            return (object)[
                'success' => $success,
                'message' => $message,
            ];
        });

        if ($transaction->success) {
            $request->session()->flash('message', $transaction->message);
            return view('operation.vehicles.issues.formConfirm', compact('currentIssue'));
        }

        $request->session()->flash('error', $transaction->message);
        return redirect(route('operation-vehicles-issues-form', ['vehicle' => $vehicle->id]));
    }
}
