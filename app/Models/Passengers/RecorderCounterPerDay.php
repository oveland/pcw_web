<?php

namespace App\Models\Passengers;

use App\Company;
use App\Vehicle;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Passengers\RecorderCounterPerDay
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDay whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDay whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDay whereDateStartRecorderPrev($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDay whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDay whereEndRecorder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDay whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDay wherePassengers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDay whereStartRecorder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDay whereStartRecorderPrev($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDay whereVehicleId($value)
 * @mixin \Eloquent
 */
class RecorderCounterPerDay extends Model
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
