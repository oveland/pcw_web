<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\GpsVehicle
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $vehicle_id
 * @property string $imei
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GpsVehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GpsVehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GpsVehicle whereImei($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GpsVehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GpsVehicle whereVehicleId($value)
 */
class GpsVehicle extends Model
{
    protected function getDateFormat()
    {
        return config('app.date_time_format');
    }
}
