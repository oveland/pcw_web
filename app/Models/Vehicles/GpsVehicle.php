<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\GpsVehicle
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $vehicle_id
 * @property string $imei
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GpsVehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GpsVehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GpsVehicle whereImei($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GpsVehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GpsVehicle whereVehicleId($value)
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 * @property int|null $gps_type_id
 * @property int|null $report_period
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GpsVehicle whereGpsTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GpsVehicle whereReportPeriod($value)
 */
class GpsVehicle extends Model
{
    protected $fillable = ['imei'];

    protected function getDateFormat()
    {
        return config('app.date_time_format');
    }

    public function hasValidImei()
    {
        return strlen($this->imei) == 15;
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
