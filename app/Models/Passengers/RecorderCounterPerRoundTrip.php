<?php

namespace App\Models\Passengers;

use App\Company;
use App\Vehicle;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Passengers\RecorderCounterPerRoundTrip
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerRoundTrip whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerRoundTrip whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerRoundTrip whereDateStartRecorderPrev($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerRoundTrip whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerRoundTrip whereEndRecorder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerRoundTrip whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerRoundTrip wherePassengers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerRoundTrip whereStartRecorder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerRoundTrip whereStartRecorderPrev($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerRoundTrip whereVehicleId($value)
 * @mixin \Eloquent
 * @property int|null $route_id
 * @property int|null $end_recorder_prev
 * @property int|null $passengers_round_trip
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerRoundTrip whereEndRecorderPrev($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerRoundTrip wherePassengersRoundTrip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\RecorderCounterPerRoundTrip whereRouteId($value)
 */
class RecorderCounterPerRoundTrip extends Model
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
        return $this->start_recorder;
    }

    public function getComputedPassengers()
    {
        return ($this->end_recorder - $this->getStartRecorder());
    }
}
