<?php

namespace App\Models\Vehicles;

/**
 * App\Models\Vehicles\Speeding
 *
 * @property int $id
 * @property int $version
 * @property string|null $date
 * @property \Carbon\Carbon $date_created
 * @property int|null $dispatch_register_id
 * @property float|null $distance
 * @property \Carbon\Carbon $last_updated
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $odometer
 * @property float|null $orientation
 * @property float|null $speed
 * @property string|null $status
 * @property int|null $vehicle_id
 * @property bool|null $off_road
 * @property int|null $vehicle_status_id
 * @property bool|null $speeding
 * @property float|null $current_mileage
 * @property string|null $ard_off_road
 * @property-read mixed $time
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location forDate($withDate)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location validCoordinates()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\LocationToday whereArdOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\LocationToday whereCurrentMileage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\LocationToday whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\LocationToday whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\LocationToday whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\LocationToday whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\LocationToday whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\LocationToday whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\LocationToday whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\LocationToday whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\LocationToday whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\LocationToday whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\LocationToday whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\LocationToday whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\LocationToday whereSpeeding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\LocationToday whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\LocationToday whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\LocationToday whereVehicleStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\LocationToday whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location witOffRoads()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location withSpeeding()
 * @mixin \Eloquent
 * @property-read \App\Models\Vehicles\AddressLocation $addressLocation
 * @property-read \App\Models\Routes\DispatchRegister|null $dispatchRegister
 * @property-read \App\Models\Routes\Report $report
 * @property-read \App\Models\Vehicles\Vehicle|null $vehicle
 * @property-read \App\Models\Vehicles\VehicleStatus|null $vehicleStatus
 */
class LocationToday extends Location
{
    protected $table = 'locations';
}
