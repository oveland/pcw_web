<?php

namespace App\Models\Vehicles;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\MaintenanceVehicle
 *
 * @property \Carbon\Carbon $date
 * @property int $id
 * @property int $week_day
 * @property int $vehicle_id
 * @property string $observations
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\MaintenanceVehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\MaintenanceVehicle whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\MaintenanceVehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\MaintenanceVehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\MaintenanceVehicle whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\MaintenanceVehicle whereWeekDay($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\MaintenanceVehicle whereObservations($value)
 */
class MaintenanceVehicle extends Model
{
    protected function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    protected $fillable = ['date','week_day','vehicle_id'];

    public function getDateAttribute($date)
    {
        return Carbon::createFromFormat(config('app.date_format'), explode('.', $date)[0])->toDateString();
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
