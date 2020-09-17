<?php

namespace App\Http\Controllers\Rocket;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralController;
use App\Models\Apps\Rocket\Photo;
use App\Models\Apps\Rocket\ProfileSeat;
use App\Models\Vehicles\Vehicle;
use App\Services\Apps\Rocket\Photos\PhotoService;
use App\Services\Auth\PCWAuthService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RocketController extends Controller
{
    /**
     * @var GeneralController
     */
    private $auth;
    /**
     * @var PhotoService
     */
    private $photoService;

    public function __construct(PCWAuthService $auth, PhotoService $photoService)
    {
        $this->auth = $auth;
        $this->photoService = $photoService;
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request)
    {
        return view('admin.rocket.index');
    }

    /**
     * @param $name
     * @param Request $request
     * @return JsonResponse
     */
    public function report($name, Request $request)
    {
        $response = (object)[
            'success' => true,
            'message' => '',
        ];

        switch ($name) {
            case 'historic':
                $vehicle = Vehicle::find($request->get('vehicle'));

                if ($vehicle) {
                    $date = $request->get('date');
                    $photos = $this->photoService->for($vehicle)->getHistoric($date);
                    $response->photos = $photos->sortByDesc('time')->values();

                    $profileSeat = ProfileSeat::where('vehicle_id', $vehicle->id)->first();
                    $response->seating = $profileSeat ? $profileSeat->occupation : [];
                } else {
                    $response->success = false;
                    $response->message = __('Vehicle not found');
                }
                break;
            default:
                $response->success = false;
                $response->message = __('Report not found');
                break;
        }

        return response()->json($response);
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
            case __('occupation'):
                $response = (object)[
                    'success' => true,
                    'message' => '',
                    'seating' => [],
                    'photo' => null
                ];

                $vehicle = Vehicle::find($request->get('vehicle'));
                $photo = null;
                if ($vehicle) {
                    $date = $request->get('date');
                    $photos = $this->photoService->for($vehicle)->getHistoric($date);

                    if ($photos->count()) {
                        $photo = Photo::find($photos->last()->id);
                    }

//                    $photo = Photo::find(39843);

//                        $photo->processRekognition(true, 'persons');
//                        $photo->save();

                    if ($photo) {
                        $response->photo = $this->photoService->getPhotoData($photo, $photos);
                    }

                    $profileSeat = ProfileSeat::where('vehicle_id', $vehicle->id)->first();
                    $response->seating = $profileSeat ? $profileSeat->occupation : [];
                } else {
                    $response->success = false;
                    $response->message = __('Vehicle not found');
                }


                return response()->json($response);
                break;
        }

        return null;
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
            case 'occupation':
                $response = (object)[
                    'success' => true,
                    'message' => __('Profile seating save successfully'),
                ];

                $vehicle = Vehicle::find($request->get('vehicle'));
                if ($vehicle) {
                    $seating = collect($request->get('seating'));
                    $profileSeat = ProfileSeat::where('vehicle_id', $vehicle->id)->first();
                    $profileSeat = $profileSeat ? $profileSeat : new ProfileSeat();
                    $profileSeat->vehicle()->associate($vehicle);

                    $profileSeat->occupation = $seating->transform(function ($seat) {
                        $seat['selected'] = false;
                        return $seat;
                    });

                    if (!$profileSeat->save()) {
                        $response->success = false;
                        $response->message = __('Error saving profile seat!');
                    }
                } else {
                    $response->success = false;
                    $response->message = __('Vehicle not found');
                }


                return response()->json($response);
                break;
        }
    }
}
