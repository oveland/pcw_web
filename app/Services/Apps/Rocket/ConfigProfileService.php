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

            $activate = $rActivate ? $rActivate : self::THRESHOLD_ACTIVATE;
            $release = $rRelease ? $rRelease : self::THRESHOLD_RELEASE;


            if (collect([22])->contains(intval($number))) {
                $activate = 1;
                $release = 3;
            }

            if (collect([2])->contains(intval($number))) {
                $activate = 5;
                $release = 5;
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

            $seatingConfig[$number] = [
                'persistence' => compact(['activate', 'release'])
            ];
        }

        $config['seating'] = $seatingConfig;
        $config['cameras'] = (object)[
            '1' => [
                'largeDetection' => false,
                'processMaxWidth' => 100,
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
