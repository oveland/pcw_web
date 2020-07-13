<?php

namespace App\Models\Routes;

use App\Http\Controllers\Utils\StrTime;
use App\Models\Company\Company;
use App\Models\Drivers\Driver;
use App\Models\Passengers\CurrentSensorPassengers;
use App\Models\Users\User;
use App\Models\Vehicles\Location;
use App\Models\Vehicles\ParkingReport;
use App\Models\Vehicles\Speeding;
use App\Models\Vehicles\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * App\Models\Routes\DispatchRegister
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vehicles\Location[] $locations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vehicles\Location[] $offRoads
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Routes\Report[] $reports
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Routes\Route|null $route
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vehicles\Vehicle|null $vehicle
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Users\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereArrivalTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereArrivalTimeDifference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereArrivalTimeScheduled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereCanceled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereDepartureTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereDispatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereEndRecorder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereRoundTrip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereStartRecorder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereTimeCanceled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereTurn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereTypeOfDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereVehicleId($value)
 * @mixin \Eloquent
 * @property string|null $driver_code
 * @property mixed $departure_fringe
 * @property mixed $arrival_fringe
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister active()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereDriverCode($value)
 * @property-read mixed $passengers
 * @property-read \App\Models\Drivers\Driver|null $driver
 * @property int|null $departure_fringe_id
 * @property int|null $arrival_fringe_id
 * @property-read \App\Models\Routes\Fringe|null $arrivalFringe
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Routes\ControlPointTimeReport[] $controlPointTimeReports
 * @property-read \App\Models\Routes\Fringe|null $departureFringe
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vehicles\ParkingReport[] $parkingReport
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vehicles\Speeding[] $speedingReport
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereArrivalFringeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereDepartureFringeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister completed()
 * @property int|null $available_vehicles
 * @property int|null $initial_sensor_counter
 * @property string|null $initial_frame_sensor_counter
 * @property int|null $initial_sensor_recorder
 * @property int|null $final_sensor_counter
 * @property string|null $final_frame_sensor_counter
 * @property string|null $initial_counter_obs
 * @property string|null $final_counter_obs
 * @property int|null $final_sensor_recorder
 * @property int|null passengersBySensor
 * @property int|null passengersBySensorRecorder
 * @property int|null calculateErrorPercent
 * @property string|null displayInitialObservationsCounter
 * @property string|null displayFinalObservationsCounter
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereAvailableVehicles($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereFinalFrameSensorCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereFinalSensorCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereFinalSensorRecorder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereInitialFrameSensorCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereInitialSensorCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereInitialSensorRecorder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereUserId($value)
 * @property string|null $initial_time_sensor_counter
 * @property string|null $final_time_sensor_counter
 * @property int|null $initial_front_sensor_counter
 * @property int|null $initial_back_sensor_counter
 * @property int|null $final_front_sensor_counter
 * @property int|null $final_back_sensor_counter
 * @property-read mixed $passengers_by_sensor
 * @property-read mixed $passengers_by_sensor_recorder
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereFinalBackSensorCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereFinalFrontSensorCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereFinalTimeSensorCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereInitialBackSensorCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereInitialFrontSensorCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereInitialTimeSensorCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister findAllByDateAndVehicleAndRoute($date, $vehicleId, $routeId)
 * @property-read mixed $final_passengers_by_sensor_recorder
 * @property-read mixed $initial_passengers_by_sensor_recorder
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereFinalCounterObs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereInitialCounterObs($value)
 * @property float|null $start_odometer
 * @property float|null $end_odometer
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereEndOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereStartOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereCompanyAndRouteId(\App\Models\Company\Company $company, $routeId = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereCompanyAndDateAndRouteIdAndVehicleId(\App\Models\Company\Company $company, $date, $routeId = null, $vehicleId = null)
 * @property-read int|null $control_point_time_reports_count
 * @property-read int|null $off_roads_count
 * @property-read int|null $parking_report_count
 * @property-read int|null $reports_count
 * @property-read int|null $speeding_report_count
 * @property int|null $edit_user_id
 * @property string|null $edited_info
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereEditUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister whereEditedInfo($value)
 */
class DispatchRegister extends Model
{
    const IN_PROGRESS = "En camino";
    const COMPLETE = "Terminó";

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    public function getDepartureTimeAttribute($departure_time)
    {
        return StrTime::toString($departure_time);
    }

    public function getArrivalTimeAttribute($arrival_time)
    {
        return StrTime::toString($arrival_time);
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'dispatch_register_id', 'id')->orderBy('date', 'asc');
    }

    /**
     * @param string $order
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function locations($order = 'asc')
    {
        return $this->hasMany(Location::class, 'dispatch_register_id', 'id')->orderBy('date', $order);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function offRoads()
    {
        return $this->hasMany(Location::class, 'dispatch_register_id', 'id')->where('off_road', true)->orderBy('date', 'asc');
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

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_code', 'code');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', $this::COMPLETE);
    }

    public function scopeActive($query, $completedTurns = null)
    {
        if ($completedTurns) {
            return $query->completed();
        }

        return $query->where(function ($query) {
            $query->where('status', $this::COMPLETE)->orWhere('status', $this::IN_PROGRESS);
        });
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
        return $this->complete() ? StrTime::subStrTime($this->arrival_time, $this->departure_time) : '--:--:--';
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

    public function getPassengersBySensorAttribute()
    {
        if ($this->inProgress()) {
            $currentSensor = CurrentSensorPassengers::whereVehicle($this->vehicle);
            $hasReset = ($currentSensor->sensorCounter < $this->initial_sensor_counter);
            return $currentSensor->sensorCounter - ($hasReset ? 0 : $this->initial_sensor_counter);
        }
        $hasReset = ($this->final_sensor_counter < $this->initial_sensor_counter);
        return ($this->final_sensor_counter - ($hasReset ? 0 : $this->initial_sensor_counter));
    }

    public function getInitialPassengersBySensorRecorderAttribute()
    {
        $hasReset = ($this->final_sensor_recorder < $this->initial_sensor_recorder);
        return $hasReset ? 0 : $this->initial_sensor_recorder;
    }

    public function getFinalPassengersBySensorRecorderAttribute()
    {
        return $this->final_sensor_recorder;
    }

    public function getPassengersBySensorRecorderAttribute()
    {
        if ($this->inProgress()) {
            $currentSensor = CurrentSensorPassengers::whereVehicle($this->vehicle);
            $hasReset = ($currentSensor->sensorRecorderCounter < $this->initial_sensor_recorder);
            return $currentSensor->sensorRecorderCounter - ($hasReset ? 0 : $this->initial_sensor_recorder);
        }
        $hasReset = ($this->final_sensor_recorder < $this->initial_sensor_recorder);
        return ($this->final_sensor_recorder - ($hasReset ? 0 : $this->initial_sensor_recorder));
    }

    public function calculateErrorPercent($reference, $value)
    {
        if (!$reference || $reference == 0) $reference = 1;
        return number_format((100 - $value * 100 / $reference), 1, '.', '');
    }

    public function scopeFindAllByDateAndVehicleAndRoute($query, $date, $vehicleId, $routeId)
    {
        return $query->completed()
            ->where('date', $date)
            ->where('vehicle_id', $vehicleId)
            ->where('route_id', $routeId)
            ->orderBy('round_trip')
            ->get();
    }

    public function getAPIFields()
    {
        return (object)[
            'id' => $this->id,
            'date' => $this->getParsedDate()->toDateString(),
            'turn' => $this->turn,
            'round_trip' => $this->round_trip,
            'departure_time' => $this->departure_time,
            'arrival_time_scheduled' => $this->arrival_time_scheduled,
            'arrival_time' => $this->complete() ? $this->arrival_time : '--:--:--',
            'difference_time' => $this->arrival_time_difference,
            'route_time' => $this->getRouteTime(),
            'route' => $this->route->getAPIFields(),
            'vehicle' => $this->vehicle->getAPIFields(),
            'status' => $this->status,
            'driver_name' => $this->driver ? $this->driver->fullName() : __('Unassigned'),
            'dispatcherName' => $this->user ? $this->user->name : __('Unassigned'),
        ];
    }

    public function displayObservationsCounter($observationsCounter)
    {
        $observationsCounterDisplay = '';
        if ($observationsCounter) {
            $observationsCounter = collect(json_decode($observationsCounter, true));
            if ($observationsCounter->isNotEmpty()) {
                $observationsCounterDisplay .= "<ul>";
                $observationsCounterDisplay .= "<li>" . __('Ascents') . ": " . $observationsCounter['passengersOnBoard'] . "</li>";
                $observationsCounterDisplay .= "<li>" . __('Descents') . ": " . $observationsCounter['passengersGettingOff'] . "</li>";
                $observationsCounterDisplay .= "<li>" . __('Total ascents') . ": " . $observationsCounter['totalPassengersOnBoard'] . "</li>";
                $observationsCounterDisplay .= "<li>" . __('Total descents') . ": " . $observationsCounter['totalPassengersGettingOff'] . "</li>";
                $observationsCounterDisplay .= "<li>" . __('Current passengers on board') . ": " . $observationsCounter['currentPassengersOnBoard'] . "</li>";
                $observationsCounterDisplay .= "<li>" . __('Calculated') . " » " . $observationsCounter['totalPassengers'] . "</li>";
                $observationsCounterDisplay .= "</ul>";
            }
        }
        return $observationsCounterDisplay;
    }

    public function displayInitialObservationsCounter()
    {
        return $this->displayObservationsCounter($this->initial_counter_obs);
    }

    public function displayFinalObservationsCounter()
    {
        return $this->displayObservationsCounter($this->final_counter_obs);
    }

    public function hasObservationCounter()
    {
        return $this->vehicle->plate == 'VCK-531';
    }

    public function scopeWhereCompanyAndRouteId($query, Company $company, $routeId = null)
    {
        $query->where(function ($query) use ($company, $routeId) {
            $query = $query->whereIn('vehicle_id', $company->userVehicles($routeId)->pluck('id'));

            if ($company->hasADD()) {
                $query = $query->orWhere('route_id', intval($routeId));
            } else if ($routeId != 'all') {
                $query = $query->where('route_id', intval($routeId));
            }

            return $query;
        });
    }

    public function scopeWhereCompanyAndDateAndRouteIdAndVehicleId($query, Company $company, $date, $routeId = null, $vehicleId = null)
    {
        $query->where('date', explode(' ', $date)[0])->where(function ($query) use ($company, $routeId, $vehicleId) {
            if ($vehicleId == 'all') {
                $query = $query->whereIn('vehicle_id', $company->userVehicles($routeId)->pluck('id'));
            } else if ($vehicleId) {
                $query = $query->where('vehicle_id', $vehicleId);
            }

            if ($company->hasADD()) {
                $query = $query->orWhere('route_id', intval($routeId));
            } else if ($routeId != 'all') {
                $query = $query->where('route_id', intval($routeId));
            }

            return $query;
        });
    }

    public function getOffRoadPercent()
    {
        $totalLocations = $this->locations()->count();
        $totalOffRoad = $totalLocations ? $this->getTotalOffRoad() : 0;

        return $totalOffRoad ? number_format(100 * $totalOffRoad / $totalLocations, 1, '.', '') : 0;
    }

    public function getRouteDistance($withFormat = false)
    {
        if ($this->inProgress()) return 0;

        $routeDistance = $this->end_odometer - $this->start_odometer;
        $routeDistance = $routeDistance > 0 && $routeDistance < 500000 ? $routeDistance : 0;

        if ($withFormat) return number_format($routeDistance / 1000, 2, ',', '');

        return $routeDistance;
    }

    public function getTotalOffRoad()
    {
        if ($this->inProgress() || $this->getRouteDistance() < 5000) return 0;


        $lastLocation = $this->locations('desc')->limit(1)->get()->first();

        return $lastLocation ? $lastLocation->getTotalOffRoad($this->route->id) : 0;
    }

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
