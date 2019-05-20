<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\PeakAndPlate
 *
 * @property \Carbon\Carbon $date
 * @property int $id
 * @property int $week_day
 * @property int $vehicle_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PeakAndPlate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PeakAndPlate whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PeakAndPlate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PeakAndPlate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PeakAndPlate whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PeakAndPlate whereWeekDay($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PeakAndPlate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PeakAndPlate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PeakAndPlate query()
 */
class PeakAndPlate extends Model
{
    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    protected $fillable = ['date','week_day','vehicle_id'];

    protected $dates = ['date'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
