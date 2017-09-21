<?php

namespace App\Models\Passengers;

use App\Company;
use App\Vehicle;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Passengers\RecorderCounterPerDays
 *
 * @property string|null $date
 * @property int|null $vehicle_id
 * @property int|null $company_id
 * @property string|null $number
 * @property int|null $start_recorder_current
 * @property int|null $start_recorder
 * @property string|null $prev_date_end_recorder
 * @property int|null $end_recorder
 * @property int|null $passengers_current
 * @property int|null $passengers
 * @property-read \App\Company|null $company
 * @property-read \App\Vehicle|null $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDays whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDays whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDays whereEndRecorder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDays whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDays wherePassengers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDays wherePassengersCurrent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDays wherePrevDateEndRecorder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDays whereStartRecorder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDays whereStartRecorderCurrent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDays whereVehicleId($value)
 * @mixin \Eloquent
 * @property int|null $start_recorder_prev
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerDays whereStartRecorderPrev($value)
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
}
