<?php

namespace App\Models\Passengers;

use App\Company;
use App\Vehicle;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Passengers\RecorderCounterPerDays
 *
 * @property int|null $dispatch_register_id
 * @property string|null $date
 * @property int|null $vehicle_id
 * @property int|null $company_id
 * @property string|null $number
 * @property int|null $start_recorder
 * @property int|null $start_recorder_prev
 * @property string|null $date_start_recorder_prev
 * @property int|null $end_recorder
 * @property int|null $passengers
 * @property-read \App\Company|null $company
 * @property-read \App\Vehicle|null $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDays whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDays whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDays whereDateStartRecorderPrev($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDays whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDays whereEndRecorder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDays whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDays wherePassengers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDays whereStartRecorder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDays whereStartRecorderPrev($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDays whereVehicleId($value)
 * @mixin \Eloquent
 */
class RecorderCounterPerDays extends Model
{
    protected function getDateFormat()
    {
        return config('app.date_time_format');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getStartRecorder()
    {
        return $this->start_recorder == 0 ? $this->start_recorder_prev : $this->start_recorder;
    }

    public function getComputedPassengers()
    {
        return ($this->end_recorder - $this->getStartRecorder());
    }
}
