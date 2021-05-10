<?php

namespace App\Models\Routes;

use App\Http\Controllers\Utils\StrTime;
use App\Models\Company\Company;
use App\Models\Drivers\Driver;
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
 * @property-read \RouteTariff $tariff
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
    const IN_PROGRESS = "En camino";
    const COMPLETE = "Terminó";
    const CANCELLED = "No terminó";
    const TAKINGS = "takings";

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    /**
     * @param $date
     * @return string
     */
    public function getDateAttribute($date)
    {
        if (Str::contains($date, '-')) {
            return Carbon::createFromFormat('Y-m-d', explode(' ', $date)[0])->toDateString();
        }

        return Carbon::createFromFormat(config('app.date_format'), explode(' ', $date)[0])->toDateString();
    }

    public function getTimeAttribute($time)
    {
        return explode('.', $time)[0];
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
        return $this->hasMany(new Location(), 'dispatch_register_id', 'id')->orderBy('date', $order);
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
        return Carbon::createFromFormat('Y-m-d', $this->date);
    }

    public function scopeWhereVehicle($query, Vehicle $vehicle = null)
    {
        if ($vehicle) $query = $query->where('vehicle_id', $vehicle->id);
        return $query;
    }

    public function scopeWhereDriver($query, Driver $driver = null)
    {
        if ($driver) $query = $query->where(function ($q) use ($driver) {
            return $q->where('driver_code', $driver->code)->orWhere('driver_id', $driver->id);
        });

//        dd($query->toSql(), $query->getBindings(), $query->get());
        return $query;
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_code', 'code');
    }

    public function driverName()
    {
        $driver = $this->driver;
        return $driver ? $driver->fullName() : ($this->driver_code ? $this->driver_code : __('Not assigned'));
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
     * @param Builder | DispatchRegister $query
     * @param $type
     * @return mixed
     */
    public function scopeType($query, $type)
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

    public function scopeCancelled($query)
    {
        return $query->where('status', 'like', $this::CANCELLED . '%');
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

    public function isActive()
    {
        return $this->status == $this::COMPLETE || $this->status == $this::IN_PROGRESS;
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
        $currentSensor = CurrentSensorPassengers::whereVehicle($this->vehicle);
        if ($this->inProgress() && $currentSensor && isset($currentSensor->sensorCounter)) {
            $hasReset = ($currentSensor->sensorCounter < $this->initial_sensor_counter);
            return $currentSensor->sensorCounter - ($hasReset ? 0 : $this->initial_sensor_counter);
        }
        $hasReset = ($this->final_sensor_counter < $this->initial_sensor_counter);
        return ($this->final_sensor_counter - ($hasReset ? 0 : $this->initial_sensor_counter));
    }

    public function getPassengersBySensorTotalAttribute()
    {
        if ($this->inProgress()) {
            $lastPassenger = Passenger::where('dispatch_register_id', $this->id)->orderByDesc('date')->first();

            if (!$lastPassenger) {
                return 0;
            }

            return $lastPassenger->total - $this->initial_front_sensor_counter;
        }
        $hasReset = ($this->final_sensor_counter < $this->initial_front_sensor_counter);
        return ($this->final_sensor_counter - ($hasReset ? 0 : $this->initial_front_sensor_counter));
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

            if (!$currentSensor || !isset($currentSensor->sensorRecorderCounter)) {
                return 0;
            }

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
     * @param DispatchRegister | Builder $query
     * @param $date
     * @param $vehicleId
     * @param $routeId
     * @return mixed
     */
    public function scopeFindAllByDateAndVehicleAndRoute($query, $date, $vehicleId = null, $routeId = null)
    {
        return $query->completed()
            ->whereDateOrRange($date)
            ->where('vehicle_id', $vehicleId)
            ->where('route_id', $routeId)
            ->orderBy('round_trip')
            ->get();
    }

    public function onlyControlTakings()
    {
        return $this->status === self::TAKINGS;
    }

    public function forNormalTakings()
    {
        return !$this->onlyControlTakings();
    }

    public function getRouteFields()
    {
        $driver = $this->driver;
        $driveName = $driver ? $driver->fullName() : __('Unassigned');
        return (object)[
            'id' => $this->id,
            'turn' => $this->turn,
            'roundTrip' => $this->round_trip,
            'departureTime' => $this->onlyControlTakings() ? $this->time : $this->departure_time,
            'driverName' => $driveName,
            'route' => $this->onlyControlTakings() ? [] : $this->route->getAPIFields(true),
        ];
    }

    public function getAPIFields($short = false)
    {
        $passengers = $this->passengers;
//        $passengers = null;

        $takings = $this->takings;
//        $takings = null;

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

                'arrival_time' => $this->onlyControlTakings() ? '' : $this->complete() ? $this->arrival_time : '--:--:--',
                'arrivalTime' => $this->onlyControlTakings() ? '' : ($this->complete() ? $this->arrival_time : '--:--:--'),

                'route' => $this->onlyControlTakings() ? [] : $this->route->getAPIFields(true),

                'passengers' => $passengers,
                'takings' => $takings ? $takings->getAPIFields() : [],
                'onlyControlTakings' => $this->onlyControlTakings(),
                'forNormalTakings' => $this->forNormalTakings(),

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

            'arrival_time' => $this->onlyControlTakings() ? '' : $this->complete() ? $this->arrival_time : '--:--:--',
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

            'forTakings' => $this->onlyControlTakings(),
            'mileage' => $this->mileage
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
     * @param Builder | DispatchRegister $query
     * @param string $initialDate
     * @param string | null $finalDate
     * @return DispatchRegister | Builder
     */
    public function scopeWhereDateOrRange($query, $initialDate, $finalDate = null)
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
    public function scopeWhereCompanyAndRouteId($query, Company $company, $routeId = null)
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
    public function scopeWhereCompanyAndRouteAndVehicle($query, Company $company, $routeId = null, $vehicleId = null)
    {
        return $query
            ->where(function ($query) use ($company, $routeId, $vehicleId) {
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
                        }

                        $route = Route::find($routeId);
                        if ($route->as_group) {
                            $subQuery = $subQuery->orWhereIn('route_id', $route->subRoutes->pluck('id'));
                        }
                    }

                    return $subQuery;
                });

                return $query;
            });
    }

    /**
     * @param Builder | DispatchRegister $query
     * @param Company $company
     * @param $date
     * @param null $routeId
     * @param null $vehicleId
     * @return DispatchRegister | Builder
     */
    public function scopeWhereCompanyAndDateAndRouteIdAndVehicleId($query, Company $company, $date, $routeId = null, $vehicleId = null)
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
    public function scopeWhereCompanyAndDateRangeAndRouteIdAndVehicleId($query, Company $company, $initialDate, $finalDate = null, $routeId = null, $vehicleId = null)
    {
        return $query->whereDateOrRange($initialDate, $finalDate)->whereCompanyAndRouteAndVehicle($company, $routeId, $vehicleId)
            ->with('vehicle')
            ->with('route')
            ->with('driver')
            ->with('user');
    }

    public function hasValidOffRoad()
    {
        $offRoadPercent = $this->getOffRoadPercent();
        $invalidGPSPercent = $this->invalidGPSPercent();

        return $offRoadPercent < 2 && $invalidGPSPercent < 2 || $offRoadPercent >= 2;
    }

    public function getOffRoadPercent()
    {
        $totalLocations = $this->locations()->count();
        $totalOffRoad = $totalLocations ? $this->getTotalOffRoad() : 0;

        return $totalOffRoad ? number_format(100 * $totalOffRoad / $totalLocations, 1, '.', '') : 0;
    }

    public function invalidGPSPercent()
    {
        $totalLocations = $this->locations()->count();
        $totalInvalidGPS = $totalLocations ? $this->locations()->where('vehicle_status_id', VehicleStatus::WITHOUT_GPS_SIGNAL)->count() : 0;

        return $totalInvalidGPS ? number_format(100 * $totalInvalidGPS / $totalLocations, 1, '.', '') : 0;
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

    public function getTotalOffRoadAttribute()
    {
        return $this->getTotalOffRoad();
    }

    public function getPassengersAttribute()
    {
        return (object)[
            'recorders' => $this->getPassengersByRecorder(),
            'sensor' => $this->getPassengersBySensor()
        ];
    }

    public function getPassengersByRecorder()
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

    public function getPassengersBySensor()
    {
        /*$tariffs = collect(\DB::select("
            SELECT tariff, sum(counted) \"totalCounted\", tariff * sum(counted) \"totalCharge\"
            FROM passengers
            WHERE dispatch_register_id = $this->id
            GROUP BY tariff
            ORDER BY tariff
        "));*/


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

    public function routeTakings()
    {
        return $this->hasOne(RouteTaking::class);
    }

    /**
     * @return RouteTaking
     */
    public function getTakingsAttribute()
    {
        $takings = $this->routeTakings;

        if (!$takings) {
            $takings = new RouteTaking();
            $takings->dispatchRegister()->associate($this);
        }

        if (!$this->onlyControlTakings()) {
            $passengers = $this->getPassengersByRecorder()->count;

            $takings->passenger_tariff = $takings->passengerTariff($this->route);

            $totalProduction = $takings->passenger_tariff * $passengers;

            if ($this->route && $this->route->company_id == Company::YUMBENOS) {
                $totalProduction = $this->final_charge - $this->initial_charge;
            }

            $takings->total_production = $totalProduction;
        }

        $takings->fuel_tariff = $takings->fuelTariff($this->route);
        $takings->fuel_gallons = $takings->fuel_tariff > 0 ? $takings->fuel / $takings->fuel_tariff : 0;

        $takings->net_production = $takings->total_production - $takings->control - $takings->fuel - $takings->others - $takings->bonus;

        $takings->save();

        return $takings;
    }

    public function getMileageAttribute()
    {
//        return ($this->end_odometer - $this->start_odometer) / 1000;
        return $this->route ? $this->route->distance_in_km : 0;
    }

    public function processedByARD()
    {
        return $this->ard_route_id && $this->ard_route_id != $this->route_id && auth()->user()->isAdmin();
    }

    /**
     * @return HasOne | DispatcherVehicle
     */
    public function dispatcherVehicle()
    {
        return $this->hasOne(DispatcherVehicle::class, 'vehicle_id', 'vehicle_id')->where('dispatch_id', $this->dispatch_id);
    }
    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
