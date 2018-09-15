<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CurrentLocation
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocation whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocation whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocation whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocation whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocation whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocation whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocation whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocation whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocation whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocation whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocation whereReferenceLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocation whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocation whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocation whereVehicleStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocation whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocation whereVehicle($vehicle)
 * @mixin \Eloquent
 * @property-read \App\CurrentDispatchRegister|null $dispatchRegister
 * @property-read \App\Vehicle|null $vehicle
 * @property float|null $yesterday_odometer
 * @property float|null $current_mileage
 * @property-read \App\VehicleStatus|null $vehicleStatus
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocation whereCurrentMileage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocation whereYesterdayOdometer($value)
 */
class CurrentLocation extends Model
{
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
