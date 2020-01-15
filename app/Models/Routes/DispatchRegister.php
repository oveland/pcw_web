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
use Auth;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
 * @property-read Collection|Location[] $locations
 * @property-read Collection|Location[] $offRoads
 * @property-read Collection|Report[] $reports
 * @property-read Collection|Route|null $route
 * @property-read Collection|Vehicle|null $vehicle
 * @property-read Collection|User|null $user
 * @method static Builder|DispatchRegister whereArrivalTime($value)
 * @method static Builder|DispatchRegister whereArrivalTimeDifference($value)
 * @method static Builder|DispatchRegister whereArrivalTimeScheduled($value)
 * @method static Builder|DispatchRegister whereCanceled($value)
 * @method static Builder|DispatchRegister whereDate($value)
 * @method static Builder|DispatchRegister whereDepartureTime($value)
 * @method static Builder|DispatchRegister whereDispatchId($value)
 * @method static Builder|DispatchRegister whereEndRecorder($value)
 * @method static Builder|DispatchRegister whereId($value)
 * @method static Builder|DispatchRegister whereRoundTrip($value)
 * @method static Builder|DispatchRegister whereRouteId($value)
 * @method static Builder|DispatchRegister whereStartRecorder($value)
 * @method static Builder|DispatchRegister whereStatus($value)
 * @method static Builder|DispatchRegister whereTime($value)
 * @method static Builder|DispatchRegister whereTimeCanceled($value)
 * @method static Builder|DispatchRegister whereTurn($value)
 * @method static Builder|DispatchRegister whereTypeOfDay($value)
 * @method static Builder|DispatchRegister whereVehicleId($value)
 * @mixin Eloquent
 * @property string|null $driver_code
 * @property mixed $departure_fringe
 * @property mixed $arrival_fringe
 * @method static Builder|DispatchRegister active($completedTurns)
 * @method static Builder|DispatchRegister whereDriverCode($value)
 * @property-read mixed $passengers
 * @property-read Driver|null $driver
 * @property int|null $departure_fringe_id
 * @property int|null $arrival_fringe_id
 * @property-read Fringe|null $arrivalFringe
 * @property-read Collection|ControlPointTimeReport[] $controlPointTimeReports
 * @property-read Fringe|null $departureFringe
 * @property-read Collection|ParkingReport[] $parkingReport
 * @property-read Collection|Speeding[] $speedingReport
 * @method static Builder|DispatchRegister whereArrivalFringeId($value)
 * @method static Builder|DispatchRegister whereDepartureFringeId($value)
 * @method static Builder|DispatchRegister completed()
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
 * @method static Builder|DispatchRegister whereAvailableVehicles($value)
 * @method static Builder|DispatchRegister whereFinalFrameSensorCounter($value)
 * @method static Builder|DispatchRegister whereFinalSensorCounter($value)
 * @method static Builder|DispatchRegister whereFinalSensorRecorder($value)
 * @method static Builder|DispatchRegister whereInitialFrameSensorCounter($value)
 * @method static Builder|DispatchRegister whereInitialSensorCounter($value)
 * @method static Builder|DispatchRegister whereInitialSensorRecorder($value)
 * @method static Builder|DispatchRegister whereUserId($value)
 * @property string|null $initial_time_sensor_counter
 * @property string|null $final_time_sensor_counter
 * @property int|null $initial_front_sensor_counter
 * @property int|null $initial_back_sensor_counter
 * @property int|null $final_front_sensor_counter
 * @property int|null $final_back_sensor_counter
 * @property-read mixed $passengers_by_sensor
 * @property-read mixed $passengers_by_sensor_recorder
 * @method static Builder|DispatchRegister whereFinalBackSensorCounter($value)
 * @method static Builder|DispatchRegister whereFinalFrontSensorCounter($value)
 * @method static Builder|DispatchRegister whereFinalTimeSensorCounter($value)
 * @method static Builder|DispatchRegister whereInitialBackSensorCounter($value)
 * @method static Builder|DispatchRegister whereInitialFrontSensorCounter($value)
 * @method static Builder|DispatchRegister whereInitialTimeSensorCounter($value)
 * @method static Builder|DispatchRegister findAllByDateAndVehicleAndRoute($date, $vehicleId, $routeId)
 * @property-read mixed $final_passengers_by_sensor_recorder
 * @property-read mixed $initial_passengers_by_sensor_recorder
 * @method static Builder|DispatchRegister whereFinalCounterObs($value)
 * @method static Builder|DispatchRegister whereInitialCounterObs($value)
 * @property float|null $start_odometer
 * @property float|null $end_odometer
 * @method static Builder|DispatchRegister whereEndOdometer($value)
 * @method static Builder|DispatchRegister whereStartOdometer($value)
 * @method static Builder|DispatchRegister newModelQuery()
 * @method static Builder|DispatchRegister newQuery()
 * @method static Builder|DispatchRegister query()
 * @method static Builder|DispatchRegister whereCompanyAndRouteId(Company $company, $routeId = null)
 * @method static Builder|DispatchRegister whereCompanyAndDateAndRouteIdAndVehicleId(Company $company, $date, $routeId = null, $vehicleId = null)
 * @property-read mixed $route_time
 * @property-read mixed $total_off_road
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
     * @return HasMany
     */
    public function locations($order = 'asc')
    {
        return $this->hasMany(Location::class, 'dispatch_register_id', 'id')->orderBy('date', $order);
    }

    /**
     * @return HasMany
     */
    public function offRoads()
    {
        return $this->hasMany(Location::class, 'dispatch_register_id', 'id')->where('off_road', true)->orderBy('date', 'asc');
    }

    /**
     * @return BelongsTo
     */
    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * @return BelongsTo
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

    /**
     * @param DispatchRegister $query
     * @return mixed
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', $this::COMPLETE);
    }

    /**
     * @param DispatchRegister $query
     * @param null $completedTurns
     * @return mixed
     */
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

    public function getRouteTime($withZero = false)
    {
        return $this->complete() ? StrTime::subStrTime($this->arrival_time, $this->departure_time) : ($withZero ? '00:00:00' : '--:--:--');
    }

    public function getRouteTimeAttribute()
    {
        return $this->getRouteTime(true);
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


    /**
     * @param DispatchRegister $query
     * @param $date
     * @param $vehicleId
     * @param $routeId
     * @return mixed
     */
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
            'driver_id' => $this->driver ? $this->driver->id : null,
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


    /**
     * @param DispatchRegister $query
     * @param Company $company
     * @param null $routeId
     */
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

    /**
     * @param DispatchRegister $query
     * @param Company $company
     * @param $date
     * @param null $routeId
     * @param null $vehicleId
     */
    public function scopeWhereCompanyAndDateAndRouteIdAndVehicleId($query, Company $company, $date, $routeId = null, $vehicleId = null)
    {
        $user = Auth::user();

        $query->where('date', explode(' ', $date)[0])
            ->whereIn('route_id', $user->getUserRoutes($company)->pluck('id'))
            ->where(function ($query) use ($company, $routeId, $vehicleId) {
            if ($vehicleId == 'all') {
                $query = $query->whereIn('vehicle_id', $company->userVehicles($routeId)->pluck('id'));
            } else if ($vehicleId) {
                $query = $query->where('vehicle_id', $vehicleId);
            }

            if ($company->hasADD() && $vehicleId == 'all') {
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

    public function getTotalOffRoadAttribute(){
        return $this->getTotalOffRoad();
    }

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
