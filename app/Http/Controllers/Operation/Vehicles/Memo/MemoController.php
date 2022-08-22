<?php

namespace App\Http\Controllers\Operation\Vehicles\Memo;

use App\Http\Controllers\Controller;
use App\Models\Company\Company;
use App\Models\Vehicles\Memos\Memo;
use App\Services\Auth\PCWAuthService;
use App\Services\Operation\Vehicles\MemoService;
use App\Services\Operation\Vehicles\NoveltyService;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemoController extends Controller
{
    /**
     * @var PCWAuthService
     */
    private $auth;

    /**
     * @var NoveltyService
     */
    private $novelty;

    /**
     * @var MemoService
     */
    private $memoService;

    /**
     * VehicleIssuesController constructor.
     * @param PCWAuthService $auth
     * @param NoveltyService $novelty
     * @param MemoService $service
     */
    public function __construct(PCWAuthService $auth, NoveltyService $novelty, MemoService $service)
    {
        $this->auth = $auth;
        $this->novelty = $novelty;
        $this->memoService = $service;
    }

    /**
     * @return Factory|View
     */
    public function index()
    {
        $access = $this->auth->access();

        $companies = $access->companies;
        $vehicles = $access->company->vehicles;

        return view('operation.vehicles.memos.index', compact(['companies', 'vehicles']));
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function show(Request $request)
    {
        $dateReport = $request->get('date-report');
        $withEndDate = $request->get('with-end-date');
        $dateEndReport = $withEndDate ? $request->get('date-end-report') : $dateReport;

        $vehicleReport = $request->get('vehicle-report');
        $sortDescending = $request->get('sort-desc');
        $company = $this->auth->getCompanyFromRequest($request);

        $report = $this->memoService->report($company, $vehicleReport, $dateReport, $withEndDate, $dateEndReport, $sortDescending);

        if ($request->get('export')) $this->novelty->export($report);

        return view('operation.vehicles.memos.show', compact('report'));
    }

    /**
     * @param Request $request
     * @return Factory|Application|View
     */
    public function formCreate(Request $request)
    {
        $company = $this->auth->getCompanyFromRequest($request);
        $vehicles = $company->vehicles;

        $memo = new Memo();

        return view('operation.vehicles.memos.formCreate', compact(['vehicles', 'memo']));
    }

    /**
     * @param Memo $memo
     * @param Request $request
     * @return Factory|Application|View
     */
    public function detail(Memo $memo, Request $request)
    {
        $company = $this->auth->getCompanyFromRequest($request);
        $vehicles = $company->vehicles;

        return view('operation.vehicles.memos.detail', compact(['vehicles', 'memo']));
    }

    /**
     * @param Memo $memo
     * @param Request $request
     * @return Factory|Application|View
     */
    public function formEdit(Memo $memo, Request $request)
    {
        $company = $this->auth->getCompanyFromRequest($request);
        $vehicles = $company->vehicles;

        return view('operation.vehicles.memos.formEdit', compact(['vehicles', 'memo']));
    }

    /**
     * @param Memo $memo
     * @return Factory|Application|View
     */
    public function formDelete(Memo $memo)
    {
        return view('operation.vehicles.memos.formDelete', compact(['memo']));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function create(Request $request)
    {
        return $this->memoService->process(null, $request);
    }

    /**
     * @param Memo $memo
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function update(Memo $memo, Request $request)
    {
        return $this->memoService->process($memo, $request);
    }

    /**
     * @param Memo $memo
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function delete(Memo $memo, Request $request)
    {
        $response = collect([
            'success' => true,
            'message' => __("Memo register deleted successfully")
        ]);

        if (!$memo->delete()) {
            $response->put('success', false);
            $response->put('message', __("Memo notification not deleted"));
        }

        return response()->json($response);
    }
}
