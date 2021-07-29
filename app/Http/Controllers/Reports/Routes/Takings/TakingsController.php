<?php

namespace App\Http\Controllers\Reports\Routes\Takings;

use App\Http\Controllers\Controller;
use App\Services\Auth\PCWAuthService;
use App\Services\BEA\BEAService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TakingsController extends Controller
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
        if(Auth::user()->role_id == 3 || Auth::user()->role_id == 4 )abort(403);
        $user = Auth::user();
        $link = config('app.beta_server_url')."/link/reportes/rutas/recaudo/$user->id"; // TODO: Change when BETA migrated fully to NE domain

        return view('partials.iframe', compact('link'));
    }
}
