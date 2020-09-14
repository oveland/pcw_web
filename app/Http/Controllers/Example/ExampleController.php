<?php

namespace App\Http\Controllers\Example;

use App\Http\Controllers\GeneralController;
use App\Services\Auth\PCWAuthService;
use App\Services\TestService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ExampleController extends Controller
{
    /**
     * @var GeneralController
     */
    private $auth;
    /**
     * @var TestService
     */
    private $test;

    public function __construct(PCWAuthService $auth, TestService $test)
    {
        $this->auth = $auth;
        $this->test = $test;
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request)
    {
        return view('example.index');
    }

    public function test(Request $request)
    {
        return $this->test->test();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        return response()->json([
            $request->header()
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
                    'companies' => $access->companies
                ]);
                break;
            case __('another'):
                return response()->json([]);
                break;
        }
    }
}
