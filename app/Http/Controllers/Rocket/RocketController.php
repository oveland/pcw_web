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
                if ($vehicle) {
                    $date = $request->get('date');
                    $photos = $this->photoService->for($vehicle)->getHistoric($date);

                    if ($photos->count()) {
                        $photo = Photo::find($photos->last()->id);
                    }

////                      338 Alameda
//
//                        $photo = Photo::find(4594);
//                        $photo = Photo::find(4598);
//                        $photo = Photo::find(4536);
//
//                        $photo = Photo::find(4600);
//                        $photo = Photo::find(4491);
//
//                        $photo = Photo::find(4501);
//                        $photo = Photo::find(4569);
//                        $photo = Photo::find(4574);
//
//                        $photo = Photo::find(4493);
//
//
////                      76 Yumbeños
//
//                        $photo = Photo::find(8041); // 6/6
//                        $photo = Photo::find(8044); // 13/14
//                        $photo = Photo::find(8045); // 14/14
//                        $photo = Photo::find(8067); // 8/9
//                        $photo = Photo::find(8069); // 7/7
//                        $photo = Photo::find(8073); // 5/5
//                        $photo = Photo::find(8076); // 5/5
//                        $photo = Photo::find(8092); // 2/2
//                        $photo = Photo::find(8149); // 4/4
//                        $photo = Photo::find(8176); // 9/9
//                        $photo = Photo::find(8178); // 9/9
//
//                        $photo = Photo::find(11913); // 9/9
//
//
////                      566 Yumbeños
//
                    $photo = Photo::find(13314);

                    $photo = Photo::find(14173); // 17 / 17     (1, 2, 3, 5, 6, 7, 8, 9, 11, 12, 14, 16, 17, 18, 19, 21, 23)
                    $photo = Photo::find(14222); // 10 / 10     (1, 4, 7, 8, 9, 12, 16, 18, 20, 23)
                    $photo = Photo::find(14220); // 9 / 10      (1, 4, 7, 8, 9, 12, 18, 20, 23)
//                    $photo = Photo::find(14217); // 9 / 10      (1, 4, 7, 8, 9, 12, 18, 20, 23)
//                    $photo = Photo::find(14217); // 9 / 9       (1, 4, 7, 8, 12, 16, 18, 20, 23)
//                    $photo = Photo::find(14210); // 5 / 5       (1, 5, 12, 16, 18)



//                        $photo->processRekognition(true, 'persons_and_faces');
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
