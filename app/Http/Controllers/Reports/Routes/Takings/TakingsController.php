<?php

namespace App\Http\Controllers\Reports\Routes\Takings;

use App\Http\Controllers\GeneralController;
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
     * @return Factory|View
     */
    public function index()
    {
        $hideMenu = session('hide-menu');
        return view('reports.route.takings.index', compact('hideMenu'));
    }

    /**
     * @param Request $request
     * @return object
     */
    private function searchParams(Request $request)
    {
        $route = $request->get('route');
        $vehicle = $request->get('vehicle');
        $date = $request->get('date');
        $initialDate = $date;
        $finalDate = null;
        if (is_array($date)) {
            $initialDate = $date[0];
            $finalDate = $date[1];
        }

        return (object)compact(['initialDate', 'finalDate', 'route', 'vehicle']);
    }

    /**
     * @param Request $request
     * @return object
     */
    private function findReport(Request $request)
    {
        $params = $this->searchParams($request);
        return $this->dispatchService->getTakingsReport($params->initialDate, $params->finalDate, $params->route, $params->vehicle);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        $report = $this->findReport($request);
        return response()->json($report);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function export(Request $request)
    {
        $params = $this->searchParams($request);
        $report = $this->findReport($request);
        return $this->dispatchService->exportTakingsReport((object)compact(['report', 'params']));
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
            default:
                return response()->json([]);
                break;
        }
    }
}
