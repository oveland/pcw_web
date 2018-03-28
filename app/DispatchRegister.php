<?php

namespace App;

use App\Http\Controllers\Utils\StrTime;
use App\Models\Passengers\RecorderCounterPerDay;
use App\Models\Passengers\RecorderCounterPerRoundTrip;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * App\DispatchRegister
 *
 * @property int $id
 * @property string|null $date
 * @property string|null $time
 * @property int|null $route_id
 * @property int|null $type_of_day
 * @property int|null $turn
 * @property int|null $round_trip
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
 * @property-read \App\Models\Passengers\RecorderCounterPerDay|null $recorderCounter
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\LocationReport[] $locationReports
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Location[] $locations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\OffRoad[] $offRoads
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Report[] $reports
 * @property-read \App\Route|null $route
 * @property-read \App\Vehicle|null $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereArrivalTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereArrivalTimeDifference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereArrivalTimeScheduled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereCanceled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereDepartureTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereDispatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereEndRecorder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereRoundTrip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereStartRecorder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereTimeCanceled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereTurn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereTypeOfDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereVehicleId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Passengers\RecorderCounterPerRoundTrip $recorderCounterPerRoundTrip
 * @property string|null $driver_code
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister active()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereDriverCode($value)
 * @property-read mixed $passengers
 * @property-read \App\Driver|null $driver
 */
class DispatchRegister extends Model
{
    const IN_PROGRESS = "En camino";
    const COMPLETE = "Terminó";

    protected function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reports()
    {
        return $this->hasMany(Report::class, 'dispatch_register_id', 'id')->orderBy('date', 'asc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function locationReports()
    {
        $numberSegments = config('maintenance.number_segments');
        $daysPerSegment = config('maintenance.day_per_segment');

        $intervals = collect(range(1, $numberSegments));
        $diff = Carbon::now()->diff(Carbon::parse($this->getParsedDate()))->days;

        if ($diff == 0) {
            $classLocationReport = "\CurrentLocationReport";
        } else {
            $segmentTarget = $intervals->filter(function ($value, $key) use ($diff, $daysPerSegment) {
                return $value * $daysPerSegment > $diff;
            })->first();
            $classLocationReport = "\LocationReport" . ($segmentTarget ? "$segmentTarget" : "");
        }

        return $this->hasMany(__NAMESPACE__ . "$classLocationReport", 'dispatch_register_id', 'id')->orderBy('date', 'asc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function locations()
    {
        return $this->hasMany(Location::class, 'dispatch_register_id', 'id')->orderBy('date', 'asc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function offRoads()
    {
        return $this->hasMany(OffRoad::class, 'dispatch_register_id', 'id')->orderBy('date', 'asc');
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

    public function recorderCounterPerRoundTrip()
    {
        return $this->hasOne(RecorderCounterPerRoundTrip::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_code', 'code');
    }

    public function scopeActive($query)
    {
        return $query->where(function ($query) {
            $query->where('status', $this::COMPLETE)->orWhere('status', $this::IN_PROGRESS);
        });
        //return $query->where('status', $this::COMPLETE)->orWhere('status', $this::IN_PROGRESS);
    }

    public function complete()
    {
        return $this->status == $this::COMPLETE;
    }

    public function inProgress()
    {
        return $this->status == $this::IN_PROGRESS;
    }

    public function speedingReport()
    {
        return $this->hasMany(Speeding::class)->orderBy('date', 'asc');
    }

    public function parkingReport()
    {
        return $this->hasMany(ParkingReport::class)->orderBy('date', 'asc');
    }

    public function controlPointTimeReports()
    {
        return $this->hasMany(ControlPointTimeReport::class)->orderBy('date', 'asc');
    }

    public function getRouteTime()
    {
        return $this->complete() ? StrTime::subStrTime($this->arrival_time, $this->departure_time) : '';
    }

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
