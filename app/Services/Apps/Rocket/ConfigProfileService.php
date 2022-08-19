<?php

namespace App\Services\Apps\Rocket;

use App\Models\Apps\Rocket\ConfigProfile;
use App\Models\Apps\Rocket\ProfileSeat;
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
     * ConfigProfileService constructor.
     * @param ProfileSeat $profileSeat
     */
    function __construct(ProfileSeat $profileSeat)
    {
        $this->profileSeat = $profileSeat;
        $this->vehicle = $this->profileSeat->vehicle;
        $this->configProfile = $this->vehicle->configProfile;
    }

    /**
     * @param $type
     * @return object
     */
    public function type($type)
    {
        $c = json_decode(json_encode($this->getConfigProfile()->type($type), JSON_FORCE_OBJECT), false);
        return $c;
    }

    /**
     * @return ConfigProfile
     */
    public function getConfigProfile()
    {
        $config = $this->configProfile;

        if (!$config) {
            $config = new ConfigProfile();
            $config->vehicle()->associate($this->vehicle);
            $config->config = $this->mergeWithProfileSeating();
            $config->save();
        } else {
            $config->config = $this->mergeWithProfileSeating();
        }

        return $config;
    }

    /**
     * @return array
     */
    private function mergeWithProfileSeating()
    {
        $config = config('rocket.' . $this->vehicle->company_id);

//        foreach ($config as $type => &$configType) {
//
//        }

        $seatingConfig = [];
        foreach ($this->profileSeat->occupation as $seat) {
            $number = $seat['number'];

            $rActivate = $this->profileSeat->persistence['activate'];
            $rRelease = $this->profileSeat->persistence['release'];

            $activate = $rActivate ?: self::THRESHOLD_ACTIVATE;
            $release = $rRelease ?: self::THRESHOLD_RELEASE;

            if ($this->vehicle->id == 1873 && false) {
                if ($this->profileSeat->camera == '1') {
                    $activate = 3;
                    $release = 4;
                } else if ($this->profileSeat->camera == '2') {
                    $activate = 2;
                    $release = 10;
                } else if ($this->profileSeat->camera == '3') {
                    $activate = 2;
                    $release = 10;
                }

                if (collect([5])->contains(intval($number))) { // Asientos con poca cobertura
                    $activate = 1;
                    $release = 15;
                }

                if (collect([13, 16])->contains(intval($number))) { // Asientos con poca cobertura
                    $activate = 2;
                    $release = 15;
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
        /*    if ($this->vehicle->id == 2357) {
                $persistenceRoutes = [
                    279 => ['a' => 3, 'r' => 20],
                    280 => ['a' => 3, 'r' => 20],

                    285 => ['a' => 10, 'r' => 80],
                    286 => ['a' => 10, 'r' => 80],
                    287 => ['a' => 10, 'r' => 80],
                    288 => ['a' => 10, 'r' => 80],
                ];
            }*/

            $seatingConfig[$number] = [
                'persistence' => compact(['activate', 'release']),
                'persistenceRoutes' => $persistenceRoutes
            ];
        }

        $config['seating'] = $seatingConfig;
        $config['cameras'] = (object)[
            '1' => [
                'largeDetection' => true,
                'processMaxWidth' => 40,
            ],
            '2' => [
                'largeDetection' => false,
                'processMaxWidth' => 30,
            ],
            '3' => [
                'largeDetection' => false,
                'processMaxWidth' => 30
            ],
        ];

        return $config;
    }
}
