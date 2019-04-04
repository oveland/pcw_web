<?php

namespace App\Http\Controllers;

use App\Services\Auth\PCWAuthService;
use Illuminate\Http\Request;

class TakingsPassengersLiquidationController extends Controller
{
    /**
     * @var PCWAuthService
     */
    private $pcwAuthService;

    public function __construct(PCWAuthService $pcwAuthService)
    {
        $this->pcwAuthService = $pcwAuthService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $accessProperties = $this->pcwAuthService->getAccessProperties();
        $companies = $accessProperties->companies;

        return view('takings.passengers.liquidation.index', compact('companies'));
    }

    /**
     * @param Request $request
     */
    public function search(Request $request)
    {
        $accessProperties = $this->pcwAuthService->getAccessProperties();
        $companies = $accessProperties->company;
        $vehicles = $accessProperties->vehicles;

        dd($accessProperties);
    }
}
