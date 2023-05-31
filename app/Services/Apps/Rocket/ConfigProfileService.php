<?php

namespace App\Services\Apps\Rocket;

use App\Models\Apps\Rocket\ConfigProfile;
use App\Models\Apps\Rocket\ProfileSeat;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;

class ConfigProfileService
{
    protected const THRESHOLD_ACTIVATE = 2;
    protected const THRESHOLD_RELEASE = 2;

    /**
     * @var Vehicle
     */
    private $vehicle;

    /**
     * @var ProfileSeat
     */
    private $profileSeat;

    /**
     * @var ConfigProfile
     */
    private $configProfile;

    /**
     * @param ProfileSeat $profileSeat
     * @param ConfigProfile $configProfile
     */
    function __construct(ProfileSeat $profileSeat, ConfigProfile $configProfile = null)
    {
        $this->profileSeat = $profileSeat;
        $this->vehicle = $this->profileSeat->vehicle;
        $this->configProfile = $configProfile;
    }

    /**
     * @param $type
     * @return object
     */
    function type($type)
    {
        $c = json_decode(json_encode($this->getConfigProfile()->type($type), JSON_FORCE_OBJECT), false);
        return $c;
    }

    /**
     * @return ConfigProfile
     */
    function getConfigProfile()
    {
//        $this->configProfile->config = $this->buildConfigProfile($this->profileSeat->camera);
        return $this->configProfile;
    }

    /**
     * @return array
     */
    function buildConfigProfile($camera)
    {
        $config = config('rocket.' . $this->vehicle->company_id);

        $seatingConfig = [];
        foreach ($this->profileSeat->occupation as $seat) {
            $number = $seat['number'];

            $rActivate = $this->profileSeat->persistence['activate'];
            $rRelease = $this->profileSeat->persistence['release'];

            $activate = $rActivate ?: self::THRESHOLD_ACTIVATE;
            $release = $rRelease ?: self::THRESHOLD_RELEASE;

            if ($this->vehicle->id == 2614) {
                if ($camera == '3') {
                    $activate = 3;
                    $release = 4;
                }
                if (collect([20])->contains(intval($number))) { // Asientos con poca cobertura
                    $activate = 2;
                    $release = 48;
                }
            }
           
           // dd($vehicleRoute);

            if ($this->vehicle->id == 2563) { // VH 5015 valledupar   OJO!!!!!!
                if (collect([1, 7, 15])->contains(intval($number))) { // Asientos con poca cobertura
                    $activate = 1;
                    $release = 40;
                }
            }
            if ($this->vehicle->id == 2576) { // VH 5013 valledupar   OJO!!!!!!
                if (collect([1])->contains(intval($number))) { // Asientos con poca cobertura
                    $activate = 1;
                    $release = 40;
                }
            }
            if ($this->vehicle->id == 2574) { // VH 5020 valledupar   OJO!!!!!!
                if (collect([2])->contains(intval($number))) { // Asientos con poca cobertura
                    $activate = 1;
                    $release = 40;
                }
            }

            if ($this->vehicle->id == 2582) { // VH 62 Armenia   OJO!!!!!!
                if (collect([15, 16, 17, 18])->contains(intval($number))) { // Asientos con poca cobertura
                    $activate = 1;
                    $release = 20;
                }
            }

            if ($this->vehicle->id == 2601) { // VH 041 IBAGUE   OJO!!!!!!
                if (collect([1])->contains(intval($number))) { // Asientos con poca cobertura
                    $activate = 1;
                    $release = 20;
                }
            }
            if ($this->vehicle->id == 2556) { // VH  5000 valledupar   OJO!!!!!!
                if (collect([1, 2])->contains(intval($number))) { // Asientos con poca cobertura
                    $activate = 1;
                    $release = 8;

                }
            }


//            if (collect([9, 10, 11, 12, 13, 14, 19, 20, 21,22, 23])->contains(intval($number))) {
//                $activate = $rActivate ? $rActivate : 5;
//                $release = $rActivate ? $rActivate : 5;
//            }
//
//            if (collect([15, 16, 17, 18, ])->contains(intval($number))) {
//                $activate = $rActivate ? $rActivate : 1;
//                $release = $rActivate ? $rActivate : 3;
//            }
            $persistenceRoutes = null;
               /* if ($this->vehicle->company_id == 39) {
                    $persistenceRoutes = [
                        279 => ['a' => 2, 'r' => 20],
                        280 => ['a' => 2, 'r' => 20],

                        285 => ['a' => 2, 'r' => 20],
                        286 => ['a' => 2, 'r' => 20],

                        282 => ['a' => 2, 'r' => 20],
                        283 => ['a' => 2, 'r' => 20],

                    ];
                }*/

            $seatingConfig[$number] = [
                'persistence' => compact(['activate', 'release']),
                'persistenceRoutes' => $persistenceRoutes
            ];
        }

        $config['seating'] = $seatingConfig;

        $cameraConfig = [
            'processWidthSize' => [
                'faces' => [
                    'maxWidth' => 0.5 * collect($this->profileSeat->occupation)->average('width'),
                    'minWidth' => 0.1 * collect($this->profileSeat->occupation)->average('width'),
                ],
                'persons' => [
                    'maxWidth' => 1.8 * collect($this->profileSeat->occupation)->average('width'),
                    'minWidth' => 0.5 * collect($this->profileSeat->occupation)->average('width'),
                ]
            ],
//            'processWidthSize' => [
//                'faces' => [
//                    'maxWidth' => 2 * collect($this->profileSeat->occupation)->average('width'),
//                    'minWidth' => 0 * collect($this->profileSeat->occupation)->average('width'),
//                ],
//                'persons' => [
//                    'maxWidth' => 2 * collect($this->profileSeat->occupation)->average('width'),
//                    'minWidth' => 0 * collect($this->profileSeat->occupation)->average('width'),
//                ]
//            ],
            'largeDetection' => true
        ];

        $config['camera'] = $cameraConfig;

        return $config;
    }
}
