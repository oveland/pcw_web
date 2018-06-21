<?php

namespace App;

use App\Http\Controllers\Utils\Database;
use App\Http\Controllers\Utils\StrTime;
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
 * @property int|null $user_id
 * @property-read \App\Models\Passengers\RecorderCounterPerDay|null $recorderCounter
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\LocationReport[] $locationReports
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Location[] $locations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\OffRoad[] $offRoads
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Report[] $reports
 * @property-read \App\Route|null $route
 * @property-read \App\Vehicle|null $vehicle
 * @property-read \App\User|null $user
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
 * @property mixed $departure_fringe
 * @property mixed $arrival_fringe
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister active()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereDriverCode($value)
 * @property-read mixed $passengers
 * @property-read \App\Driver|null $driver
 * @property int|null $departure_fringe_id
 * @property int|null $arrival_fringe_id
 * @property-read \App\Fringe|null $arrivalFringe
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ControlPointTimeReport[] $controlPointTimeReports
 * @property-read \App\Fringe|null $departureFringe
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ParkingReport[] $parkingReport
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Speeding[] $speedingReport
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereArrivalFringeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister whereDepartureFringeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatchRegister completed()
 */
class DispatchRegister extends Model
{
    const IN_PROGRESS = "En camino";
    const COMPLETE = "Terminó";

    protected function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    public function getDepartureTimeAttribute($departureTime)
    {
        return StrTime::toString($departureTime);
    }

    public function getArrivalTimeAttribute($arrivalTime)
    {
        return StrTime::toString($arrivalTime);
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'dispatch_register_id', 'id')->orderBy('date', 'asc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function locationReports()
    {
        $stringClassLocationReport = Database::findLocationReportModelStringByDate($this->getParsedDate());
        return $this->hasMany($stringClassLocationReport, 'dispatch_register_id', 'id')->orderBy('date', 'asc');
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

    public function scopeCompleted($query)
    {
        return $query->where('status', $this::COMPLETE);
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

    public function departureFringe()
    {
        return $this->belongsTo(Fringe::class, 'departure_fringe_id', 'id');
    }

    public function arrivalFringe()
    {
        return $this->belongsTo(Fringe::class, 'arrival_fringe_id', 'id');
    }

    public function getStatusString()
    {
        return explode('.', $this->status)[0];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
