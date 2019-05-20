<?php

namespace App\Http\Controllers;

use App\Models\BEA\Liquidation;
use App\Models\BEA\Mark;
use App\Services\Auth\PCWAuthService;
use App\Services\BEA\BEAService;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use function MongoDB\BSON\toJSON;
use PDF;

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
     * @return View
     */
    public function index()
    {
        //$accessProperties = $this->pcwAuthService->getAccessProperties();
        //$companies = $accessProperties->companies;

        return view('takings.passengers.liquidation.index');
    }

    /**
     * @return JsonResponse
     */
    public function getParamsSearch()
    {
        return response()->json($this->beaService->repository->getAllVehicles());
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function search(Request $request)
    {
        $vehicleReport = $request->get('vehicle');
        $dateReport = $request->get('date');

        $beaMarks = $this->beaService->getBEAMarks($vehicleReport, $dateReport);

        return response()->json($beaMarks);
    }

    public function exportLiquidation(Request $request)
    {
        $liquidation = Liquidation::find($request->get('id'));
        $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif'])->loadView('takings.passengers.liquidation.exports.liquidation',compact('liquidation'));

        $customPaper = array(0,0,360,360);
        $pdf->setPaper($customPaper);

        return $pdf->stream('invoice.pdf');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function searchLiquidated(Request $request)
    {
        $vehicleReport = $request->get('vehicle');
        $dateReport = $request->get('date');

        $beaLiquidations = $this->beaService->getBEALiquidations($vehicleReport, $dateReport);

        return response()->json($beaLiquidations);
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function getAllParams()
    {
        return response()->json($this->beaService->getLiquidationParams());
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function liquidate(Request $request)
    {
        $response = (object)[
            'success' => true,
            'message' => __('Liquidation processed successfully')
        ];

        DB::beginTransaction();
        $user = Auth::user();
        $liquidation = new Liquidation();
        $liquidation->date = Carbon::now();
        $liquidation->vehicle_id = $request->get('vehicle');
        $liquidation->liquidation = collect($request->get('liquidation'))->toJson();
        $liquidation->totals = collect($request->get('totals'))->toJson();
        $liquidation->user_id = $user->id;

        if ($liquidation->save()) {
            foreach ($request->get('marks') as $markId) {
                $mark = Mark::find($markId);
                if ($mark) {
                    $mark->liquidated = true;
                    $mark->liquidation_id = $liquidation->id;
                    if (!$mark->save()) {
                        $response->success = false;
                        $response->message = __('Error at associate liquidation with BEA Mark register');
                        DB::rollBack();
                        break;
                    };
                }
            }
        } else {
            $response->success = false;
            $response->message = __('Error at generate liquidation register');
            DB::rollBack();
        }

        DB::commit();

        return response()->json($response);
    }
}
