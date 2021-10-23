<?php

namespace App\Http\Controllers;

use App;
use App\Models\LM\Advance;
use App\Models\LM\Commission;
use App\Models\LM\Discount;
use App\Models\LM\Liquidation;
use App\Models\LM\ManagementCost;
use App\Models\LM\Mark;
use App\Models\LM\Penalty;
use App\Models\LM\Trajectory;
use App\Models\Vehicles\Vehicle;
use App\Services\Auth\PCWAuthService;
use App\Services\LM\LMService;
use App\Services\PCWExporterService;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PDF;
use Storage;
use Validator;
use App\Facades\DFSDB;

class TakingsPassengersLiquidationController extends Controller
{
    /**
     * @var PCWAuthService
     */
    private $auth;
    /**
     * @var LMService
     */
    private $lmService;
    /**
     * @var PCWExporterService
     */
    private $exporter;

    public function __construct(PCWAuthService $auth, PCWExporterService $exporter)
    {
        $this->auth = $auth;
        $this->exporter = $exporter;

        $this->middleware(function ($request, $next) {
            $this->lmService = App::makeWith('lm.service', ['company' => $this->auth->getCompanyFromRequest($request)->id]);
            return $next($request);
        });
    }

    function asDollars($value)
    {
        return '$' . number_format($value, 0);
    }

    /**
     * @param Request $request
     * @return Factory|View
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

        $lmTurns = $this->lmService->getLMTurns($vehicleReport, $dateReport);

        return response()->json($lmTurns);
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

        return $pdf->stream(__('Receipt') . "-$liquidation->id.pdf");
    }

    public function exportDailyReport(Request $request)
    {
        $vehicle = Vehicle::find($request->get('vehicle'));
        $date = $request->get('date');

        $report = (object)$this->lmService->getDailyReport($vehicle->id, $date)->toArray();

        $options = (object)[
            'w' => 1500,
            'h' => 600,
            'dpi' => 2650,
        ];

        $pdf = PDF::setOptions([
            'dpi' => $options->dpi,
            'defaultFont' => 'sans-serif'
        ])->setPaper('A4', 'landscape')->loadView('takings.passengers.liquidation.exports.dailyReport', compact(['report', 'vehicle', 'date']));

        return $pdf->stream(__('Daily report') . "-$date.pdf");
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function searchDailyReport(Request $request)
    {
        $vehicleReport = $request->get('vehicle');
        $dateReport = $request->get('date');

        $dailyReport = $this->lmService->getDailyReport($vehicleReport, $dateReport)->toArray();

        return response()->json($dailyReport);
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

        $lmTakings = $this->lmService->getLMTakings($vehicleReport, $dateReport)->values()->toArray();

        return response()->json($lmTakings);
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

        $lmTakingsList = $this->lmService->getLMTakingsList($vehicleReport, $dateReport)->values()->toArray();

        return response()->json($lmTakingsList);
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
                    'company' => $this->lmService->repository->company,
                    'vehicles' => $this->lmService->repository->getAllVehicles(),
                    'companies' => $access->companies
                ]);
                break;
            case __('discounts'):
                $vehicle = Vehicle::find($request->get('vehicle'));
                $trajectory = $request->get('trajectory');

                $this->lmService->sync->checkDiscountsFor($vehicle);

                return response()->json($this->lmService->discount->byVehicleAndTrajectory($vehicle->id, $trajectory, true));
                break;
            case __('costs'):
                $vehicle = Vehicle::find($request->get('vehicle'));

                $this->lmService->sync->checkManagementCostsFor($vehicle);

                $costs = $this->lmService->repository->getManagementCosts($vehicle);

                return response()->json($costs->where('uid', '<>', ManagementCost::PAYROLL_ID)->values()->toArray());
                break;
            case __('advances'):
                $vehicle = Vehicle::find($request->get('vehicle'));

                return response()->json(Advance::findAllByVehicle($vehicle));
                break;
            default:
                return response()->json($this->lmService->getLiquidationParams(true));
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
                    'data' => []
                ];

                $saveOptions = json_decode(json_encode($request->get('options')), FALSE);
                $discountToEdit = (object)$request->get('discount');
                $discount = Discount::find($discountToEdit->id);
                if ($discount) {
                    $options = $this->processSaveOptionsDiscount($discount, $saveOptions);

                    $data = collect([]);

                    if ($options->default) {
                        $discount->value = $discountToEdit->value;
                        $discount->optional = $discountToEdit->optional;
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
                                    ->first();

                                if ($discountFromCustom) {
                                    $discountFromCustom->value = $discountToEdit->value;
                                    $discountFromCustom->optional = $discountToEdit->optional;

                                    $data->push(" Vehicle $vehicle->number,  $trajectory->name,  Discount $discountFromCustom->id ");

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

                    $response->data = $data->toArray();
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
            case __('costs'):
                $response = (object)[
                    'error' => false,
                    'message' => __('Cost edited successfully'),
                ];
                $newCost = (object)$request->get('cost');
                $validator = Validator::make(collect($newCost)->toArray(), [
                    'name' => 'required|max:255',
                    'concept' => 'required',
                    'value' => 'required|numeric',
                    'priority' => 'required|numeric',
                ], [
                    'required' => __('The :attribute field is required'),
                ]);

                if ($validator->errors()->count()) {
                    $response->error = true;
                    $response->message = '<hr> ✗ ' . collect($validator->errors())->flatten()->implode('<br> ✗ ');
                } else {
                    if ($newCost->create ?? null) {
                        $cost = new ManagementCost([
                            'vehicle_id' => $newCost->vehicleId,
                            'uid' => intval(ManagementCost::where('vehicle_id', $newCost->vehicleId)->max('uid')) + 1,
                        ]);
                    } else {
                        $cost = ManagementCost::find($newCost->id);
                    }

                    if ($cost) {
                        $cost->name = $newCost->name;
                        $cost->concept = $newCost->concept;
                        $cost->value = $newCost->value;
                        $cost->active = $newCost->active;
                        $cost->priority = $newCost->priority;

                        if (!$cost->save()) {
                            $response->error = true;
                            $response->message .= "<br> - " . __("Error saving cost register");
                        }
                    } else {
                        $response->error = true;
                        $response->message .= "<br> - " . __("Cost register doesn't exists in the system");
                    }
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
            if ($otherDiscount->hasFile && $base64File && Str::contains($base64File, 'data:image')) {
                $processedOK = false;
                try {
                    list($baseType, $image) = explode(';', $base64File);
                    list(, $image) = explode(',', $image);
                    $image = base64_decode($image);
                    $imageName = config('takings.discounts.others.files.path') . "/$otherDiscount->id." . config('takings.discounts.others.files.image-extension');
                    $processedOK = Storage::put($imageName, $image);
                    if (!$processedOK) {
                        break;
                    }
                } catch (Exception $e) {
                    $processedOK = false;
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
        $marks = collect($request->get('marks'));
        $falls = collect($request->get('falls'));

        $liquidation = new Liquidation();
        $liquidation->date = Mark::find($marks->keys()->first())->date;
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
            if ($marks->count()) {
                if ($liquidation->save()) {
                    foreach ($marks as $markId => $discountsParams) {
                        $mark = Mark::find($markId);

                        $mark->setDiscountsParams($discountsParams);

                        if ($mark) {
                            $mark->liquidated = true;
                            $mark->taken = false;
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
                                $response->message = __('Error at associate liquidation with ML Mark register');
                                DB::rollBack();
                                break;
                            };
                        }
                    }

                    foreach (Advance::TYPES as $type) {
                        Advance::findByVehicle($vehicle, $type)->liquidate($liquidation)->save();
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
            $mark->liquidated = true;
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

    public function setAdvances(Vehicle $vehicle, Request $request)
    {
        $response = (object)[
            'success' => true,
            'message' => __('Advances saved successfully'),
            'value' => 0
        ];

        foreach ($request->get('advances') as $type => $value) {
            $advance = Advance::findByVehicle($vehicle, $type);

            $advance->value = round(preg_replace("/[^0-9.]/", "", $value));

            if (!$advance->save()) {
                $response->success = false;
                $response->message = __("Error setting advance $type");
            }
        }

        $response->advances = Advance::findAllByVehicle($vehicle);

        return response()->json($response);

    }
}
