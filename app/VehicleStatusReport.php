<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\VehicleStatusReport
 *
 * @property int $id
 * @property string $date
 * @property string $time
 * @property int|null $vehicle_id
 * @property int|null $vehicle_status_id
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $orientation
 * @property float|null $speed
 * @property int|null $odometer
 * @property string|null $frame
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleStatusReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleStatusReport whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleStatusReport whereFrame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleStatusReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleStatusReport whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleStatusReport whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleStatusReport whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleStatusReport whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleStatusReport whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleStatusReport whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleStatusReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleStatusReport whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleStatusReport whereVehicleStatusId($value)
 * @mixin \Eloquent
 */
class VehicleStatusReport extends Model
{
    public function status()
    {
        return $this->belongsTo(VehicleStatus::class,'vehicle_status_id','id_status');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
