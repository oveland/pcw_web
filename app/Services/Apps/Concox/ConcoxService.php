<?php

namespace App\Services\Apps\Concox;

use App\Models\Apps\Rocket\Photo;
use App\Models\Vehicles\GpsVehicle;
use App\Models\Vehicles\Vehicle;
use App\Services\API\Apps\Contracts\APIFilesInterface;
use App\Services\Apps\Rocket\Photos\PhotoService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Image;

class ConcoxService
{
    /**
     * @var AuthService
     */
    private $auth;

    /**
     * ConcoxService constructor.
     */
    public function __construct()
    {
        $this->auth = new AuthService();
    }

    /**
     * @param string $camera
     * @return Collection
     */
    public function takePhoto($camera = '1')
    {
        $response = collect([]);
        $accessToken = $this->auth->getAccessToken();

        if ($accessToken) {
            $this->auth->setPrivateParams([
                'method' => 'jimi.device.meida.cmd.send',
                'access_token' => $accessToken->access_token,
                'imei' => '351777095427025',
                'camera' => $camera,
                'mediaType' => '1',
            ]);

            $request = $this->auth->request();

            if ($request->get('code') == 0) {
                $response = collect($request->get('result'));
            }
        }

        return $response;
    }

    /**
     * @param string $camera
     * @param int $minutesAgo
     * @return Collection
     */
    public function getPhoto($camera = '1', $minutesAgo = 2)
    {
        $photos = collect([]);
        $accessToken = $this->auth->getAccessToken();

        if ($accessToken) {
            $this->auth->setPrivateParams([
                'method' => 'jimi.device.media.URL',
                'access_token' => $accessToken->access_token,
                'imei' => '351777095427025',
                'camera' => $camera,
                'media_type' => '1',
                'page_no' => 0,
                'page_size' => 2,
                'start_time' => Carbon::now('UTC')->subMinutes($minutesAgo)->toDateTimeString(),
                'end_time' => Carbon::now('UTC')->toDateTimeString()
            ]);

            $request = $this->auth->request();

            if ($request->get('code') == 0) {
                $photos = collect($request->get('result'))->sortByDesc('create_time');
            }
        }

        return $photos;
    }

    /**
     * @return Collection
     */
    public function getLiveStreamVideo()
    {
        $response = collect([]);
        $accessToken = $this->auth->getAccessToken();

        if ($accessToken) {
            $this->auth->setPrivateParams([
                'method' => 'jimi.device.live.page.url',
                'access_token' => $accessToken->access_token,
                'imei' => '351777095427025'
            ]);

            $request = $this->auth->request();

            if ($request->get('code') == 0) {
                $response = collect($request->get('result'));
            }
        }

        return $response;
    }

    /**
     * @return Collection
     */
    public function getCommandSupportList()
    {
        $response = collect([]);
        $accessToken = $this->auth->getAccessToken();

        if ($accessToken) {
            $this->auth->setPrivateParams([
                'method' => 'jimi.open.instruction.list',
                'access_token' => $accessToken->access_token,
                'imei' => '351777095427025'
            ]);

            $request = $this->auth->request();

            if ($request->get('code') == 0) {
                $response = collect($request->get('result'));
            }
        }

        return $response;
    }

    /**
     *
     */
    public function syncPhotos()
    {
        $response = collect([
            'success' => true,
            'message' => '',
            'data' => []
        ]);

        $photoService = new PhotoService();
        $photos = $this->getPhoto(1, 2);

        $messages = collect([]);
        foreach ($photos as $photo) {
            $fileUrl = $photo->file_URL;
            $fileUrlData = explode("/", $fileUrl);
            $photoData = explode("_", collect($fileUrlData)->get(3));

            $imei = collect($photoData)->get(0);
            $photoUId = collect($photoData)->get(1);

            $checkPhoto = Photo::whereUid($photoUId)->first();

            if (!$checkPhoto || true) {
                $gpsVehicle = GpsVehicle::findByImei($imei)->first();
                if ($gpsVehicle || true) {


//                    $vehicle = $gpsVehicle->vehicle; // TODO: implement logic for associate Imei Concox with vehicle
                    $vehicle = Vehicle::find(2150);
                    dd($vehicle);

                    $year = collect($photoData)->get(2);
                    $month = collect($photoData)->get(3);
                    $day = collect($photoData)->get(4);
                    $hour = collect($photoData)->get(5);
                    $minutes = collect($photoData)->get(6);
                    $seconds = collect($photoData)->get(7);

                    $photoDate = Carbon::createFromFormat('YmdHis', "$year$month$day$hour$minutes$seconds");

                    $image = Image::make($fileUrl)
                        ->rotate(180)
                        ->encode('data-url');

                    $data = [
                        'date' => $photoDate->toDateTimeString(),
                        'img' => $image,
                        'type' => 'concox',
                        'side' => $photo->camera,
                        'uid' => $photoUId
                    ];

                    $photoService->for($vehicle);
                    $photoService->saveImageData($data);
                } else {
                    $response->put('success', false);
                    $response->put('message', "One or more photos haven't a vehicle associated with your imei");
                    $messages->push("Imei $imei of photo $photoUId is not associate with a vehicle!");
                }
            }
        }

        $response->put('data', $messages);

        return $response;
    }
}
