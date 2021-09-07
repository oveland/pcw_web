<?php

namespace App\Http\Controllers\Reports\Liquidation;

use App;
use App\Http\Controllers\Controller;
use App\Models\BEA\Advance;
use App\Models\BEA\ManagementCost;
use App\Models\Drivers\Driver;
use App\Models\Vehicles\Vehicle;
use App\Services\Auth\PCWAuthService;
use App\Services\BEA\BEAService;
use App\Services\PCWExporterService;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PDF;
use Storage;

class ReportLiquidationController extends Controller
{
    /**
     * @var PCWAuthService
     */
    private $auth;
    /**
     * @var BEAService
     */
    private $beaService;
    /**
     * @var PCWExporterService
     */
    private $exporter;

    public function __construct(PCWAuthService $auth, PCWExporterService $exporter)
    {
        $this->auth = $auth;
        $this->exporter = $exporter;

        $this->middleware(function ($request, $next) {
            $this->beaService = App::makeWith('bea.service', ['company' => $this->auth->getCompanyFromRequest($request)->id]);
            return $next($request);
        });
    }

    function asDollars($value)
    {
        return '$' . number_format($value, 0);
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request)
    {
        $hideMenu = session('hide-menu');
        return view('reports.liquidation.index', compact('hideMenu'));
    }

    public function export(Request $request)
    {
        $vehicle = Vehicle::find($request->get('vehicle'));
        $driver = Driver::find($request->get('driver'));
        $date = explode(',', $request->get('date'));

        $initialDate = $date[0];
        $finalDate = $date[1] ?? $initialDate;

        $report = (object)$this->beaService->getMainReport($vehicle->id, $driver->id ?? null, $initialDate, $finalDate)->toArray();

        $options = (object)[
            'w' => 1500,
            'h' => 600,
            'dpi' => 2650,
        ];

        $pdf = PDF::setOptions([
            'dpi' => $options->dpi,
            'defaultFont' => 'sans-serif'
        ])->setPaper('A4', 'landscape')->loadView('reports.liquidation.exports.takings', compact(['report', 'vehicle', 'initialDate', 'finalDate']));

        return $pdf->stream(__('TakingsReport') . ".pdf");
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function search(Request $request)
    {
        $vehicle = Vehicle::find($request->get('vehicle'));
        $driver = Driver::find($request->get('driver'));
        $date = $request->get('date');
        $initialDate = $date;
        $finalDate = $date;

        if (is_array($date)) {
            $initialDate = $date[0];
            $finalDate = $date[1];
        }

        $dailyReport = $this->beaService->getMainReport($vehicle->id, $driver->id ?? null, $initialDate, $finalDate)->toArray();

        return response()->json($dailyReport);
    }

    /**
     * @param $name
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function getParams($name, Request $request)
    {
        switch ($name) {
            case __('search'):
                $company = $this->auth->getCompanyFromRequest($request);
                $access = $this->auth->access($company);

                return response()->json([
                    'company' => $this->beaService->repository->company,
                    'vehicles' => $this->beaService->repository->getAllVehicles(),
                    'drivers' => $this->beaService->repository->getAllDrivers(),
                    'companies' => $access->companies
                ]);
                break;
            case __('discounts'):
                $vehicle = Vehicle::find($request->get('vehicle'));
                $trajectory = $request->get('trajectory');

                $this->beaService->sync->checkDiscountsFor($vehicle);

                return response()->json($this->beaService->discount->byVehicleAndTrajectory($vehicle->id, $trajectory, true));
                break;
            case __('costs'):
                $vehicle = Vehicle::find($request->get('vehicle'));

                $this->beaService->sync->checkManagementCostsFor($vehicle);

                $costs = $this->beaService->repository->getManagementCosts($vehicle);

                return response()->json($costs->where('uid', '<>', ManagementCost::PAYROLL_ID)->values()->toArray());
                break;
            case __('advances'):
                $vehicle = Vehicle::find($request->get('vehicle'));

                return response()->json(Advance::findAllByVehicle($vehicle));
                break;
            default:
                return response()->json($this->beaService->getLiquidationParams(true));
                break;
        }
    }


    public function getFileDiscount($otherDiscountId)
    {
        $filePath = config('takings.discounts.others.files.path') . "/$otherDiscountId." . config('takings.discounts.others.files.image-extension');


        $exists = Storage::exists($filePath);
        if ($exists) {
            $file = Storage::get($filePath);
            $image = \Image::make($file);
            return $image->response(config('takings.discounts.others.files.image-extension'));
        } else {
            return response()->file("unavailable.jpeg");
        }
    }
}
