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
     * ConfigProfileService constructor.
     * @param Vehicle $vehicle
     */
    function __construct(Vehicle $vehicle)
    {
        $this->vehicle = $vehicle;
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
        $config = ConfigProfile::where('vehicle_id', $this->vehicle->id)->first();

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

        $profileSeating = $this->getProfileSeating();

//        foreach ($config as $type => &$configType) {
//
//        }

        $seatingConfig = [];
        foreach ($profileSeating->occupation as $seat) {
            $number = $seat['number'];

            $activate = self::THRESHOLD_ACTIVATE;
            $release = self::THRESHOLD_RELEASE;

//            if (collect([15, 18])->contains(intval($number))) {
//                $activate = 1;
//                $release = 2;
//            }

            $seatingConfig[$number] = [
                'persistence' => compact(['activate', 'release'])
            ];
        }

        $config['seating'] = $seatingConfig;

        return $config;
    }

    /**
     * @return ProfileSeat
     */
    function getProfileSeating()
    {
        return ProfileSeat::findByVehicle($this->vehicle);
    }
}