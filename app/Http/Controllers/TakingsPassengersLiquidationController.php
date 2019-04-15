<?php

namespace App\Http\Controllers;

use App\Services\Auth\PCWAuthService;
use App\Services\BEA\BEAService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TakingsPassengersLiquidationController extends Controller
{
    /**
     * @var PCWAuthService
     */
    private $pcwAuthService;
    /**
     * @var BEAService
     */
    private $beaService;

    public function __construct(PCWAuthService $pcwAuthService, BEAService $beaService)
    {
        $this->pcwAuthService = $pcwAuthService;
        $this->beaService = $beaService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        //$accessProperties = $this->pcwAuthService->getAccessProperties();
        //$companies = $accessProperties->companies;
        $vehicles = $this->beaService->getAllVehicles();

        return view('takings.passengers.liquidation.index', compact('vehicles'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws Exception
     */
    public function search(Request $request)
    {
        $vehicleReport = $request->get('vehicle-report');

        $beaMarks = $this->beaService->getBEAMarksFrom($vehicleReport);

        return view('takings.passengers.liquidation.show', compact('beaMarks'));
    }


}
