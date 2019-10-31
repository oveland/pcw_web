<?php

namespace App\Http\Controllers;

use App\Models\Vehicles\Vehicle;
use App\Models\Vehicles\VehicleIssue;
use App\Models\Vehicles\VehicleIssueType;
use App\Services\Auth\PCWAuthService;
use Illuminate\Http\Request;

class VehicleIssuesController extends Controller
{
    /**
     * @var PCWAuthService
     */
    private $authService;

    /**
     * ReportRouteController constructor.
     * @param PCWAuthService $authService
     */
    public function __construct(PCWAuthService $authService)
    {
        $this->authService = $authService;
    }

    public function index()
    {
        $accessProperties = $this->authService->getAccessProperties();

        $companies = $accessProperties->companies;
        $vehicles = $accessProperties->vehicles;

        return view('operation.vehicles.issues.index', compact(['companies', 'vehicles']));
    }

    public function show(Request $request)
    {
        dd($request->all());
    }

    public function form(Vehicle $vehicle, Request $request)
    {
        return view('operation.vehicles.issues.formCreate', compact('vehicle'));
    }

    public function create(Vehicle $vehicle, Request $request)
    {
        $transaction = \DB::transaction(function () use ($vehicle, $request) {
            $success = false;
            $message = "";

            $currentIssue = $vehicle->getCurrentIssue();

            $currentIssue->issue_type_id = $currentIssue->readyForIn() ? VehicleIssueType::IN : $request->get('issue_type_id');
            $currentIssue->generateUid();

            $currentIssue->observations = $request->get('observations');

            $issue = new VehicleIssue($currentIssue->toArray());


            if ($currentIssue->save() && $issue->save()) {
                $success = true;
                $message = __('Issue registered successfully') . ". ";
            } else {
                if ($currentIssue->save()) $message .= __('Error in registering issue') . ". ";
                if ($currentIssue->save()) $message .= __('Error in registering Current issue') . ". ";
            }

            return (object)[
                'success' => $success,
                'message' => $message,
            ];
        });

        if ($transaction->success) {
            $request->session()->flash('message', $transaction->message);
            return view('operation.vehicles.issues.formConfirm', compact('currentIssue'));
        }

        $request->session()->flash('error', $transaction->message);
        return redirect(route('operation-vehicles-issues-form', ['vehicle' => $vehicle->id]));
    }
}
