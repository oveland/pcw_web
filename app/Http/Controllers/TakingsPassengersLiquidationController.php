<?php

namespace App\Http\Controllers;

use App\Services\Auth\PCWAuthService;
use App\Services\BEA\BEAService;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
     * @return Factory|View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $link = config('app.beta_server_url')."/link/recaudo/pasajeros/liquidacion/$user->id"; // TODO: Change when BETA migrated fully to NE domain

        return view('partials.iframe', compact('link'));
    }

    /**
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function search(Request $request)
    {
        $vehicleReport = $request->get('vehicle-report');

        $beaMarks = $this->beaService->getBEAMarksFrom($vehicleReport);

        return view('takings.passengers.liquidation.show', compact('beaMarks'));
    }


}
