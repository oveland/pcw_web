<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Passengers\CobanPhoto;
use App\Models\Vehicles\Vehicle;
use App\Services\Auth\PCWAuthService;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CobanCameraController extends Controller
{
    /**
     * @var GeneralController
     */
    private $auth;

    public function __construct(PCWAuthService $pcwAuthService)
    {
        $this->auth = $pcwAuthService;
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request)
    {
        $company = $request->get('company');
        return view('reports.passengers.sensors.cameras.index', compact(['company']));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function searchParams(Request $request)
    {
        $company = Company::find($request->get('company'));
        return response()->json([
            'vehicles' => $company->vehicles,
            'date' => Carbon::now()->toDateString(),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request)
    {
        $dateReport = $request->get('date');
        $vehicle = Vehicle::find($request->get('vehicle') ?? 0);

        $photosReport = $this->getPhotosReport($vehicle, $dateReport);

        return response()->json($photosReport);
    }

    /**
     * @param Vehicle $vehicle
     * @param $date
     * @return object
     */
    private function getPhotosReport(Vehicle $vehicle = null, $date)
    {
        $report = collect([]);
        if ($vehicle) {
            $photos = CobanPhoto::whereDate('created_at', $date)
                ->where('vehicle_id', $vehicle->id)
                ->with(['vehicle', 'location', 'dispatchRegister'])
                ->orderByDesc('date')
                ->get();

            foreach ($photos as $photo) {
                $dispatchRegister = $photo->dispatchRegister;
                $location = $photo->location;
                $report->push([
                    'id' => $photo->id,
                    'date' => ($report->count() + 1) . " ⮞ " . $photo->date->toDateTimeString(),
                    'dispatchRegister' => $dispatchRegister ? $dispatchRegister->getAPIFields() : null,
                    'location' => $location ? $location->getAPIFields() : null
                ]);
            }
        }
        return $report;
    }

    /**
     * @param CobanPhoto $photo
     * @return BinaryFileResponse
     */
    public function showPhoto(CobanPhoto $photo)
    {
        $photoPackages = $photo->packages;

        if ($photoPackages->count()) {
            $fileName = "tmp.jpeg";
            $binary = pack("H*", $photoPackages->implode('data'));
            file_put_contents($fileName, $binary);

            return response()->file($fileName);
        } else {
            return response()->file("unavailable.jpeg");
        }
    }
}