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
     * @param Vehicle $vehicle
     */
    function __construct(Vehicle $vehicle)
    {
        $this->vehicle = $vehicle;
        $this->profileSeat = $vehicle->profile_seating;
        $this->configProfile = $vehicle->configProfile;
    }

    /**
     * @param $type
     * @return object
     */
    public function type($type)
    {
        $c = json_decode(json_encode($this->get()->type($type), JSON_FORCE_OBJECT), false);
        return $c;
    }

    /**
     * @return ConfigProfile
     */
    public function get()
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

            $rActivate = request()->get('activate');
            $rRelease = request()->get('release');

            $activate = $rActivate ? $rActivate : self::THRESHOLD_ACTIVATE;
            $release = $rRelease ? $rRelease : self::THRESHOLD_RELEASE;

            if (collect([1, 2, 3, 4, 5, 6, 7, 8])->contains(intval($number))) {
                $activate = 4;
                $release = 1;
            }

            if (collect([9, 10, 11, 12, 13, 14, 19, 20, 21,22, 23])->contains(intval($number))) {
                $activate = 5;
                $release = 5;
            }

            if (collect([15, 16, 17, 18, ])->contains(intval($number))) {
                $activate = 1;
                $release = 3;
            }

            $seatingConfig[$number] = [
                'persistence' => compact(['activate', 'release'])
            ];
        }

        $config['seating'] = $seatingConfig;

        return $config;
    }
}