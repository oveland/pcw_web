<?php
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App{
/**
 * App\AccessLog
 *
 * @property int $id
 * @property string|null $date
 * @property string|null $time
 * @property string|null $user_id
 * @property-read \App\UserLog|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccessLog whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccessLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccessLog whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccessLog whereUserId($value)
 * @mixin \Eloquent
 */
	class AccessLog extends \Eloquent {}
}

namespace App{
/**
 * App\AlamedaAugustLocation
 *
 * @property int $id
 * @property int|null $version
 * @property string|null $date
 * @property \Carbon\Carbon|null $date_created
 * @property int|null $dispatch_register_id
 * @property float|null $distance
 * @property \Carbon\Carbon|null $last_updated
 * @property string|null $latitude
 * @property string|null $longitude
 * @property float|null $odometer
 * @property float|null $orientation
 * @property int|null $speed
 * @property string|null $status
 * @property int|null $vehicle_id
 * @property bool|null $off_road
 * @property-read \App\AlamedaAugustReport $report
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereVersion($value)
 * @mixin \Eloquent
 */
	class AlamedaAugustLocation extends \Eloquent {}
}

namespace App{
/**
 * App\AlamedaAugustReport
 *
 * @property int $id
 * @property int|null $version
 * @property string|null $date
 * @property string|null $date_created
 * @property int|null $dispatch_register_id
 * @property int|null $distanced
 * @property int|null $distancem
 * @property int|null $distancep
 * @property string|null $last_updated
 * @property string|null $status
 * @property string|null $timed
 * @property string|null $timem
 * @property string|null $timep
 * @property int|null $location_id
 * @property float|null $status_in_minutes
 * @property-read \App\DispatchRegister|null $dispatchRegister
 * @property-read \App\AlamedaAugustLocation|null $location
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereDistanced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereDistancep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereTimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereTimem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereTimep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereVersion($value)
 * @mixin \Eloquent
 */
	class AlamedaAugustReport extends \Eloquent {}
}

namespace App{
/**
 * App\AlamedaJulyLocation
 *
 * @property int $id
 * @property int|null $version
 * @property string|null $date
 * @property \Carbon\Carbon|null $date_created
 * @property int|null $dispatch_register_id
 * @property float|null $distance
 * @property \Carbon\Carbon|null $last_updated
 * @property string|null $latitude
 * @property string|null $longitude
 * @property float|null $odometer
 * @property float|null $orientation
 * @property int|null $speed
 * @property string|null $status
 * @property int|null $vehicle_id
 * @property bool|null $off_road
 * @property-read \App\AlamedaJulyReport $report
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereVersion($value)
 * @mixin \Eloquent
 */
	class AlamedaJulyLocation extends \Eloquent {}
}

namespace App{
/**
 * App\AlamedaJulyReport
 *
 * @property int $id
 * @property int|null $version
 * @property string|null $date
 * @property string|null $date_created
 * @property int|null $dispatch_register_id
 * @property int|null $distanced
 * @property int|null $distancem
 * @property int|null $distancep
 * @property string|null $last_updated
 * @property string|null $status
 * @property string|null $timed
 * @property string|null $timem
 * @property string|null $timep
 * @property int|null $location_id
 * @property float|null $status_in_minutes
 * @property-read \App\DispatchRegister|null $dispatchRegister
 * @property-read \App\AlamedaJulyLocation|null $location
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereDistanced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereDistancep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereTimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereTimem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereTimep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereVersion($value)
 * @mixin \Eloquent
 */
	class AlamedaJulyReport extends \Eloquent {}
}

namespace App{
/**
 * App\AlamedaJuneLocation
 *
 * @property int $id
 * @property int|null $version
 * @property string|null $date
 * @property \Carbon\Carbon|null $date_created
 * @property int|null $dispatch_register_id
 * @property float|null $distance
 * @property \Carbon\Carbon|null $last_updated
 * @property string|null $latitude
 * @property string|null $longitude
 * @property float|null $odometer
 * @property float|null $orientation
 * @property int|null $speed
 * @property string|null $status
 * @property int|null $vehicle_id
 * @property bool|null $off_road
 * @property-read \App\AlamedaJuneReport $report
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereVersion($value)
 * @mixin \Eloquent
 */
	class AlamedaJuneLocation extends \Eloquent {}
}

namespace App{
/**
 * App\AlamedaJuneReport
 *
 * @property int $id
 * @property int|null $version
 * @property string|null $date
 * @property string|null $date_created
 * @property int|null $dispatch_register_id
 * @property int|null $distanced
 * @property int|null $distancem
 * @property int|null $distancep
 * @property string|null $last_updated
 * @property string|null $status
 * @property string|null $timed
 * @property string|null $timem
 * @property string|null $timep
 * @property int|null $location_id
 * @property float|null $status_in_minutes
 * @property-read \App\DispatchRegister|null $dispatchRegister
 * @property-read \App\AlamedaJuneLocation|null $location
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereDistanced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereDistancep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereTimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereTimem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereTimep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereVersion($value)
 * @mixin \Eloquent
 */
	class AlamedaJuneReport extends \Eloquent {}
}

namespace App{
/**
 * App\Company
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Vehicle[] $activeVehicles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Vehicle[] $vehicles
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property string $nit
 * @property string|null $address
 * @property string $link
 * @property bool $active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property mixed $routes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Company whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Company whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Company whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Company whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Company whereNit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Company whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Company whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Company active()
 */
	class Company extends \Eloquent {}
}

namespace App{
/**
 * App\ControlPoint
 *
 * @property-read \App\Route $route
 * @mixin \Eloquent
 * @property int $id
 * @property string $latitude
 * @property string $longitude
 * @property string $name
 * @property int $order
 * @property int $trajectory
 * @property string $type
 * @property int $distance_from_dispatch
 * @property int $distance_next_point
 * @property int $route_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereDistanceFromDispatch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereDistanceNextPoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereTrajectory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereUpdatedAt($value)
 */
	class ControlPoint extends \Eloquent {}
}

namespace App{
/**
 * App\ControlPointTime
 *
 * @property int $id
 * @property string $time
 * @property string $time_from_dispatch
 * @property string $time_next_point
 * @property int $day_type_id
 * @property int $control_point_id
 * @property int|null $fringe_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\ControlPoint $controlPoint
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereControlPointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereDayTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereFringeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereTimeFromDispatch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereTimeNextPoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Fringe|null $fringe
 */
	class ControlPointTime extends \Eloquent {}
}

namespace App{
/**
 * App\DayType
 *
 * @property int $id
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DayType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DayType whereName($value)
 * @mixin \Eloquent
 */
	class DayType extends \Eloquent {}
}

namespace App{
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
 */
	class DispatchRegister extends \Eloquent {}
}

namespace App{
/**
 * App\Fringe
 *
 * @property int $id
 * @property string $name
 * @property string $from
 * @property string $to
 * @property bool $active
 * @property int $route_id
 * @property int $day_type_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\DayType $dayType
 * @property-read \App\Route $route
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fringe whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fringe whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fringe whereDayTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fringe whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fringe whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fringe whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fringe whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fringe whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fringe whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $sequence
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fringe whereSequence($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ControlPointTime[] $controlPointTimes
 */
	class Fringe extends \Eloquent {}
}

namespace App{
/**
 * App\GpsVehicle
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $vehicle_id
 * @property string $imei
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GpsVehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GpsVehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GpsVehicle whereImei($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GpsVehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GpsVehicle whereVehicleId($value)
 */
	class GpsVehicle extends \Eloquent {}
}

namespace App{
/**
 * App\HistoryMarker
 *
 * @property int $id
 * @property string|null $fecha
 * @property string|null $hora
 * @property float|null $lat
 * @property float|null $lng
 * @property string|null $id_gps
 * @property int|null $km
 * @property float|null $velocidad
 * @property float|null $orientacion
 * @property int|null $estado
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereHora($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereIdGps($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereOrientacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereVelocidad($value)
 * @mixin \Eloquent
 * @property int|null $km_gps
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereKmGps($value)
 */
	class HistoryMarker extends \Eloquent {}
}

namespace App{
/**
 * App\HistorySeat
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string|null $plate
 * @property int|null $seat
 * @property string $date
 * @property string $time
 * @property float|null $active_latitude
 * @property float|null $active_longitude
 * @property string $active_time
 * @property string|null $inactive_time
 * @property int|null $active_km
 * @property int|null $inactive_km
 * @property string|null $busy_time
 * @property int|null $busy_km
 * @property int|null $complete
 * @property float|null $inactive_latitude
 * @property float|null $inactive_longitude
 * @property int|null $vehicle_id
 * @property int|null $dispatch_register_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereActiveKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereActiveLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereActiveLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereActiveTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereBusyKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereBusyTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereComplete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereInactiveKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereInactiveLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereInactiveLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereInactiveTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat wherePlate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereSeat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereVehicleId($value)
 */
	class HistorySeat extends \Eloquent {}
}

namespace App{
/**
 * App\Location
 *
 * @property int $id
 * @property int $version
 * @property string|null $date
 * @property \Carbon\Carbon $date_created
 * @property int|null $dispatch_register_id
 * @property float|null $distance
 * @property \Carbon\Carbon $last_updated
 * @property string|null $latitude
 * @property string|null $longitude
 * @property float|null $odometer
 * @property float|null $orientation
 * @property int|null $speed
 * @property string|null $status
 * @property int|null $vehicle_id
 * @property bool|null $off_road
 * @property-read \App\Report $report
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereVersion($value)
 */
	class Location extends \Eloquent {}
}

namespace App{
/**
 * App\LocationReport
 *
 * @property int|null $location_id
 * @property int|null $dispatch_register_id
 * @property bool|null $off_road
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $date
 * @property string|null $timed
 * @property int|null $distancem
 * @property float|null $status_in_minutes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport whereTimed($value)
 */
	class LocationReport extends \Eloquent {}
}

namespace App\Models\Passengers{
/**
 * App\Models\Passengers\PassengerCounterPerDay
 *
 * @property int $id
 * @property int|null $total
 * @property float|null $ipk
 * @property string|null $date
 * @property int|null $vehicle_id
 * @property int|null $company_id
 * @property-read \App\Company|null $company
 * @property-read \App\Vehicle|null $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDay whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDay whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDay whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDay whereIpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDay whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDay whereVehicleId($value)
 */
	class PassengerCounterPerDay extends \Eloquent {}
}

namespace App\Models\Passengers{
/**
 * App\Models\Passengers\PassengerCounterPerDaySixMonth
 *
 * @property int $id
 * @property int|null $total
 * @property float|null $ipk
 * @property string|null $date
 * @property int|null $vehicle_id
 * @property int|null $company_id
 * @property-read \App\Company|null $company
 * @property-read \App\Vehicle|null $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDaySixMonth whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDaySixMonth whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDaySixMonth whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDaySixMonth whereIpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDaySixMonth whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDaySixMonth whereVehicleId($value)
 */
	class PassengerCounterPerDaySixMonth extends \Eloquent {}
}

namespace App\Models\Passengers{
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
 */
	class RecorderCounterPerDay extends \Eloquent {}
}

namespace App\Models\Passengers{
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
 */
	class RecorderCounterPerRoundTrip extends \Eloquent {}
}

namespace App{
/**
 * App\OffRoad
 *
 * @property int $id
 * @property int|null $version
 * @property string|null $date
 * @property \Carbon\Carbon|null $date_created
 * @property int|null $dispatch_register_id
 * @property float|null $distance
 * @property \Carbon\Carbon|null $last_updated
 * @property string|null $latitude
 * @property string|null $longitude
 * @property float|null $odometer
 * @property float|null $orientation
 * @property int|null $speed
 * @property string|null $status
 * @property int|null $vehicle_id
 * @property bool|null $off_road
 * @property-read \App\DispatchRegister|null $dispatchRegister
 * @property-read \App\Report $report
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad validCoordinates()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereVersion($value)
 */
	class OffRoad extends \Eloquent {}
}

namespace App{
/**
 * App\Report
 *
 * @property int $id
 * @property int $version
 * @property string $date
 * @property string $date_created
 * @property int $dispatch_register_id
 * @property int $distanced
 * @property int $distancem
 * @property int $distancep
 * @property string $last_updated
 * @property string $status
 * @property string $timed
 * @property string $timem
 * @property string $timep
 * @property int|null $location_id
 * @property float|null $status_in_minutes
 * @property-read \App\DispatchRegister $dispatchRegister
 * @property-read \App\Location|null $location
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereDistanced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereDistancep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereTimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereTimem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereTimep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereVersion($value)
 */
	class Report extends \Eloquent {}
}

namespace App{
/**
 * App\Route
 *
 * @property int $id
 * @property string $name
 * @property int $distance
 * @property int $road_time
 * @property string $url
 * @property int $company_id
 * @property int $dispatch_id
 * @property bool $active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Company $company
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ControlPoint[] $controlPoints
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereDispatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereRoadTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereUrl($value)
 */
	class Route extends \Eloquent {}
}

namespace App{
/**
 * App\RouteGoogle
 *
 * @property int $id_ruta
 * @property string|null $url
 * @property string|null $coordenadas
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RouteGoogle whereCoordenadas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RouteGoogle whereIdRuta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RouteGoogle whereUrl($value)
 */
	class RouteGoogle extends \Eloquent {}
}

namespace App{
/**
 * App\User
 *
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property string $username
 * @property string $password
 * @property string|null $remember_token
 * @property string|null $role
 * @property bool $active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int|null $company_id
 * @property-read \App\Company|null $company
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUsername($value)
 */
	class User extends \Eloquent {}
}

namespace App{
/**
 * App\UserLog
 *
 * @property string $usuario
 * @property string|null $clave
 * @property int $nivel
 * @property int $id_usuario
 * @property string|null $nombre
 * @property int|null $id_empresa
 * @property string|null $correo
 * @property string|null $menureporte
 * @property string|null $primer_nombre
 * @property string|null $segundo_nombre
 * @property string|null $primer_apellido
 * @property string|null $segundo_apellido
 * @property int|null $cedula
 * @property bool|null $estado
 * @property int $id_idusuario
 * @property string|null $creado
 * @property string|null $modificado
 * @property string|null $cargo
 * @property string|null $foto
 * @property int|null $estado_session
 * @property string|null $ultima_actividad
 * @property string|null $observaciones
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereCargo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereCedula($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereClave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereCorreo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereCreado($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereEstadoSession($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereIdEmpresa($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereIdIdusuario($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereIdUsuario($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereMenureporte($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereModificado($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereNivel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereObservaciones($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog wherePrimerApellido($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog wherePrimerNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereSegundoApellido($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereSegundoNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereUltimaActividad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereUsuario($value)
 */
	class UserLog extends \Eloquent {}
}

namespace App{
/**
 * App\Vehicle
 *
 * @property int $id
 * @property string $plate
 * @property string $number
 * @property int $company_id
 * @property bool $active
 * @property bool $in_repair
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Company $company
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Vehicle active()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Vehicle whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Vehicle whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Vehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Vehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Vehicle whereInRepair($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Vehicle whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Vehicle wherePlate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Vehicle whereUpdatedAt($value)
 */
	class Vehicle extends \Eloquent {}
}

