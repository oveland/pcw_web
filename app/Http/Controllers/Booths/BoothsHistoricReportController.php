<?php

namespace App\Http\Controllers\Booths;

use App\Models\Company\Company;
use App\Models\Passengers\CounterIssue;
use App\Models\Passengers\Passenger;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use App\Services\Auth\PCWAuthService;
use App\Services\Reports\Routes\RouteService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class BoothsHistoricReportController extends Controller
{
    /**
     * @var PCWAuthService
     */
    private $auth;

    /**
     * BoothsHistoricReportController constructor.
     * @param PCWAuthService $authService
     */
    public function __construct(PCWAuthService $authService)
    {
        $this->auth = $authService;
    }

    /**
     * @param Request $request
     * @return Factory|Application|View
     */
    public function index(Request $request)
    {
        $access = $this->auth->access();
        $companies = $access->companies;
        $vehicles = $access->vehicles;
        return view('reports.booths.historic.index', compact(['companies', 'vehicles']));
    }

    public function search(Request $request)
    {
        $company = $this->auth->getCompanyFromRequest($request);
        $vehicle = $this->auth->getVehicleFromRequest($request);
        $initialDate = $request->get('initial-date');
        $finalDate = $request->get('final-date');
        if ($initialDate > $finalDate) return view('partials.dates.invalidRange');

        $passengers = Passenger::findAllByDateRange($vehicle->id, $initialDate, $finalDate)->orderBy('id')
            ->paginate(50);

        $passengers->appends($request->all());

        $initialPassengerCount = Passenger::findAllByDateRange($vehicle->id, $initialDate, $finalDate)->orderBy('id')->limit(1)->get()->first();
        $lastPassengerCount = Passenger::findAllByDateRange($vehicle->id, $initialDate, $finalDate)->orderByDesc('id')->limit(1)->get()->first();

        return view('reports.booths.historic.listHistory', compact('passengers'))->with([
            'initialPassengerCount' => $initialPassengerCount,
            'lastPassengerCount' => $lastPassengerCount,
        ]);
    }
}
