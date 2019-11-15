<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Utils\Geolocation;
use App\Models\Company\Company;
use App\Models\Vehicles\Location;
use App\Models\Vehicles\VehicleStatus;
use App\Models\Vehicles\Vehicle;
use App\Models\Vehicles\VehicleStatusReport;
use App\Services\Auth\PCWAuthService;
use App\Services\Reports\Routes\SpeedingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class VehicleStatusReportController extends Controller
{
    /**
     * @var PCWAuthService
     */
    private $authService;

    /**
     * SpeedingReportController constructor.
     *
     * @param PCWAuthService $authService
     */
    public function __construct(PCWAuthService $authService)
    {
        $this->authService = $authService;
    }

    public function index(Request $request)
    {
        $access = $this->authService->getAccessProperties();
        $companies = $access->companies;
        $vehicles = $access->vehicles;

        $vehicleStatusList = VehicleStatus::visibleFilter()->orderBy('order')->get();

        return view('reports.vehicles.status.index',compact(['companies', 'vehicles', 'vehicleStatusList']));
    }

    public function searchReport(Request $request)
    {
        $company = $this->authService->getCompanyFromRequest($request);

        $statusReport = collect($request->get('status-report'));
        $vehicleStatusList = VehicleStatus::visibleFilter()->orderBy('des_status')->get();
        $vehicleStatusList = $statusReport->isEmpty() ? $vehicleStatusList : $vehicleStatusList->whereIn('id', $statusReport);

        $dateReport = $request->get('date-report');
        list($initialTime, $finalTime) = explode(';', $request->get('time-range-report'));

        $vehicleReport = $request->get('vehicle-report');
        $vehicles = $vehicleReport == 'all' ?  $company->vehicles : $company->vehicles()->where('id', $vehicleReport)->get();

        $vehicleStatusReports = VehicleStatusReport::with('status')
            ->whereDate('date',$dateReport)
            ->whereBetween('time',["$initialTime:00", "$finalTime:00"])
            ->whereIn('vehicle_id', $vehicles->pluck('id'))
            ->whereIn('vehicle_status_id', $vehicleStatusList->pluck('id'))
            ->orderBy('id')
            ->get()
            ->groupBy('vehicle_id');

        return view('reports.vehicles.status.report', compact('vehicleStatusReports'));
    }

    /**
     * @param VehicleStatusReport $vehicleStatusReport
     * @return mixed
     */
    public function getImageFromCoordinate(VehicleStatusReport $vehicleStatusReport)
    {
        return Geolocation::getImageLocationFromCoordinates($vehicleStatusReport->latitude, $vehicleStatusReport->longitude);
    }
}
