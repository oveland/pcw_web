<?php

namespace App\Http\Controllers;

use App\Services\API\Web\Track\TrackMapService;
use App\Services\Auth\PCWAuthService;
use Illuminate\Http\Request;

class OperationTrackMapController extends Controller
{
    /**
     * @var PCWAuthService
     */
    private $authService;
    /**
     * @var TrackMapService
     */
    private $trackMapService;

    public function __construct(PCWAuthService $authService, TrackMapService $trackMapService)
    {
        $this->authService = $authService;
        $this->trackMapService = $trackMapService;
    }

    public function index(Request $request)
    {
        dd('Index for Track map');
    }

    public function get(Request $request)
    {
        $companyId = $request->get('empresaShow');
        $routeID = $request->get('idRuta');

        $trackData = collect($this->trackMapService->track($companyId, $routeID))->take(10)->chunk(8);

        return response()->json();
    }
}
