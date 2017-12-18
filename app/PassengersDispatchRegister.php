<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\PassengersDispatchRegister
 *
 * @property int $id
 * @property string|null $date
 * @property string|null $time
 * @property int|null $route_id
 * @property int|null $type_of_day
 * @property int|null $turn
 * @property string|null $round_trip
 * @property int|null $vehicle_id
 * @property int|null $dispatch_id
 * @property string|null $departure_time
 * @property string|null $arrival_time_scheduled
 * @property string|null $arrival_time_difference
 * @property string|null $arrival_time
 * @property bool|null $canceled
 * @property string|null $time_canceled
 * @property string|null $status
 * @property int|null $start_recorder
 * @property int|null $end_recorder
 * @property-read \App\Route|null $route
 * @property-read \App\Vehicle|null $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PassengersDispatchRegister active()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PassengersDispatchRegister whereArrivalTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PassengersDispatchRegister whereArrivalTimeDifference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PassengersDispatchRegister whereArrivalTimeScheduled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PassengersDispatchRegister whereCanceled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PassengersDispatchRegister whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PassengersDispatchRegister whereIn($column, $values, $boolean = 'and', $not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PassengersDispatchRegister whereDepartureTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PassengersDispatchRegister whereDispatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PassengersDispatchRegister whereEndRecorder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PassengersDispatchRegister whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PassengersDispatchRegister whereRoundTrip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PassengersDispatchRegister whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PassengersDispatchRegister whereStartRecorder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PassengersDispatchRegister whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PassengersDispatchRegister whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PassengersDispatchRegister whereTimeCanceled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PassengersDispatchRegister whereTurn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PassengersDispatchRegister whereTypeOfDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PassengersDispatchRegister whereVehicleId($value)
 * @mixin \Eloquent
 */
class PassengersDispatchRegister extends Model
{
    const IN_PROGRESS = "En camino";
    const COMPLETE = "Terminó";

    protected function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getParsedDate()
    {
        if ($this->date == null) dd($this->id, $this->date);
        return Carbon::createFromFormat(config('app.date_format'), $this->date);
    }

    public function dateLessThanDateNewOffRoadReport()
    {
        return $this->getParsedDate()->format('Y-m-d') < '2017-09-16';
    }

    public function scopeActive($query){
        return $query->where('status',$this::COMPLETE)->orWhere('status',$this::IN_PROGRESS);
    }

    public function complete()
    {
        return $this->status == $this::COMPLETE;
    }

    public function inProgress()
    {
        return $this->status == $this::IN_PROGRESS;
    }
}
