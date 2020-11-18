<?php

namespace App\Http\Controllers\Operation\Vehicles\Binnacle;

use App\Http\Controllers\Controller;
use App\Models\Company\Company;
use App\Models\Vehicles\Binnacles\Binnacle;
use App\Models\Vehicles\Binnacles\Type;
use App\Models\Vehicles\CurrentVehicleIssue;
use App\Services\Auth\PCWAuthService;
use App\Services\Operation\Vehicles\BinnacleService;
use App\Services\Operation\Vehicles\NoveltyService;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BinnacleController extends Controller
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
     * @var BinnacleService
     */
    private $service;

    /**
     * VehicleIssuesController constructor.
     * @param PCWAuthService $auth
     * @param NoveltyService $novelty
     * @param BinnacleService $service
     */
    public function __construct(PCWAuthService $auth, NoveltyService $novelty, BinnacleService $service)
    {
        $this->auth = $auth;
        $this->novelty = $novelty;
        $this->service = $service;
    }

    /**
     * @return Factory|View
     */
    public function index()
    {
        $access = $this->auth->access();

        $companies = $access->companies;
        $vehicles = $access->company->vehicles;

        return view('operation.vehicles.binnacle.index', compact(['companies', 'vehicles']));
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function show(Request $request)
    {
        $dateReport = $request->get('date-report');
        $withEndDate = $request->get('with-end-date');
        $dateEndReport = $withEndDate ? $request->get('date-end-report') : $dateReport;

        $vehicleReport = $request->get('vehicle-report');
        $sortDescending = $request->get('sort-desc');
        $company = $this->auth->getCompanyFromRequest($request);

        $report = $this->service->report($company, $vehicleReport, $dateReport, $withEndDate, $dateEndReport, $sortDescending);

        if ($request->get('export')) $this->novelty->export($report);

        return view('operation.vehicles.binnacle.show', compact('report'));
    }

    public function current(Company $company)
    {
        $vehicles = $company->activeVehicles;
        $currentVehiclesIssues = CurrentVehicleIssue::whereIn('vehicle_id', $vehicles->pluck('id'))->withActiveIssue()->get();

        return view('operation.vehicles.binnacle.current', compact('currentVehiclesIssues'));
    }

    /**
     * @param Request $request
     * @return Factory|Application|View
     */
    public function formCreate(Request $request)
    {
        $company = $this->auth->getCompanyFromRequest($request);
        $vehicles = $company->vehicles;
        $binnacleTypes = Type::active()->get();
        $users = $company->users;

        $binnacle = null;

        return view('operation.vehicles.binnacle.formCreate', compact(['vehicles', 'binnacleTypes', 'binnacle', 'users']));
    }

    /**
     * @param Binnacle $binnacle
     * @param Request $request
     * @return Factory|Application|View
     */
    public function formEdit(Binnacle $binnacle, Request $request)
    {
        $company = $this->auth->getCompanyFromRequest($request);
        $vehicles = $company->vehicles;
        $binnacleTypes = Type::active()->get();
        $users = $company->users;

        return view('operation.vehicles.binnacle.formEdit', compact(['vehicles', 'binnacleTypes', 'binnacle', 'users']));
    }

    /**
     * @param Binnacle $binnacle
     * @return Factory|Application|View
     */
    public function formDelete(Binnacle $binnacle)
    {
        return view('operation.vehicles.binnacle.formDelete', compact(['binnacle']));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function create(Request $request)
    {
        return $this->service->process(null, $request);
    }

    /**
     * @param Binnacle $binnacle
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function update(Binnacle $binnacle, Request $request)
    {
        return $this->service->process($binnacle, $request);
    }

    /**
     * @param Binnacle $binnacle
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function delete(Binnacle $binnacle, Request $request)
    {
        $response = collect([
            'success' => true,
            'message' => __("Binnacle register deleted successfully")
        ]);

        if (!$binnacle->delete()) {
            $response->put('success', false);
            $response->put('message', __("Binnacle notification not deleted"));
        }

        return response()->json($response);
    }
}
