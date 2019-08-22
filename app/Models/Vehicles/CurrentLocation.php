<?php

namespace App\Models\Vehicles;

use App\Models\Routes\CurrentDispatchRegister;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\CurrentLocation
 *
 * @property int $id
 * @property int|null $version
 * @property string|null $date
 * @property string $date_created
 * @property int|null $dispatch_register_id
 * @property float|null $distance
 * @property string $last_updated
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $odometer
 * @property bool|null $off_road
 * @property float|null $orientation
 * @property int|null $reference_location_id
 * @property int|null $speed
 * @property string|null $status
 * @property int|null $vehicle_id
 * @property int|null $vehicle_status_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereReferenceLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereVehicleStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereVehicle($vehicle)
 * @mixin \Eloquent
 * @property-read \App\Models\Routes\CurrentDispatchRegister|null $dispatchRegister
 * @property-read \App\Models\Vehicles\Vehicle|null $vehicle
 * @property float|null $yesterday_odometer
 * @property float|null $current_mileage
 * @property-read \App\Models\Vehicles\VehicleStatus|null $vehicleStatus
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereCurrentMileage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereYesterdayOdometer($value)
 * @property bool|null $speeding
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereSpeeding($value)
 * @property int|null $location_id
 * @property string|null $ard_off_road
 * @property int|null $jumps
 * @property int|null $total_locations
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereArdOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereJumps($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereTotalLocations($value)
 */
class CurrentLocation extends Model
{
    protected $dates = ['date'];

    protected function getDateFormat()
    {
        return config('app.date_time_format');
    }

    public function getDateAttribute($date)
    {
        return Carbon::createFromFormat(config('app.simple_date_time_format'),explode('.',$date)[0]);
    }

    public function dispatchRegister()
    {
        return $this->belongsTo(CurrentDispatchRegister::class, 'dispatch_register_id', 'dispatch_register_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getAPIFields()
    {
        return (object)[
            'id' => $this->id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'orientation' => $this->orientation,
            'current_mileage' => number_format($this->current_mileage/1000, 2, ',', '.'),
            'speed' => $this->speed,
        ];
    }

    public function vehicleStatus()
    {
        return $this->belongsTo(VehicleStatus::class, 'vehicle_status_id', 'id_status');
    }

    public function scopeWhereVehicle($query, Vehicle $vehicle)
    {
        return $query->where('vehicle_id', $vehicle->id)->get()->first();
    }
}
