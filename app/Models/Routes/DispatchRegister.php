<?php

namespace App\Models\Routes;

use App\Http\Controllers\Utils\StrTime;
use App\Models\Company\Company;
use App\Models\Drivers\Driver;
use App\Models\Operation\FuelStation;
use App\Models\Passengers\CurrentSensorPassengers;
use App\Models\Passengers\Passenger;
use App\Models\Users\User;
use App\Models\Vehicles\Location;
use App\Models\Vehicles\ParkingReport;
use App\Models\Vehicles\Speeding;
use App\Models\Vehicles\Vehicle;
use App\Models\Vehicles\VehicleStatus;
use Auth;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * App\Models\Routes\DispatchRegister
 *
 * @property int $id
 * @property string|null $date
 * @property string|null $time
 * @property int|null $route_id
 * @property int|null $ard_route_id
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
 * @method static Builder|DispatchRegister whereDate($column, $operator, $value = null, $boolean = 'and')
 * @method static Builder|DispatchRegister whereBetween($column, $values, $boolean = 'and', $not = false)
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
 * @method static Builder|DispatchRegister active($completedTurns = null)
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
 * @method static Builder|DispatchRegister type($type)
 * @method static Builder|DispatchRegister completed()
 * @method static Builder|DispatchRegister cancelled()
 * @method static Builder|DispatchRegister whereStatusType($completedTurns = true, $activeTurns = true, $cancelledTurns = false)
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
 * @method static Builder|DispatchRegister findAllByDateAndVehicleAndRoute($date, $vehicleId, $routeId)
 * @method static Builder|DispatchRegister whereFinalBackSensorCounter($value)
 * @method static Builder|DispatchRegister whereFinalFrontSensorCounter($value)
 * @method static Builder|DispatchRegister whereFinalTimeSensorCounter($value)
 * @method static Builder|DispatchRegister whereInitialBackSensorCounter($value)
 * @method static Builder|DispatchRegister whereInitialFrontSensorCounter($value)
 * @method static Builder|DispatchRegister whereInitialTimeSensorCounter($value)
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
 * @method static Builder|DispatchRegister whereDateOrRange($initialDate, $finalDate = null)
 * @method static Builder|DispatchRegister whereCompanyAndRouteAndVehicle(Company $company, $route = null, $vehicle = null)
 * @method static Builder|DispatchRegister whereCompanyAndDateRangeAndRouteIdAndVehicleId(Company $company, $initialDate, $finalDate = null, $routeId = null, $vehicleId = null)
 * @method static Builder|DispatchRegister whereCompanyAndDateAndRouteIdAndVehicleId(Company $company, $date, $routeId = null, $vehicleId = null)
 * @property-read mixed $route_time
 * @property-read mixed $total_off_road
 * @method static Builder|DispatchRegister whereDriver(Driver $driver = null)
 * @method static Builder|DispatchRegister whereVehicle(Vehicle $vehicle = null)
 * @property-read int|null $control_point_time_reports_count
 * @property-read int|null $off_roads_count
 * @property-read int|null $parking_report_count
 * @property-read int|null $reports_count
 * @property-read int|null $speeding_report_count
 * @property int|null $edit_user_id
 * @property string|null $edited_info
 * @method static Builder|DispatchRegister whereEditUserId($value)
 * @method static Builder|DispatchRegister whereEditedInfo($value)
 * @property-read RouteTaking $takings
 * @property-read RouteTariff $tariff
 * @property-read RouteTariff $fuel_tariff
 * @property-read mixed $mileage
 * @property-read mixed $passengers_by_sensor_total
 * @property-read RouteTaking|null $routeTakings
 * @property int|null $initial_charge
 * @property int|null $final_charge
 * @method static Builder|DispatchRegister whereFinalCharge($value)
 * @method static Builder|DispatchRegister whereInitialCharge($value)
 * @property int|null $driver_id
 * @method static Builder|DispatchRegister whereDriverId($value)
 */
class DispatchRegister extends Model
{
    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';

    const IN_PROGRESS = "En camino";
    const COMPLETE = "Terminó";
    const CANCELLED = "No terminó";
    const TAKINGS = "takings";

    protected function newRelatedInstance($class)
    {
        if ($class instanceof Model) {
            return $class;
        }

        return tap(new $class, function ($instance) {
            if (!$instance->getConnectionName()) {
                $instance->setConnection($this->connection);
            }
        });
    }

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    /**
     * @param $date
     * @return string
     */
    function getDateAttribute($date)
    {
        if (Str::contains($date, '-')) {
            return Carbon::createFromFormat('Y-m-d', explode(' ', $date)[0])->toDateString();
        }

        return Carbon::createFromFormat(config('app.date_format'), explode(' ', $date)[0])->toDateString();
    }

    function getTimeAttribute($time)
    {
        return explode('.', $time)[0];
    }

    function getDepartureTimeAttribute($departure_time)
    {
        return StrTime::toString($departure_time);
    }

    function getArrivalTimeAttribute($arrival_time)
    {
        return StrTime::toString($arrival_time);
    }

    function reports()
    {
        return $this->hasMany(Report::class, 'dispatch_register_id', 'id')->orderBy('date', 'asc');
    }

    /**
     * @param string $order
     * @return HasMany
     */
    function locations($order = 'asc')
    {
        $location = new Location();
        $location->forDate($this->date);
        return $this->hasMany($location, 'dispatch_register_id', 'id')->orderBy('date', $order);
    }

    /**
     * @return HasMany
     */
    function offRoads()
    {
        $location = new Location();
        $location->forDate($this->date);
        return $this->hasMany($location, 'dispatch_register_id', 'id')->where('off_road', true)->orderBy('date', 'asc');
    }

    /**
     * @return BelongsTo
     */
    function route()
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * @return BelongsTo
     */
    function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    function getParsedDate()
    {
        return Carbon::createFromFormat('Y-m-d', $this->date);
    }

    function scopeWhereVehicle($query, Vehicle $vehicle = null)
    {
        if ($vehicle) $query = $query->where('vehicle_id', $vehicle->id);
        return $query;
    }

    function scopeWhereDriver($query, Driver $driver = null)
    {
        if ($driver) $query = $query->where(function ($q) use ($driver) {
            return $q->where('driver_code', $driver->code)->orWhere('driver_id', $driver->id);
        });

        return $query;
    }

    function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_code', 'code');
    }

    function driverName()
    {
        $driver = $this->driver;
        return $driver ? $driver->fullName() : ($this->driver_code ? $this->driver_code : __('Not assigned'));
    }

    /**
     * @param DispatchRegister|Builder $query
     * @return mixed
     */
    function scopeCompleted(Builder $query)
    {
        return $query->where('status', $this::COMPLETE);
    }

    /**
     * @param DispatchRegister|Builder $query
     * @return mixed
     */
    function scopeWhereStatusType(Builder $query, $completedTurns = true, $activeTurns = true, $cancelledTurns = false)
    {
        $q = $query->where(function (Builder $subQuery) use ($completedTurns, $activeTurns, $cancelledTurns) {
            if ($completedTurns) $subQuery->orWhere('status', $this::COMPLETE);
            if ($activeTurns) $subQuery->orWhere('status', $this::IN_PROGRESS);
            if ($cancelledTurns) $subQuery->orWhere('status', 'like', $this::CANCELLED . '%');

            $subQuery->where('status', 'not like', '%Falsa%')
                ->where('status', 'not like', '%Duplicado%');

            return $subQuery;
        });
    }

    /**
     * @param Builder | DispatchRegister $query
     * @param $type
     * @return mixed
     */
    function scopeType($query, $type)
    {
        switch ($type) {
            case 'completed':
                return $query->completed();
                break;
            case 'active':
                return $query->active();
                break;
            case 'cancelled':
                return $query->cancelled();
                break;
            case 'takings':
                return $query->where(function ($query) use ($type) {
                    $query->completed()->orWhere('status', $type);
                });
                break;
            default:
                return $query;
                break;
        }
    }

    function scopeCancelled($query)
    {
        return $query->where('status', 'like', $this::CANCELLED . '%');
    }

    function scopeActive($query, $completedTurns = null)
    {
        if ($completedTurns) {
            return $query->completed();
        }

        return $query->where(function ($query) {
            $query->where('status', $this::COMPLETE)->orWhere('status', $this::IN_PROGRESS);
        });
    }

    function complete()
    {
        return $this->status == $this::COMPLETE;
    }

    function isActive()
    {
        return $this->status == $this::COMPLETE || $this->status == $this::IN_PROGRESS;
    }

    function isCancelled()
    {
        return Str::contains($this->status, $this::CANCELLED);
    }

    function inProgress()
    {
        return $this->status == $this::IN_PROGRESS;
    }

    function speedingReport()
    {
        return $this->hasMany(Speeding::class)->orderBy('date', 'asc');
    }

    function parkingReport()
    {
        return $this->hasMany(ParkingReport::class)->orderBy('date', 'asc');
    }

    function controlPointTimeReports()
    {
        return $this->hasMany(ControlPointTimeReport::class)->orderBy('date', 'asc');
    }

    function getRouteTime($withZero = false)
    {
        return $this->complete() ? StrTime::subStrTime($this->arrival_time, $this->departure_time) : ($withZero ? '00:00:00' : '--:--:--');
    }

    function getRouteTimeAttribute()
    {
        return $this->getRouteTime(true);
    }

    function departureFringe()
    {
        return $this->belongsTo(Fringe::class, 'departure_fringe_id', 'id');
    }

    function arrivalFringe()
    {
        return $this->belongsTo(Fringe::class, 'arrival_fringe_id', 'id');
    }

    function getStatusString()
    {
        return explode('.', $this->status)[0];
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }

    function getPassengersBySensorAttribute()
    {
        $initialCount = $this->initial_sensor_counter;
        $finalCount = $this->final_sensor_counter;

        $currentSensor = CurrentSensorPassengers::whereVehicle($this->vehicle);
        if ($this->inProgress() && $this->getParsedDate()->isToday() && $currentSensor && isset($currentSensor->sensorCounter)) $finalCount = $currentSensor->sensorCounter;

        $firstPassenger = Passenger::where('dispatch_register_id', $this->id)->orderBy('date')->first();
        $lastPassenger = Passenger::where('dispatch_register_id', $this->id)->orderByDesc('date')->first();

        if ($firstPassenger && !$finalCount) $initialCount = $firstPassenger->total_prev;
        if ($lastPassenger && !$finalCount) $finalCount = $lastPassenger->total;

        $hasReset = $finalCount < $initialCount;
        return $finalCount - ($hasReset ? 0 : $initialCount);
    }

    function getPassengersBySensorTotalAttribute()
    {
        if ($this->inProgress() && false) {
            $lastPassenger = Passenger::where('dispatch_register_id', $this->id)->orderByDesc('date')->first();

            if (!$lastPassenger) {
                return 0;
            }

            return $lastPassenger->total - $this->initial_front_sensor_counter;
        }
        $hasReset = ($this->final_sensor_counter < $this->initial_front_sensor_counter);
        return ($this->final_sensor_counter - ($hasReset ? 0 : $this->initial_front_sensor_counter));
    }

    function getInitialPassengersBySensorRecorderAttribute()
    {
        $hasReset = ($this->final_sensor_recorder < $this->initial_sensor_recorder);
        return $hasReset ? 0 : $this->initial_sensor_recorder;
    }

    function getFinalPassengersBySensorRecorderAttribute()
    {
        return $this->final_sensor_recorder;
    }

    function getPassengersBySensorRecorderAttribute()
    {
        if ($this->inProgress()) {
            $currentSensor = CurrentSensorPassengers::whereVehicle($this->vehicle);

            if (!$currentSensor || !isset($currentSensor->sensorRecorderCounter)) {
                return 0;
            }

            $hasReset = ($currentSensor->sensorRecorderCounter < $this->initial_sensor_recorder);
            return $currentSensor->sensorRecorderCounter - ($hasReset ? 0 : $this->initial_sensor_recorder);
        }
        $hasReset = ($this->final_sensor_recorder < $this->initial_sensor_recorder);
        return ($this->final_sensor_recorder - ($hasReset ? 0 : $this->initial_sensor_recorder));
    }

    function calculateErrorPercent($reference, $value)
    {
        if (!$reference || $reference == 0) $reference = 1;
        return number_format((100 - $value * 100 / $reference), 1, '.', '');
    }


    /**
     * @param DispatchRegister | Builder $query
     * @param $date
     * @param $vehicleId
     * @param $routeId
     * @return mixed
     */
    function scopeFindAllByDateAndVehicleAndRoute($query, $date, $vehicleId = null, $routeId = null)
    {
        return $query->completed()
            ->whereDateOrRange($date)
            ->where('vehicle_id', $vehicleId)
            ->where('route_id', $routeId)
            ->orderBy('round_trip')
            ->get();
    }

    function onlyControlTakings()
    {
        return $this->status === self::TAKINGS;
    }

    function forNormalTakings()
    {
        return !$this->onlyControlTakings();
    }

    function getRouteFields()
    {
        $driver = $this->driver;
        $driveName = $driver ? $driver->fullName() : $this->driver_code;
        $driveName = $driveName ?? __('Unassigned');

        return (object)[
            'id' => $this->id,
            'turn' => $this->turn,
            'trip' => $this->round_trip,
            'departure' => $this->onlyControlTakings() ? $this->time : $this->departure_time,
            'driver' => $driveName,
            'route' => $this->onlyControlTakings() ? [] : $this->route->name,
        ];
    }

    function getAPIFields($short = false)
    {
        $passengers = $this->passengers;
        $takings = $this->takings;

        if ($short) {
            return (object)[
                'id' => $this->id,
                'date' => $this->getParsedDate()->toDateString(),
                'turn' => $this->turn,

                'round_trip' => $this->round_trip,
                'roundTrip' => $this->round_trip,

                'departure_time' => $this->onlyControlTakings() ? $this->time : $this->departure_time,
                'departureTime' => $this->onlyControlTakings() ? $this->time : $this->departure_time,

                'routeTime' => $this->getRouteTime(),

                'arrival_time_scheduled' => $this->arrival_time_scheduled,
                'arrivalTimeScheduled' => $this->arrival_time_scheduled,

                'arrival_time' => $this->onlyControlTakings() ? '' : ($this->complete() ? $this->arrival_time : '--:--:--'),
                'arrivalTime' => $this->onlyControlTakings() ? '' : ($this->complete() ? $this->arrival_time : '--:--:--'),

                'route' => $this->onlyControlTakings() ? [] : $this->route->getAPIFields(true),

                'passengers' => $passengers,
                'takings' => $takings && $this->vehicle->company->hasRouteTakings() ? $takings->getAPIFields() : [],
                'onlyControlTakings' => $this->onlyControlTakings(),
                'forNormalTakings' => $this->forNormalTakings(),
                'processTakings' => $this->processTakings(),

                'forTakings' => $this->onlyControlTakings(),
                'mileage' => $this->mileage
            ];
        }

        $driver = $this->driver;
        $driveName = $driver ? $driver->fullName() : __('Unassigned');

        return (object)[
            'id' => $this->id,
            'date' => $this->getParsedDate()->toDateString(),
            'turn' => $this->turn,

            'round_trip' => $this->round_trip,
            'roundTrip' => $this->round_trip,

            'departure_time' => $this->onlyControlTakings() ? $this->time : $this->departure_time,
            'departureTime' => $this->onlyControlTakings() ? $this->time : $this->departure_time,

            'arrival_time_scheduled' => $this->arrival_time_scheduled,
            'arrivalTimeScheduled' => $this->arrival_time_scheduled,

            'arrival_time' => $this->onlyControlTakings() ? '' : ($this->complete() ? $this->arrival_time : '--:--:--'),
            'arrivalTime' => $this->onlyControlTakings() ? '' : ($this->complete() ? $this->arrival_time : '--:--:--'),

            'difference_time' => $this->arrival_time_difference,
            'differenceTime' => $this->arrival_time_difference,

            'route_time' => $this->getRouteTime(),
            'routeTime' => $this->getRouteTime(),

            'route' => $this->onlyControlTakings() ? [] : $this->route->getAPIFields(true),
            'vehicle' => $this->vehicle->getAPIFields(null, true),
            'vehicle_id' => $this->vehicle_id,
            'status' => $this->status,

            'driver_name' => $driveName,
            'driverName' => $driveName,
            'driverCode' => $this->driver_code ? $this->driver_code : __('Unassigned'),

            'dispatcherName' => $this->user ? $this->user->name : __('Unassigned'),

            'passengers' => $passengers,
            'takings' => $takings ? $takings->getAPIFields() : [],
            'onlyControlTakings' => $this->onlyControlTakings(),
            'forNormalTakings' => $this->forNormalTakings(),
            'processTakings' => $this->processTakings(),

            'forTakings' => $this->onlyControlTakings(),
            'mileage' => $this->mileage
        ];
    }

    function displayObservationsCounter($observationsCounter)
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

    function displayInitialObservationsCounter()
    {
        return $this->displayObservationsCounter($this->initial_counter_obs);
    }

    function displayFinalObservationsCounter()
    {
        return $this->displayObservationsCounter($this->final_counter_obs);
    }

    function hasObservationCounter()
    {
        return $this->vehicle->plate == 'VCK-531';
    }

    /**
     * @param Builder | DispatchRegister $query
     * @param string $initialDate
     * @param string | null $finalDate
     * @return DispatchRegister | Builder
     */
    function scopeWhereDateOrRange($query, $initialDate, $finalDate = null)
    {
        if ($finalDate) {
            $query = $query->whereBetween('date', [explode(' ', $initialDate)[0], explode(' ', $finalDate)[0]]);
        } else {
            $query = $query->whereDate('date', explode(' ', $initialDate)[0]);
        }

        return $query;
    }

    /**
     * @param Builder | DispatchRegister $query
     * @param Company $company
     * @param $routeId
     * @return Builder | DispatchRegister
     */
    function scopeWhereCompanyAndRouteId($query, Company $company, $routeId = null)
    {
        return $query->whereCompanyAndRouteAndVehicle($company, $routeId);
    }

    /**
     * @param Builder | DispatchRegister $query
     * @param Company $company
     * @param $routeId
     * @param $vehicleId
     * @return Builder | DispatchRegister
     */
    function scopeWhereCompanyAndRouteAndVehicle($query, Company $company, $routeId = null, $vehicleId = null)
    {
        $query = $query->where(function ($query) use ($company, $routeId, $vehicleId) {
            if ($vehicleId == 'all' || $vehicleId == null) {
                $query = $query->whereIn('vehicle_id', $company->userVehicles($routeId)->pluck('id'));
            } else if ($vehicleId) {
                $query = $query->where('vehicle_id', $vehicleId);
            }

            $query->where(function (Builder $subQuery) use ($company, $routeId, $vehicleId) {
                $user = Auth::user();

                if ($user && $routeId == 'all') {
                    $subQuery = $subQuery->whereIn('route_id', $user->getUserRoutes($company)->pluck('id'));
                }

                if ($routeId != null && $routeId != 'all') {
                    if ($company->hasADD() && $vehicleId == 'all') {
                        $subQuery = $subQuery->orWhere('route_id', intval($routeId));
                    } else {
                        $subQuery = $subQuery->where('route_id', intval($routeId));

                        $route = Route::find(intval($routeId));
                        if ($route) {
                            $subQuery = $subQuery->orWhereIn('route_id', $route->subRoutes->pluck('id'));
                        }
                    }
                }

                return $subQuery;
            });

            return $query;
        });

        $user = Auth::user();

        $route = Route::find(intval($routeId));
        if ($route && $vehicleId == 'all' && $user && !$user->isProprietary()) {
            $query = $query->orWhereIn('route_id', $route->subRoutes->pluck('id'));
        }

        return $query;
    }

    /**
     * @param Builder | DispatchRegister $query
     * @param Company $company
     * @param $date
     * @param null $routeId
     * @param null $vehicleId
     * @return DispatchRegister | Builder
     */
    function scopeWhereCompanyAndDateAndRouteIdAndVehicleId($query, Company $company, $date, $routeId = null, $vehicleId = null)
    {
        return $query->whereDateOrRange($date)->whereCompanyAndRouteAndVehicle($company, $routeId, $vehicleId);
    }

    /**
     * @param Builder | DispatchRegister $query
     * @param Company $company
     * @param string $initialDate
     * @param string | null $finalDate
     * @param null $routeId
     * @param null $vehicleId
     * @return DispatchRegister | Builder
     */
    function scopeWhereCompanyAndDateRangeAndRouteIdAndVehicleId($query, Company $company, $initialDate, $finalDate = null, $routeId = null, $vehicleId = null)
    {
        return $query->whereDateOrRange($initialDate, $finalDate)->whereCompanyAndRouteAndVehicle($company, $routeId, $vehicleId)
            ->with('vehicle')
            ->with('route')
            ->with('driver')
            ->with('user');
    }

    function hasValidOffRoad()
    {
        $offRoadPercent = $this->getOffRoadPercent();
        $invalidGPSPercent = $this->invalidGPSPercent();

        return $offRoadPercent < 2 && $invalidGPSPercent < 2 || $offRoadPercent >= 2;
    }

    function getOffRoadPercent()
    {
        $totalLocations = $this->locations()->count();
        $totalOffRoad = $totalLocations ? $this->getTotalOffRoad() : 0;

        return min([$totalOffRoad ? number_format(100 * $totalOffRoad / $totalLocations, 1, '.', '') : 0, 100]);
    }

    function invalidGPSPercent()
    {
        $totalLocations = $this->locations()->count();
        $totalInvalidGPS = $totalLocations ? $this->locations()->where('vehicle_status_id', VehicleStatus::WITHOUT_GPS_SIGNAL)->count() : 0;

        return $totalInvalidGPS ? number_format(100 * $totalInvalidGPS / $totalLocations, 1, '.', '') : 0;
    }

    function getRouteDistance($withFormat = false)
    {
        if ($this->inProgress()) return 0;

        $routeDistance = $this->end_odometer - $this->start_odometer;
        $routeDistance = $routeDistance > 0 && $routeDistance < 500000 ? $routeDistance : 0;

        if ($withFormat) return number_format($routeDistance / 1000, 2, ',', '');

        return $routeDistance;
    }

    function getTotalOffRoad()
    {
        if ($this->inProgress() || $this->getRouteDistance() < 5000) return 0;

        return $this->offRoads()->count();
    }

    function getTotalOffRoadAttribute()
    {
        return $this->getTotalOffRoad();
    }

    function getPassengersAttribute()
    {
        $passengers = collect([
            'recorders' => $this->getPassengersByRecorder(),
            'sensor' => $this->getPassengersBySensor(),
            'takings' => $this->getPassengersByTakings(),
        ]);
        $counter = $this->getCounterForTakings();
        $passengers->put('taken', $passengers->get($counter));

        return (object)$passengers->toArray();
    }

    function getPassengersTakings()
    {
        $counter = $this->getCounterForTakings();
        return $this->passengers->$counter;
    }

    function getPassengersBy($counter)
    {
        return $this->passengers->$counter;
    }

    function getPassengersByRecorder()
    {
        if ($this->route && $this->route->company_id == Company::YUMBENOS) {
            return $this->getPassengersBySensor();
        }

        $count = $this->complete() ? intval($this->end_recorder) - intval($this->start_recorder) : 0;

        if ($count < 0 && intval($this->end_recorder) < 1000 && intval($this->start_recorder) > 900000) {
            $count = (1000000 - intval($this->start_recorder)) + intval($this->end_recorder);
        }

        return (object)[
            'start' => $this->start_recorder,
            'end' => $this->end_recorder,
            'count' => $count,
            'mileage' => $this->mileage
        ];
    }

    function getPassengersBySensor()
    {
        $default = (object)[
            'tariff' => 0,
            'totalCounted' => 0,
            'totalCharge' => 0,
        ];
        $tariffs = collect([$default, $default]);

        return (object)[
            'start' => $this->initial_sensor_counter,
            'end' => $this->final_sensor_counter,
            'count' => $this->passengersBySensor,
            'mileage' => $this->mileage,
            'tariff' => (object)[
                'a' => (object)($tariffs->get(0) ? $tariffs->get(0) : $default),
                'b' => (object)($tariffs->get(1) ? $tariffs->get(1) : $default),
            ]
        ];
    }

    function getPassengersByTakings()
    {
        return $this->getPassengersBySensorRecorder();
    }

    function getPassengersBySensorRecorder()
    {
        return (object)[
            'start' => $this->initial_sensor_recorder,
            'end' => $this->final_sensor_recorder,
            'count' => $this->passengersBySensorRecorder,
            'mileage' => $this->mileage,
        ];
    }

    function routeTakings()
    {
        return $this->hasOne(RouteTaking::class);
    }

    /**
     * @return RouteTaking
     */
    function getTakingsAttribute()
    {
        $this->refresh();
        $takings = $this->routeTakings;

        if (!$takings) {
            $takings = new RouteTaking();
            $takings->total_production = 0;
            $takings->dispatchRegister()->associate($this);

            $fuelStations = FuelStation::allByCompany($this->route->company);
            $takings->fuelStation()->associate($fuelStations->first());
        }

        if (!$this->onlyControlTakings()) {
            $passengers = $this->getPassengersBy($this->getCounterForTakings())->count;

            $takings->passenger_tariff = $takings->passengerTariff($this->route);
            $totalProduction = $takings->passenger_tariff * $passengers;

            if ($this->route && $this->route->company_id == Company::YUMBENOS) {
                $totalProduction = $this->final_charge - $this->initial_charge;
            }

            if (!$this->route->company->hasTakingsWithMultitariff()) $takings->total_production = $totalProduction;
        }

        $takings->fuel_tariff = $takings->fuelTariff($this->route);
        $takings->fuel_gallons = $takings->fuel_tariff > 0 ? $takings->fuel / $takings->fuel_tariff : 0;

        $takings->net_production = $takings->total_production - $takings->control - $takings->fuel - $takings->others - $takings->bonus;

        if ($this->vehicle->company->hasRouteTakings()) {
            $takings->save();
        }

        if (!$this->processTakings()) {
            $takings->total_production = 0;
            $takings->control = 0;
            $takings->fuel = 0;
            $takings->fuel_gallons = 0;
            $takings->fuel_station_id = null;
            $takings->bonus = 0;
            $takings->others = 0;
            $takings->net_production = 0;
            $takings->advance = 0;
            $takings->balance = 0;
            $takings->observations = "<< VEHICULO EXCLUIDO DE RECAUDO >> " . $takings->observations;
        }

        return $takings;
    }

    function processTakings()
    {
        return !(!$this->vehicle->process_takings && ($this->date > $this->vehicle->to_date_takings || !$this->vehicle->to_date_takings));
    }

    function getMileageAttribute()
    {
//        return ($this->end_odometer - $this->start_odometer) / 1000;
        if (!$this->route) return 0;

        $lastControlPoint = $this->route->controlPoints()->get()->sortBy('order')->last();
        return $lastControlPoint ? $lastControlPoint->distance_from_dispatch / 1000 : 0;
    }

    function processedByARD()
    {
        return $this->ard_route_id && $this->ard_route_id != $this->route_id && auth()->user()->isAdmin();
    }

    /**
     * @return HasOne | DispatcherVehicle
     */
    function dispatcherVehicle()
    {
        return $this->hasOne(DispatcherVehicle::class, 'vehicle_id', 'vehicle_id')->where('dispatch_id', $this->dispatch_id);
    }

    function getObservation($field = null): DrObservation
    {
        $field = __($field);
        $drObs = $this->hasOne(DrObservation::class)->where('field', $field)->first();

        if (!$drObs) {
            $user = Auth::user();
            $drObs = new DrObservation();
            $drObs->field = $field;
            $drObs->dispatchRegister()->associate($this);
            $drObs->user()->associate($user);

            if ($user->isSuperAdmin()) {
                $drObs->observation = 'Por solicitud grupo de soporte';
            }
        }

        return $drObs;
    }

    function getPassengersAccumulated($from = null)
    {

        $from = $from ?: $this->date;

        $dispatchRegisters = DispatchRegister::whereCompanyAndDateRangeAndRouteIdAndVehicleId($this->route->company, $from, null, $this->route_id, $this->vehicle_id)
            ->completed()
            ->where('id', '<=', $this->id)
            ->get();

        $dispatchRegistersNoTaken = $dispatchRegisters->filter(function (DispatchRegister $dispatchRegister) {
            return !$dispatchRegister->takings->isTaken() // Exclude turns already taken
                || $dispatchRegister->id == $this->id // Includes current turn
                || $dispatchRegister->takings->parent_takings_id == $this->takings->id; // Includes turn related with current takings
        });

        $dispatchRegistersTaken = $dispatchRegisters->filter(function (DispatchRegister $dispatchRegister) {
            return $dispatchRegister->takings->isTaken() && $dispatchRegister->id != $this->id;
        });

        $accumulated = $dispatchRegistersNoTaken->reduce(function ($carry, DispatchRegister $dispatchRegister) {
            $prev = collect($carry);
            return collect([
                'recorders' => intval($prev->get('recorders')) + $dispatchRegister->passengers->recorders->count,
                'sensor' => intval($prev->get('sensor')) + $dispatchRegister->passengers->sensor->count,
                'takings' => intval($prev->get('takings')) + $dispatchRegister->passengers->takings->count
            ]);
        });

        $accumulated = collect($accumulated);

        $accumulated->put('taken', $dispatchRegistersTaken->sortBy('departure_time')->map(function (DispatchRegister $dispatchRegister) {

            return (object)[
                'roundTrip' => $dispatchRegister->round_trip,
                'routeName' => $dispatchRegister->route->name,
            ];
        }));

        $accumulated->put('noTaken', $dispatchRegistersNoTaken->sortBy('departure_time')->map(function (DispatchRegister $dispatchRegister) {
            return (object)[
                'roundTrip' => $dispatchRegister->round_trip,
                'routeName' => $dispatchRegister->route->name,
            ];
        }));

        return (object)$accumulated->toArray();
    }

    /**
     * Define cuál conteo (Por sensor o registradora) será tenido en cuenta para el proceso de recaudo
     * Para los turnos que ya han sido recaudados se retorna el contador guardado en el momento de guardar el recaudo
     * En caso contrario se retorna un valor por defecto
     * @return string
     */
    function getCounterForTakings()
    {
        $counterTakings = $this->routeTakings;

        if ($counterTakings && $counterTakings->isTaken()) return $counterTakings->counter;

        return 'recorders'; // TODO: Se debe cambiar dinámicamente según el tipo de contador por defecto que tenga el vehículo
    }

    function getTypeCounters()
    {
        return $this->route ? $this->route->company->getTypeCounters() : collect([]);
    }
}
