<?php

namespace App\Services\Apps\Rocket;

use App\Events\App\Rocket\AppEvent;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use FontLib\TrueType\Collection;

class RocketService
{
    /**
     * @param Vehicle $vehicle
     * @return RocketService
     */
    function for(Vehicle $vehicle)
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    /**
     * Sends Rocket App command via Web Sockets connection
     * @param array | Collection $params
     * @return object
     */
    function command($params)
    {
        $params = collect($params);

        event(new AppEvent($this->vehicle, $params->toArray()));

        return (object)[
            'success' => true,
            'message' => "Rocket App command has been requested to vehicle " . $this->vehicle->number,
            'params' => (object)$params->put('date', Carbon::now()->toDateTimeString())->toArray()
        ];
    }
}