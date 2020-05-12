<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\GeneralController;
use App\Services\Auth\PCWAuthService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AppsReportController extends Controller
{
    /**
     * @var GeneralController
     */
    private $auth;

    public function __construct(PCWAuthService $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request)
    {
        $company = $request->get('company');
        return view('reports.apps.index', compact('company'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        $report = $request->all();
        return response()->json($report);
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
                    'companies' => $access->companies
                ]);
                break;
            case __('another'):
                return response()->json([]);
                break;
        }
    }
}
