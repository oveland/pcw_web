<?php

namespace App\Models\Vehicles;

/**
 * App\Models\Vehicles\Speeding
 *
 * @property-read \App\Models\Vehicles\AddressLocation $addressLocation
 * @property-read \App\Models\Routes\DispatchRegister $dispatchRegister
 * @property-read mixed $date
 * @property-read mixed $speed
 * @property-read mixed $time
 * @property-read \App\Models\Routes\Report $report
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 * @property-read \App\Models\Vehicles\VehicleStatus $vehicleStatus
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location forDate($withDate)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location validCoordinates()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location witOffRoads()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location withSpeeding()
 * @mixin \Eloquent
 */
class Speeding extends Location
{
    protected $table = 'overspeed';
}
