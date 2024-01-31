<?php

namespace App\Http\Controllers\Rocket;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralController;
use App\Models\Apps\Rocket\Photo;
use App\Models\Apps\Rocket\ProfileSeat;
use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Vehicle;
use App\Services\Apps\Rocket\Photos\PhotoService;
use App\Services\Auth\PCWAuthService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportPhotosController extends Controller
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
        $hideMenu = session('hide-menu');
        return view('reports.passengers.photos.index', compact('hideMenu'));
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
                $camera = $request->get('camera');

                if ($vehicle) {
                    $date = $request->get('date');
                    $persistenceActivate = $request->get('activate');
                    $persistenceRelease = $request->get('release');

                    $photos = $this->photoService->for($vehicle, $camera, $persistenceActivate, $persistenceRelease, $date)->getHistoric(true);

                    $response->photos = $photos->sortByDesc('ts')->values();

                    $response->seating = $vehicle->getProfileSeating($camera, $date)->occupation;
                } else {
                    $response->success = false;
                    $response->message = __('Vehicle not found');
                }
                break;
            case 'historic-seating-counts':
                $dr = DispatchRegister::find($request->get('dr'));

                if ($dr) {
                    $vehicle = $dr->vehicle;
                    $date = $dr->date;

                    $data = collect([
                        [
                            'seat' => 1,
                            'history' => [
                                [
                                    'id' => 11,
                                    'date' => '',
                                    'camera' => 1,
                                    'url' => 'asasssasss',
                                    'active' => true
                                ],
                                [
                                    'id' => 12,
                                    'date' => '',
                                    'camera' => 1,
                                    'url' => 'asasssasss',
                                    'active' => true
                                ],
                                [
                                    'id' => 13,
                                    'date' => '',
                                    'camera' => 1,
                                    'url' => 'asasssasss',
                                    'active' => true
                                ]
                            ]
                        ],
                        [
                            'seat' => 2,
                            'history' => [
                                [
                                    'id' => 11,
                                    'date' => '',
                                    'camera' => 1,
                                    'url' => 'asasssasss',
                                    'active' => false
                                ],
                                [
                                    'id' => 12,
                                    'date' => '',
                                    'camera' => 1,
                                    'url' => 'asasssasss',
                                    'active' => false
                                ],
                                [
                                    'id' => 13,
                                    'date' => '',
                                    'camera' => 1,
                                    'url' => 'asasssasss',
                                    'active' => true
                                ]
                            ]
                        ],
                        [
                            'seat' => 3,
                            'history' => []
                        ],
                        [
                            'seat' => 4,
                            'history' => []
                        ],
                    ]);


                    $persistenceActivate = $request->get('activate') ?? 3;
                    $persistenceRelease = $request->get('release') ?? 100;

                    $historic = collect((object)[]);

                    foreach ($vehicle->cameras as $vc) {
                        $h = $this->photoService->for($vehicle, $vc->camera, $persistenceActivate, $persistenceRelease, $date)->getHistoric(true)->where('drId', $dr->id);
                        $historic->push((object)[
                            'camera' => $vc->camera,
                            'profileSeating' => $vehicle->getProfileSeating($vc->camera, $date)->occupation,
                            'historic' => $h
                        ]);
                    }

                    $response->historic = $historic->sortByDesc('ts')->values();


                    $data = collect([]);

                    // Organizar datos conforme a $data

                    foreach ($response->historic as $h) {

                        foreach ($h->profileSeating as $ps) {
                            dd($ps);
                            $seat = $ps->number;

                            $dataH = collect([]);
                            foreach ($h->historic as $hh) {
                                $dataH->push([
                                    'id' => 11,
                                    'date' => '',
                                    'camera' => 1,
                                    'url' => 'asasssasss',
                                    'active' => collect($hh->details->occupation->seatingActivate)->contains($seat)
                                ]);
                            }

                            $data->push([
                                'seat' => $seat,
                                'history' => $dataH
                            ]);
                        }
                    }

                    dd($response->historic);
                } else {
                    $response->success = false;
                    $response->message = __('Dispatch register not found');
                }
                break;

            case 'guide-historic-seating-counts':
                $dr = DispatchRegister::find($request->get('dr'));

                if ($dr) {
                    $vehicle = $dr->vehicle;
                    $date = $dr->date;

                    $data = collect([
                        [
                            'seat' => 1,
                            'history' => [
                                [
                                    'id' => 11,
                                    'date' => '',
                                    'camera' => 1,
                                    'url' => 'asasssasss',
                                    'active' => true
                                ],
                                [
                                    'id' => 12,
                                    'date' => '',
                                    'camera' => 1,
                                    'url' => 'asasssasss',
                                    'active' => true
                                ],
                                [
                                    'id' => 13,
                                    'date' => '',
                                    'camera' => 1,
                                    'url' => 'asasssasss',
                                    'active' => true
                                ]
                            ]
                        ],
                        [
                            'seat' => 2,
                            'history' => [
                                [
                                    'id' => 11,
                                    'date' => '',
                                    'camera' => 1,
                                    'url' => 'asasssasss',
                                    'active' => false
                                ],
                                [
                                    'id' => 12,
                                    'date' => '',
                                    'camera' => 1,
                                    'url' => 'asasssasss',
                                    'active' => false
                                ],
                                [
                                    'id' => 13,
                                    'date' => '',
                                    'camera' => 1,
                                    'url' => 'asasssasss',
                                    'active' => true
                                ]
                            ]
                        ],
                        [
                            'seat' => 3,
                            'history' => []
                        ],
                        [
                            'seat' => 4,
                            'history' => []
                        ],
                    ]);


                    $persistenceActivate = $request->get('activate') ?? 3;
                    $persistenceRelease = $request->get('release') ?? 100;

                    $historic = collect((object)[]);

                    foreach ($vehicle->cameras as $vc) {
                        $h = $this->photoService->for($vehicle, $vc->camera, $persistenceActivate, $persistenceRelease, $date)->getHistoric(true)->where('drId', $dr->id);
                        $historic->push((object)[
                            'camera' => $vc->camera,
                            'profileSeating' => $vehicle->getProfileSeating($vc->camera, $date)->occupation,
                            'historic' => $h
                        ]);
                    }

                    $response->historic = $historic->sortByDesc('ts')->values();


                    $data = collect([]);

                    // Organizar datos conforme a $data

                    foreach ($response->historic as $h) {

                        foreach ($h->profileSeating as $ps) {
                            dd($ps);
                            $seat = $ps->number;

                            $dataH = collect([]);
                            foreach ($h->historic as $hh) {
                                $dataH->push([
                                    'id' => 11,
                                    'date' => '',
                                    'camera' => 1,
                                    'url' => 'asasssasss',
                                    'active' => collect($hh->details->occupation->seatingActivate)->contains($seat)
                                ]);
                            }

                            $data->push([
                                'seat' => $seat,
                                'history' => $dataH
                            ]);
                        }
                    }
                } else {
                    $response->success = false;
                    $response->message = __('Dispatch register not found');
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
                    'vehicles' => $company->userVehicles(),
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
                $camera = $request->get('camera') ?? 'all';

                $photo = null;
                if ($vehicle) {
                    $date = $request->get('date');
                    $photos = $this->photoService->for($vehicle, $camera, null, null, $date)->getHistoric();

                    if ($photos->count()) {
                        $photo = Photo::find($photos->last()->id);
                    }


//                    $photo = Photo::find(13314);
//                    $photo = Photo::find(77176);


//                        $photo->processRekognition(true, 'persons_and_faces');
//                        $photo->save();

                    if ($photo) {
                        $response->photo = $this->photoService->getPhotoData($photo, $photos);
                    }

                    $response->seating = $vehicle->getProfileSeating($camera, $date)->occupation;
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
                $camera = $request->get('camera') ?? 'all';
                if ($vehicle) {
                    $seating = collect($request->get('seating'));
                    $profileSeat = ProfileSeat::findByVehicleAndCamera($vehicle, $camera);
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
