<?php

namespace App\Http\Controllers\Reports\Routes\Takings;

use App\Http\Controllers\GeneralController;
use App\Http\Controllers\Utils\StrTime;
use App\Models\Routes\DispatchRegister;
use App\Services\Auth\PCWAuthService;
use App\Services\Reports\Routes\DispatchService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class TakingsController extends Controller
{
    /**
     * @var GeneralController
     */
    private $auth;
    /**
     * @var DispatchService
     */
    private $dispatchService;

    public function __construct(PCWAuthService $auth)
    {
        $this->auth = $auth;
        $this->middleware(function ($request, $next) {
            $this->dispatchService = app(DispatchService::class);
            return $next($request);
        });
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request)
    {
        $hideMenu = session('hide-menu');
        return view('reports.route.takings.index', compact('hideMenu'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        $date = $request->get('date');
        $initialDate = $date;
        $finalDate = null;
        if (is_array($date)) {
            $initialDate = $date[0];
            $finalDate = $date[1];
        }

        $route = $request->get('route');
        $vehicle = $request->get('vehicle');

        $data = $this->dispatchService->getTurns($initialDate, $finalDate, $route, $vehicle);

        $totals = [
            'passengers' => $data->sum(function ($d) {
                return $d->passengers->recorders->count;
            }),
            'totalProduction' => $data->sum(function ($d) {
                return $d->takings->totalProduction;
            }),
            'control' => $data->sum(function ($d) {
                return $d->takings->control;
            }),
            'fuel' => $data->sum(function ($d) {
                return $d->takings->fuel;
            }),
            'others' => $data->sum(function ($d) {
                return $d->takings->others;
            }),
            'netProduction' => $data->sum(function ($d) {
                return $d->takings->netProduction;
            }),
            'routeTime' => StrTime::segToStrTime($data->sum(function ($d) {
                return StrTime::toSeg($d->routeTime);
            })),
        ];

        $averages = [
            'passengers' => intval($data->average(function ($d) {
                return $d->passengers->recorders->count;
            })),
            'totalProduction' => $data->average(function ($d) {
                return $d->takings->totalProduction;
            }),
            'control' => $data->average(function ($d) {
                return $d->takings->control;
            }),
            'fuel' => $data->average(function ($d) {
                return $d->takings->fuel;
            }),
            'others' => $data->average(function ($d) {
                return $d->takings->others;
            }),
            'netProduction' => $data->average(function ($d) {
                return $d->takings->netProduction;
            }),
            'routeTime' => StrTime::segToStrTime($data->average(function ($d) {
                return StrTime::toSeg($d->routeTime);
            })),
        ];

        return response()->json([
            'report' => $data,
            'totals' => $totals,
            'averages' => $averages,
        ]);
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
                    'company' => $company,
                    'vehicles' => $company->vehicles,
                    'routes' => $company->routes,
                    'companies' => $access->companies
                ]);
                break;
            case __('another'):
                return response()->json([]);
                break;
        }
    }
}
