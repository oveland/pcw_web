<?php

namespace App\Models\Vehicles;

use App\Models\Passengers\Passenger;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\Report;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Vehicles\Speeding
 *
 * @property-read AddressLocation $addressLocation
 * @property-read DispatchRegister $dispatchRegister
 * @property-read mixed $date
 * @property-read mixed $speed
 * @property-read mixed $time
 * @property-read Report $report
 * @property-read Vehicle $vehicle
 * @property-read VehicleStatus $vehicleStatus
 * @method static Builder|Location forDate($withDate)
 * @method static Builder|Location validCoordinates()
 * @method static Builder|Location witOffRoads()
 * @method static Builder|Location withSpeeding()
 * @mixin Eloquent
 * @property-read Passenger $passenger
 * @property-read PhotoLocation $photo
 */
class Speeding extends Location
{
    protected $table = 'overspeed';

    public function toArray()
    {
        return [
            'id' => $this->id,
            'date' => $this->date->toDateTimeString(),
            'speed' => $this->speed,
            'dispatchRegister' => $this->dispatchRegister ? $this->dispatchRegister->getRouteFields(true) : null,
            'vehicle' => $this->vehicle->getAPIFields(null, true)
        ];
    }
}
