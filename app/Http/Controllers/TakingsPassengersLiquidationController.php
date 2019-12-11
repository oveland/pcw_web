<?php

namespace App\Http\Controllers;

use App\Facades\BEADB;
use App\Models\BEA\Commission;
use App\Models\BEA\Discount;
use App\Models\BEA\Liquidation;
use App\Models\BEA\Mark;
use App\Models\BEA\Penalty;
use App\Models\BEA\Trajectory;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use App\Services\Auth\PCWAuthService;
use App\Services\BEA\BEAService;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PDF;
use Storage;

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

    public function test(Request $request)
    {
        $m = BEADB::select("SELECT count(*) FROM A_MARCA WHERE AMR_FHINICIO > current_date");
        dd($m);
    }

    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {

        $hideMenu = session('hide-menu');
        return view('takings.passengers.liquidation.index', compact('hideMenu'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function searchLiquidation(Request $request)
    {
        $vehicleReport = $request->get('vehicle');
        $dateReport = $request->get('date');

        $beaMarks = $this->beaService->getBEAMarks($vehicleReport, $dateReport);

        return response()->json($beaMarks);
    }

    public function exportLiquidation(Liquidation $liquidation, Request $request)
    {
        $mark = Mark::find($request->get('mark'));

        $template = 'takings.passengers.liquidation.exports.liquidation' . ($mark ? 'Turn' : 'Total');
        $options = (object)[
            'w' => 350,
            'h' => 600,
            'dpi' => 150,
        ];

        $pdf = PDF::setOptions([
            'dpi' => $options->dpi,
            'defaultFont' => 'sans-serif'
        ])->setPaper(array(0, 0, $options->w, $options->h))->loadView($template, compact(['liquidation', 'mark']));

        return $pdf->stream(__('Receipt')."-$liquidation->id.pdf");
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function searchTakings(Request $request)
    {
        $vehicleReport = $request->get('vehicle');
        $dateReport = $request->get('date');

        $beaLiquidations = $this->beaService->getBEATakings($vehicleReport, $dateReport);

        return response()->json($beaLiquidations);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function searchTakingsList(Request $request)
    {
        $vehicleReport = $request->get('vehicle');
        $dateReport = $request->get('date');

        $beaLiquidations = $this->beaService->getBEATakingsList($vehicleReport, $dateReport);

        return response()->json($beaLiquidations);
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
                return response()->json($this->beaService->repository->getAllVehicles());
                break;
            case __('discounts'):
                $vehicle = $request->get('vehicle');
                $trajectory = $request->get('trajectory');

                return response()->json($this->beaService->discount->byVehicleAndTrajectory($vehicle, $trajectory));
                break;
            default:
                return response()->json($this->beaService->getLiquidationParams());
                break;
        }
    }

    /**
     * @param Discount $discount
     * @param $options
     * @return object
     */
    function processSaveOptionsDiscount(Discount $discount, $options)
    {
        $default = $options->for->vehicles == 'default' && $options->for->trajectories == 'default';

        $vehicles = collect([]);
        switch ($options->for->vehicles) {
            case 'all':
                $vehicles = $discount->vehicle->company->activeVehicles;
                break;
            case 'custom':
                foreach ($options->vehicles as $vehicleId) {
                    $vehicle = Vehicle::find($vehicleId);
                    if ($vehicle) $vehicles->push($vehicle);
                }
                break;
            default:
                $vehicles->push($discount->vehicle);
                break;
        }

        $trajectories = collect([]);
        switch ($options->for->trajectories) {
            case 'all':
                $routes = $discount->vehicle->company->activeRoutes;
                $trajectories = Trajectory::whereIn('route_id', $routes->pluck('id'))->get();
                break;
            case 'custom':
                foreach ($options->trajectories as $trajectoriesId) {
                    $trajectory = Trajectory::find($trajectoriesId);
                    if ($trajectory) $trajectories->push($trajectory);
                }
                break;
            default:
                $trajectories->push($discount->trajectory);
                break;
        }

        return (object)[
            'default' => $default,
            'vehicles' => $vehicles,
            'trajectories' => $trajectories,
        ];
    }

    /**
     * @param $name
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function setParams($name, Request $request)
    {
        switch ($name) {
            case __('discounts'):
                $response = (object)[
                    'error' => false,
                    'message' => __('Discount edited successfully'),
                ];

                $saveOptions = json_decode(json_encode($request->get('options')), FALSE);
                $discountToEdit = (object)$request->get('discount');
                $discount = Discount::find($discountToEdit->id);
                if ($discount) {
                    $options = $this->processSaveOptionsDiscount($discount, $saveOptions);

                    if ($options->default) {
                        $discount->value = $discountToEdit->value;
                        if (!$discount->save()) {
                            $response->error = true;
                            $response->message .= "<br> - " . __("Discount :name unable to update", ['name' => $discountToEdit->discount_type->name]);
                        }
                    } else {
                        foreach ($options->vehicles as $vehicle) {
                            foreach ($options->trajectories as $trajectory) {
                                $discountFromCustom = Discount::with(['vehicle', 'route', 'trajectory', 'discountType'])
                                    ->where('vehicle_id', $vehicle->id)
                                    ->where('trajectory_id', $trajectory->id)
                                    ->where('discount_type_id', $discountToEdit->discount_type_id)
                                    ->first();;

                                if ($discountFromCustom) {
                                    $discountFromCustom->value = $discountToEdit->value;

                                    if (!$discountFromCustom->save()) {
                                        $response->error = true;
                                        $response->message .= "<br> - " . __("Discount :name unable to update for vehicle :vehicle on trajectory :trajectory", [
                                                'name' => $discountToEdit->discount_type->name,
                                                'vehicle' => $vehicle->number,
                                                'trajectory' => $trajectory->name,
                                            ]);
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $response->error = true;
                    $response->message .= "<br> - " . __("Discount :name doesn't exists in the system", ['name' => $discountToEdit->discount_type->name]);
                }

                return response()->json($response);
                break;
            case __('commissions'):
                $response = (object)[
                    'error' => false,
                    'message' => __('Commission edited successfully'),
                ];

                $commissionToEdit = (object)$request->get('commission');
                $commission = Commission::find($commissionToEdit->id);

                if ($commission) {
                    $commission->type = $commissionToEdit->type;
                    $commission->value = $commissionToEdit->value;

                    if (!$commission->save()) {
                        $response->error = true;
                        $response->message .= "<br> - " . __("Commission unable to update");
                    }
                } else {
                    $response->error = true;
                    $response->message .= "<br> - " . __("Commission register doesn't exists in the system");
                }

                return response()->json($response);
                break;
            case __('penalties'):
                $response = (object)[
                    'error' => false,
                    'message' => __('Penalties edited successfully'),
                ];

                $penaltyToEdit = (object)$request->get('penalty');
                $penalty = Penalty::find($penaltyToEdit->id);

                if ($penalty) {
                    $penalty->type = $penaltyToEdit->type;
                    $penalty->value = $penaltyToEdit->value;

                    if (!$penalty->save()) {
                        $response->error = true;
                        $response->message .= "<br> - " . __("Penalty unable to update");
                    }
                } else {
                    $response->error = true;
                    $response->message .= "<br> - " . __("Penalty register doesn't exists in the system");
                }

                return response()->json($response);
                break;
            default:
                return response()->json([]);
                break;
        }
    }

    public function getFileDiscount($otherDiscountId)
    {
        $filePath = config('takings.discounts.others.files.path') . "/$otherDiscountId." . config('takings.discounts.others.files.image-extension');

        $exists = Storage::exists($filePath);
        if ($exists) {
            $file = Storage::get($filePath);
            $image = \Image::make($file);
            return $image->response(config('takings.discounts.others.files.image-extension'));
        } else {
            return response()->file("unavailable.jpeg");
        }
    }

    public function processDiscountFiles($dataLiquidation)
    {
        $processedOK = true;

        $otherDiscounts = $dataLiquidation['otherDiscounts'];

        foreach ($otherDiscounts as $otherDiscount) {
            $otherDiscount = (object)$otherDiscount;
            $base64File = $otherDiscount->fileUrl;
            if ($otherDiscount->hasFile && $base64File && !Str::contains($base64File, 'http')) {
                list($baseType, $image) = explode(';', $base64File);
                list(, $image) = explode(',', $image);
                $image = base64_decode($image);
                $imageName = config('takings.discounts.others.files.path') . "/$otherDiscount->id." . config('takings.discounts.others.files.image-extension');
                $processedOK = Storage::put($imageName, $image);
                if (!$processedOK) {
                    break;
                }
            }
        }

        return $processedOK;
    }

    /**
     * @param Request $request
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
        $dataLiquidation = collect($request->get('liquidation'));
        $marksID = collect($request->get('marks'));
        $falls = collect($request->get('falls'));

        $liquidation = new Liquidation();
        $liquidation->date = Mark::find($marksID->first())->date;
        $liquidation->vehicle_id = $request->get('vehicle');
        $liquidation->liquidation = $dataLiquidation;

        $liquidation->totals = collect($request->get('totals'))->toJson();
        $liquidation->user()->associate(Auth::user());
        $liquidation->taken = false;

        $vehicle = $liquidation->vehicle;
        $dateQuery = $liquidation->date->toDateString();

        $lastMarksNoLiquidated = Mark::where('date', '<', $dateQuery)
            ->whereHas('turn', function ($turn) use ($vehicle) {
                return $turn->where('vehicle_id', $vehicle->id);
            })
            ->where('liquidated', false)
            ->whereNotNull('trajectory_id')
            ->orderByDesc('date')
            ->limit(1)
            ->get()->first();

        if ($lastMarksNoLiquidated && false) {
            $response->success = false;
            $response->message = __('There are turns no liquidated in :date fot this vehicle', ['date' => $lastMarksNoLiquidated->date->toDateString()]);
        } else {
            if ($marksID->count()) {
                if ($liquidation->save()) {
                    foreach ($marksID as $markId) {
                        $mark = Mark::find($markId);
                        if ($mark) {
                            $mark->liquidated = true;
                            $mark->liquidation()->associate($liquidation);
                            $mark->pay_fall = collect($falls->get('pay'))->get($mark->id);
                            $mark->get_fall = collect($falls->get('get'))->get($mark->id);
                            if ($mark->save()) {
                                if (!$this->processDiscountFiles($dataLiquidation)) {
                                    $response->success = false;
                                    $response->message = __('Error saving other discounts files');
                                    DB::rollBack();
                                    break;
                                };
                            } else {
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
            } else {
                $response->success = false;
                $response->message = __('No there are registers for liquidation');
                DB::rollBack();
            }
        }

        DB::commit();

        return response()->json($response);
    }

    /**
     * @param Liquidation $liquidation
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function updateLiquidation(Liquidation $liquidation, Request $request)
    {
        $response = (object)[
            'success' => true,
            'message' => __('Liquidation updated successfully')
        ];

        DB::beginTransaction();
        $dataLiquidation = collect($request->get('liquidation'));
        $liquidation->liquidation = $dataLiquidation;
        $liquidation->totals = collect($request->get('totals'))->toJson();

        if (!$this->processDiscountFiles($dataLiquidation)) {
            $response->success = false;
            $response->message = __('Error saving other discounts files');
            DB::rollBack();
        };
        if (!$liquidation->save()) {
            $response->success = false;
            $response->message = __('Error at updating liquidation register');
            DB::rollBack();
        }

        DB::commit();

        return response()->json($response);
    }

    /**
     * @param Liquidation $liquidation
     * @return JsonResponse
     * @throws Exception
     */
    public function takings(Liquidation $liquidation)
    {
        $response = (object)[
            'success' => true,
            'message' => __('Taking processed successfully')
        ];

        DB::beginTransaction();

        $marks = $liquidation->marks;
        foreach ($marks as $mark) {
            $mark->taken = true;
            if (!$mark->save()) {
                DB::rollBack();
            }
        }

        $liquidation->taken = true;
        $liquidation->taking_date = Carbon::now();
        $liquidation->takingUser()->associate(Auth::user());

        if (!$liquidation->save()) {
            $response->success = false;
            $response->message = __('Error at generate taking register');
            DB::rollBack();
        }

        DB::commit();

        return response()->json($response);
    }
}
