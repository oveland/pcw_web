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
                    $response->photos = $photos;

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
                if ($vehicle) {
                    $date = $request->get('date');
                    $photos = $this->photoService->for($vehicle)->getHistoric($date);
//                    $photo = Photo::where('id', 4594)->where('vehicle_id', $vehicle->id)->first(); // Indeterminado...
//                    $photo = Photo::where('id', 4598)->where('vehicle_id', $vehicle->id)->first(); // Indeterminado...
//                    $photo = Photo::where('id', 4536)->where('vehicle_id', $vehicle->id)->first(); // Similar a 4600
//
//
//                    $photo = Photo::where('id', 4600)->where('vehicle_id', $vehicle->id)->first();
//
//
//                    $photo = Photo::where('id', 4491)->where('vehicle_id', $vehicle->id)->first();
//
//                    $photo = Photo::where('id', 4501)->where('vehicle_id', $vehicle->id)->first();
//                    $photo = Photo::where('id', 4569)->where('vehicle_id', $vehicle->id)->first();
//                    $photo = Photo::where('id', 4574)->where('vehicle_id', $vehicle->id)->first();
//
                    $photo = Photo::where('id', 4493)->where('vehicle_id', $vehicle->id)->first();


                    // 76 Yumbeños
//                    $photo = Photo::where('id', 8041)->where('vehicle_id', $vehicle->id)->first(); // 6/6
                    $photo = Photo::where('id', 8044)->where('vehicle_id', $vehicle->id)->first(); // 13/14
//                    $photo = Photo::where('id', 8045)->where('vehicle_id', $vehicle->id)->first(); // 14/14
//                    $photo = Photo::where('id', 8067)->where('vehicle_id', $vehicle->id)->first(); // 8/9
//                    $photo = Photo::where('id', 8069)->where('vehicle_id', $vehicle->id)->first(); // 7/7
//                    $photo = Photo::where('id', 8073)->where('vehicle_id', $vehicle->id)->first(); // 5/5
//                    $photo = Photo::where('id', 8076)->where('vehicle_id', $vehicle->id)->first(); // 5/5
//                    $photo = Photo::where('id', 8092)->where('vehicle_id', $vehicle->id)->first(); // 2/2
//                    $photo = Photo::where('id', 8149)->where('vehicle_id', $vehicle->id)->first(); // 4/4
//                    $photo = Photo::where('id', 8176)->where('vehicle_id', $vehicle->id)->first(); // 9/9
//                    $photo = Photo::where('id', 8178)->where('vehicle_id', $vehicle->id)->first(); // 9/9

                    if ($photo) {
//                        $photo->processRekognition(true, 'persons_and_faces');
//                        $photo->save();

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
