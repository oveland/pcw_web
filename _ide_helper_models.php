<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App{
/**
 * App\LastLocation
 *
 * @property int $id
 * @property int|null $version
 * @property string|null $date
 * @property string|null $date_created
 * @property int|null $dispatch_register_id
 * @property float|null $distance
 * @property string|null $last_updated
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $odometer
 * @property bool|null $off_road
 * @property float|null $orientation
 * @property int|null $reference_location_id
 * @property float|null $speed
 * @property string|null $status
 * @property int|null $vehicle_id
 * @property int|null $vehicle_status_id
 * @property float|null $yesterday_odometer
 * @property float|null $current_mileage
 * @property bool|null $speeding
 * @method static Builder|\App\LastLocation whereCurrentMileage($value)
 * @method static Builder|\App\LastLocation whereDate($value)
 * @method static Builder|\App\LastLocation whereDateCreated($value)
 * @method static Builder|\App\LastLocation whereDispatchRegisterId($value)
 * @method static Builder|\App\LastLocation whereDistance($value)
 * @method static Builder|\App\LastLocation whereId($value)
 * @method static Builder|\App\LastLocation whereLastUpdated($value)
 * @method static Builder|\App\LastLocation whereLatitude($value)
 * @method static Builder|\App\LastLocation whereLongitude($value)
 * @method static Builder|\App\LastLocation whereOdometer($value)
 * @method static Builder|\App\LastLocation whereOffRoad($value)
 * @method static Builder|\App\LastLocation whereOrientation($value)
 * @method static Builder|\App\LastLocation whereReferenceLocationId($value)
 * @method static Builder|\App\LastLocation whereSpeed($value)
 * @method static Builder|\App\LastLocation whereSpeeding($value)
 * @method static Builder|\App\LastLocation whereStatus($value)
 * @method static Builder|\App\LastLocation whereVehicleId($value)
 * @method static Builder|\App\LastLocation whereVehicleStatusId($value)
 * @method static Builder|\App\LastLocation whereVersion($value)
 * @method static Builder|\App\LastLocation whereYesterdayOdometer($value)
 * @mixin Eloquent
 * @property-read Vehicle|null $vehicle
 * @method static Builder|\App\LastLocation newModelQuery()
 * @method static Builder|\App\LastLocation newQuery()
 * @method static Builder|\App\LastLocation query()
 * @property bool|null $vehicle_active
 * @property bool|null $vehicle_in_repair
 * @property int|null $jumps
 * @property int|null $total_locations
 * @property-read Collection|ReportVehicleStatus[] $reportVehicleStatus
 * @method static Builder|\App\LastLocation whereJumps($value)
 * @method static Builder|\App\LastLocation whereTotalLocations($value)
 * @method static Builder|\App\LastLocation whereVehicleActive($value)
 * @method static Builder|\App\LastLocation whereVehicleInRepair($value)
 */
	class LastLocation extends \Eloquent {}
}

namespace App\Models\Drivers{
/**
 * App\Models\Drivers\Driver
 *
 * @property int $id
 * @property string|null $code
 * @property string|null $identity
 * @property string|null $first_name
 * @property string|null $second_name
 * @property string|null $last_name
 * @property bool|null $active
 * @property int $company_id
 * @property int $bea_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereIdentity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereSecondName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver withCode($code)
 * @property-read mixed $full_name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver active()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereBeaId($value)
 * @property string|null $phone
 * @property string|null $cellphone
 * @property string|null $address
 * @property string|null $email
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereCellphone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver wherePhone($value)
 */
	class Driver extends \Eloquent {}
}

namespace App\Models\Passengers{
/**
 * App\Models\Passengers\CobanPhoto
 *
 * @property int $id
 * @property string $date
 * @property int $vehicle_id
 * @property int|null $location_id
 * @property int|null $dispatch_register_id
 * @property float $latitude
 * @property float $longitude
 * @property float $speed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|CobanPhoto newModelQuery()
 * @method static Builder|CobanPhoto newQuery()
 * @method static Builder|CobanPhoto query()
 * @method static Builder|CobanPhoto whereCreatedAt($value)
 * @method static Builder|CobanPhoto whereDate($value)
 * @method static Builder|CobanPhoto whereDispatchRegisterId($value)
 * @method static Builder|CobanPhoto whereId($value)
 * @method static Builder|CobanPhoto whereLatitude($value)
 * @method static Builder|CobanPhoto whereLocationId($value)
 * @method static Builder|CobanPhoto whereLongitude($value)
 * @method static Builder|CobanPhoto whereSpeed($value)
 * @method static Builder|CobanPhoto whereUpdatedAt($value)
 * @method static Builder|CobanPhoto whereVehicleId($value)
 * @mixin Eloquent
 * @property-read DispatchRegister $dispatchRegister
 * @property-read Location $location
 * @property-read Collection|CobanPhotoPackage[] $packages
 * @property-read Vehicle $vehicle
 */
	class CobanPhoto extends \Eloquent {}
}

namespace App\Models\Passengers{
/**
 * App\Models\Passengers\CobanPhotoPackage
 *
 * @property int $id
 * @property int $photo_id
 * @property int $package_id
 * @property int $package_length
 * @property string $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CobanPhotoPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CobanPhotoPackage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CobanPhotoPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CobanPhotoPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CobanPhotoPackage whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CobanPhotoPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CobanPhotoPackage wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CobanPhotoPackage wherePackageLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CobanPhotoPackage wherePhotoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CobanPhotoPackage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class CobanPhotoPackage extends \Eloquent {}
}

namespace App\Models\Passengers{
/**
 * App\Models\Passengers\Passenger
 *
 * @property int $id
 * @property Carbon $date
 * @property int $total
 * @property int $total_prev
 * @property int $vehicle_id
 * @property int|null $dispatch_register_id
 * @property int|null $location_id
 * @property int|null $counter_issue_id
 * @property float $latitude
 * @property float $longitude
 * @property string $frame
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $total_platform
 * @property int|null $vehicle_status_id
 * @property-read CounterIssue|null $counterIssue
 * @property-read DispatchRegister|null $dispatchRegister
 * @property-read mixed $hex_seats
 * @property-read Vehicle $vehicle
 * @property-read VehicleStatus|null $vehicleStatus
 * @method static Builder|Passenger findAllByDateRange($vehicleId, $initialDate, $finalDate)
 * @method static Builder|Passenger findAllByRoundTrip($vehicleId, $routeId, $roundTrip, $date)
 * @method static Builder|Passenger whereCounterIssueId($value)
 * @method static Builder|Passenger whereCreatedAt($value)
 * @method static Builder|Passenger whereDate($value)
 * @method static Builder|Passenger whereDispatchRegisterId($value)
 * @method static Builder|Passenger whereFrame($value)
 * @method static Builder|Passenger whereId($value)
 * @method static Builder|Passenger whereLatitude($value)
 * @method static Builder|Passenger whereLocationId($value)
 * @method static Builder|Passenger whereLongitude($value)
 * @method static Builder|Passenger whereTotal($value)
 * @method static Builder|Passenger whereTotalPlatform($value)
 * @method static Builder|Passenger whereTotalPrev($value)
 * @method static Builder|Passenger whereUpdatedAt($value)
 * @method static Builder|Passenger whereVehicleId($value)
 * @method static Builder|Passenger whereVehicleStatusId($value)
 * @mixin \Eloquent
 * @property int|null $total_sensor_recorder
 * @property int|null $total_front_sensor
 * @property int|null $total_back_sensor
 * @method static Builder|Passenger whereTotalBackSensor($value)
 * @method static Builder|Passenger whereTotalFrontSensor($value)
 * @method static Builder|Passenger whereTotalSensorRecorder($value)
 * @property int|null $fringe_id
 * @method static Builder|Passenger whereFringeId($value)
 * @method static Builder|Passenger newModelQuery()
 * @method static Builder|Passenger newQuery()
 * @method static Builder|Passenger query()
 * @property mixed hexSeats
 * @property int|null $history_seat_id
 * @method static Builder|Passenger whereHistorySeatId($value)
 */
	class Passenger extends \Eloquent {}
}

namespace App\Models\Passengers{
/**
 * App\Models\Passengers\HistorySeat
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereActiveKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereActiveLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereActiveLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereActiveTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereBusyKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereBusyTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereComplete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereInactiveKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereInactiveLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereInactiveLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereInactiveTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat wherePlate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereSeat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat query()
 */
	class HistorySeat extends \Eloquent {}
}

namespace App\Models\Passengers{
/**
 * App\Models\Passengers\CounterIssue
 *
 * @property int $id
 * @property string $date
 * @property int $total
 * @property int $total_prev
 * @property int $vehicle_id
 * @property int|null $dispatch_register_id
 * @property float $latitude
 * @property float $longitude
 * @property string $frame
 * @property string|null $items_issues
 * @property string|null $raspberry_cameras_issues
 * @property string|null $raspberry_check_counter_issue
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereFrame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereItemsIssues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereRaspberryCamerasIssues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereRaspberryCheckCounterIssue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereTotalPrev($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereVehicleId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Routes\DispatchRegister|null $dispatchRegister
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue query()
 */
	class CounterIssue extends \Eloquent {}
}

namespace App\Models\Users{
/**
 * App\Models\Users\User
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
 * @property-read \App\Models\Company\Company|null $company
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereUsername($value)
 * @mixin \Eloquent
 * @property int $role_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User query()
 * @property string|null $vehicle_tags
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereVehicleTags($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models\Users{
/**
 * App\Models\Users\AccessLog
 *
 * @property int $id
 * @property string|null $date
 * @property string|null $time
 * @property string|null $user_id
 * @property-read \App\Models\Users\UserLog|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\AccessLog whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\AccessLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\AccessLog whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\AccessLog whereUserId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\AccessLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\AccessLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\AccessLog query()
 */
	class AccessLog extends \Eloquent {}
}

namespace App\Models\Users{
/**
 * App\Models\Users\UserLog
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereCargo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereCedula($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereClave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereCorreo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereCreado($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereEstadoSession($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereIdEmpresa($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereIdIdusuario($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereIdUsuario($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereMenureporte($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereModificado($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereNivel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereObservaciones($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog wherePrimerApellido($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog wherePrimerNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereSegundoApellido($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereSegundoNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereUltimaActividad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereUsuario($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog query()
 * @property string|null $vehicle_tags
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereVehicleTags($value)
 */
	class UserLog extends \Eloquent {}
}

namespace App\Models\Proprietaries{
/**
 * App\Models\Proprietaries\ProprietaryVehicle
 *
 * @property int $id
 * @property int $vehicle_id
 * @property int $proprietary_id
 * @property bool $active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Proprietaries\Proprietary $proprietary
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\ProprietaryVehicle whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\ProprietaryVehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\ProprietaryVehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\ProprietaryVehicle whereProprietaryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\ProprietaryVehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\ProprietaryVehicle whereVehicleId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\ProprietaryVehicle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\ProprietaryVehicle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\ProprietaryVehicle query()
 */
	class ProprietaryVehicle extends \Eloquent {}
}

namespace App\Models\Proprietaries{
/**
 * App\Proprietaries\Proprietary
 *
 * @property int $id
 * @property string|null $first_name
 * @property string|null $second_name
 * @property string|null $surname
 * @property string|null $second_surname
 * @property string|null $phone
 * @property string|null $cellphone
 * @property string|null $address
 * @property string|null $email
 * @property bool|null $active
 * @property bool|null $passenger_report_via_sms
 * @property string|null $company_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Proprietaries\ProprietaryVehicle[] $assignedVehicles
 * @property-read \App\Models\Company\Company|null $company
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereCellphone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary wherePassengerReportViaSms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereSecondName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereSecondSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereSurname($value)
 * @mixin \Eloquent
 * @property-read mixed $simple_name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary query()
 * @property string|null $identity
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereIdentity($value)
 */
	class Proprietary extends \Eloquent {}
}

namespace App\Models\BEA{
/**
 * App\Models\BEA\Penalty
 *
 * @property int $id
 * @property int $route_id
 * @property string $type
 * @property int $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Penalty newModelQuery()
 * @method static Builder|Penalty newQuery()
 * @method static Builder|Penalty query()
 * @method static Builder|Penalty whereCreatedAt($value)
 * @method static Builder|Penalty whereId($value)
 * @method static Builder|Penalty whereRouteId($value)
 * @method static Builder|Penalty whereType($value)
 * @method static Builder|Penalty whereUpdatedAt($value)
 * @method static Builder|Penalty whereValue($value)
 * @mixin Eloquent
 * @property-read Route $route
 * @property-read \App\Models\BEA\Mark $mark
 * @property int $mark_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\MarkPenalty whereMarkId($value)
 */
	class MarkPenalty extends \Eloquent {}
}

namespace App\Models\BEA{
/**
 * App\Models\BEA\Trajectory
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int|null $route_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory whereUpdatedAt($value)
 * @property-read \App\Models\Routes\Route $route
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory whereRouteId($value)
 */
	class Trajectory extends \Eloquent {}
}

namespace App\Models\BEA{
/**
 * App\Models\BEA\Discount
 *
 * @method static Builder|Discount newModelQuery()
 * @method static Builder|Discount newQuery()
 * @method static Builder|Discount query()
 * @mixin Eloquent
 * @property int $id
 * @property int $discount_type_id
 * @property int $route_id
 * @property int $trajectory_id
 * @property int $vehicle_id
 * @property int $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DiscountType $discountType
 * @property-read Route $route
 * @property-read Trajectory $trajectory
 * @property-read Vehicle $vehicle
 * @method static Builder|Discount whereCreatedAt($value)
 * @method static Builder|Discount whereDiscountTypeId($value)
 * @method static Builder|Discount whereId($value)
 * @method static Builder|Discount whereRouteId($value)
 * @method static Builder|Discount whereTrajectoryId($value)
 * @method static Builder|Discount whereUpdatedAt($value)
 * @method static Builder|Discount whereValue($value)
 * @method static Builder|Discount whereVehicleId($value)
 */
	class Discount extends \Eloquent {}
}

namespace App\Models\BEA{
/**
 * App\Models\BEA\Taking
 *
 * @method static Builder|Taking newModelQuery()
 * @method static Builder|Taking newQuery()
 * @method static Builder|Taking query()
 * @mixin Eloquent
 * @property-read Liquidation $liquidation
 * @property-read User $user
 * @property int $id
 * @property string $date
 * @property int|null $liquidation_id
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Taking whereCreatedAt($value)
 * @method static Builder|Taking whereDate($value)
 * @method static Builder|Taking whereId($value)
 * @method static Builder|Taking whereLiquidationId($value)
 * @method static Builder|Taking whereUpdatedAt($value)
 * @method static Builder|Taking whereUserId($value)
 */
	class Taking extends \Eloquent {}
}

namespace App\Models\BEA{
/**
 * App\Models\BEA\Penalty
 *
 * @property int $id
 * @property int $route_id
 * @property string $type
 * @property int $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Penalty newModelQuery()
 * @method static Builder|Penalty newQuery()
 * @method static Builder|Penalty query()
 * @method static Builder|Penalty whereCreatedAt($value)
 * @method static Builder|Penalty whereId($value)
 * @method static Builder|Penalty whereRouteId($value)
 * @method static Builder|Penalty whereType($value)
 * @method static Builder|Penalty whereUpdatedAt($value)
 * @method static Builder|Penalty whereValue($value)
 * @mixin Eloquent
 * @property-read Route $route
 */
	class Penalty extends \Eloquent {}
}

namespace App\Models\BEA{
/**
 * App\Models\BEA\Commission
 *
 * @property int $id
 * @property int $route_id
 * @property string $type
 * @property int $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Commission newModelQuery()
 * @method static Builder|Commission newQuery()
 * @method static Builder|Commission query()
 * @method static Builder|Commission whereCreatedAt($value)
 * @method static Builder|Commission whereId($value)
 * @method static Builder|Commission whereRouteId($value)
 * @method static Builder|Commission whereType($value)
 * @method static Builder|Commission whereUpdatedAt($value)
 * @method static Builder|Commission whereValue($value)
 * @mixin Eloquent
 * @property-read Route $route
 */
	class Commission extends \Eloquent {}
}

namespace App\Models\BEA{
/**
 * App\Models\BEA\DiscountType
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $icon
 * @property int $default
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|DiscountType newModelQuery()
 * @method static Builder|DiscountType newQuery()
 * @method static Builder|DiscountType query()
 * @method static Builder|DiscountType whereCreatedAt($value)
 * @method static Builder|DiscountType whereDefault($value)
 * @method static Builder|DiscountType whereDescription($value)
 * @method static Builder|DiscountType whereIcon($value)
 * @method static Builder|DiscountType whereId($value)
 * @method static Builder|DiscountType whereName($value)
 * @method static Builder|DiscountType whereUpdatedAt($value)
 * @mixin Eloquent
 */
	class DiscountType extends \Eloquent {}
}

namespace App\Models\BEA{
/**
 * App\Models\BEA\Commission
 *
 * @property int $id
 * @property int $route_id
 * @property string $type
 * @property int $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Commission newModelQuery()
 * @method static Builder|Commission newQuery()
 * @method static Builder|Commission query()
 * @method static Builder|Commission whereCreatedAt($value)
 * @method static Builder|Commission whereId($value)
 * @method static Builder|Commission whereRouteId($value)
 * @method static Builder|Commission whereType($value)
 * @method static Builder|Commission whereUpdatedAt($value)
 * @method static Builder|Commission whereValue($value)
 * @mixin Eloquent
 * @property-read Route $route
 * @property int $mark_id
 * @method static Builder|MarkCommission whereMarkId($value)
 * @property-read \App\Models\BEA\Mark $mark
 */
	class MarkCommission extends \Eloquent {}
}

namespace App\Models\BEA{
/**
 * App\Models\BEA\DiscountType
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $icon
 * @property int $default
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|DiscountType newModelQuery()
 * @method static Builder|DiscountType newQuery()
 * @method static Builder|DiscountType query()
 * @method static Builder|DiscountType whereCreatedAt($value)
 * @method static Builder|DiscountType whereDefault($value)
 * @method static Builder|DiscountType whereDescription($value)
 * @method static Builder|DiscountType whereIcon($value)
 * @method static Builder|DiscountType whereId($value)
 * @method static Builder|DiscountType whereName($value)
 * @method static Builder|DiscountType whereUpdatedAt($value)
 * @mixin Eloquent
 */
	class MarkDiscountType extends \Eloquent {}
}

namespace App\Models\BEA{
/**
 * App\Models\BEA\Turn
 *
 * @property int $id
 * @property int $vehicle_id
 * @property int $route_id
 * @property int $driver_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Driver $driver
 * @property-read Route $route
 * @property-read Vehicle $vehicle
 * @method static Builder|Turn newModelQuery()
 * @method static Builder|Turn newQuery()
 * @method static Builder|Turn query()
 * @method static Builder|Turn whereCreatedAt($value)
 * @method static Builder|Turn whereDriverId($value)
 * @method static Builder|Turn whereId($value)
 * @method static Builder|Turn whereRouteId($value)
 * @method static Builder|Turn whereUpdatedAt($value)
 * @method static Builder|Turn whereVehicleId($value)
 * @mixin Eloquent
 * @property-read mixed $a_p_i
 */
	class Turn extends \Eloquent {}
}

namespace App\Models\BEA{
/**
 * App\Models\BEA\Mark
 *
 * @property int $id
 * @property int $turn_id
 * @property int $trajectory_id
 * @property Carbon|string $date
 * @property string $initial_time
 * @property string $final_time
 * @property int $passengers_up
 * @property int $passengers_down
 * @property int $locks
 * @property int $auxiliaries
 * @property int $boarded
 * @property int $im_bea_max
 * @property int $im_bea_min
 * @property int $total_bea
 * @property int $passengers_bea
 * @property bool $liquidated
 * @property string|null $liquidation_date
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Trajectory $trajectory
 * @method static Builder|Mark newModelQuery()
 * @method static Builder|Mark newQuery()
 * @method static Builder|Mark query()
 * @method static Builder|Mark whereAuxiliaries($value)
 * @method static Builder|Mark whereBoarded($value)
 * @method static Builder|Mark whereCreatedAt($value)
 * @method static Builder|Mark whereDate($value)
 * @method static Builder|Mark whereFinalTime($value)
 * @method static Builder|Mark whereId($value)
 * @method static Builder|Mark whereImBeaMax($value)
 * @method static Builder|Mark whereImBeaMin($value)
 * @method static Builder|Mark whereInitialTime($value)
 * @method static Builder|Mark whereLiquidated($value)
 * @method static Builder|Mark whereLiquidationDate($value)
 * @method static Builder|Mark whereLocks($value)
 * @method static Builder|Mark wherePassengersBea($value)
 * @method static Builder|Mark wherePassengersDown($value)
 * @method static Builder|Mark wherePassengersUp($value)
 * @method static Builder|Mark whereTotalBea($value)
 * @method static Builder|Mark whereTrajectoryId($value)
 * @method static Builder|Mark whereTurnId($value)
 * @method static Builder|Mark whereUpdatedAt($value)
 * @method static Builder|Mark whereUserId($value)
 * @mixin Eloquent
 * @property string|null $liquidated_date
 * @property-read Turn $turn
 * @method static Builder|Mark whereLiquidatedDate($value)
 * @property int|null $liquidation_id
 * @property bool $taken
 * @method static Builder|Mark whereLiquidationId($value)
 * @method static Builder|Mark whereTaken($value)
 * @property-read int $boarding
 * @property-read Object $commission
 * @property-read Discount[]|Collection $discounts
 * @property-read mixed $status
 * @property-read int $total_gross_bea
 * @property-read mixed $duration
 * @property-read Carbon initialTime
 * @property-read Carbon finalTime
 * @property-read Object $penalty
 * @property int $pay_fall
 * @property int $get_fall
 * @method static Builder|Mark whereExtra($value)
 * @method static Builder|Mark whereGetFall($value)
 * @method static Builder|Mark wherePayFall($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|MarkCommission[] $markCommissions
 * @property-read \Illuminate\Database\Eloquent\Collection|MarkDiscount[] $markDiscounts
 * @property-read \Illuminate\Database\Eloquent\Collection|MarkPenalty[] $markPenalties
 * @property int|null $number
 * @method static Builder|Mark whereNumber($value)
 */
	class Mark extends \Eloquent {}
}

namespace App\Models\BEA{
/**
 * App\Models\BEA\Liquidation
 *
 * @method static Builder|Liquidation newModelQuery()
 * @method static Builder|Liquidation newQuery()
 * @method static Builder|Liquidation query()
 * @mixin Eloquent
 * @property int $id
 * @property string $date
 * @property int $vehicle_id
 * @property string $liquidation
 * @property string $totals
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Mark[] $marks
 * @property-read User|null $user
 * @method static Builder|Liquidation whereCreatedAt($value)
 * @method static Builder|Liquidation whereDate($value)
 * @method static Builder|Liquidation whereId($value)
 * @method static Builder|Liquidation whereLiquidation($value)
 * @method static Builder|Liquidation whereTotals($value)
 * @method static Builder|Liquidation whereUpdatedAt($value)
 * @method static Builder|Liquidation whereUserId($value)
 * @method static Builder|Liquidation whereVehicleId($value)
 * @property-read mixed $total
 * @property-read Vehicle $vehicle
 * @property-read \Mark|null $first_mark
 * @property-read \Mark|null $last_mark
 * @property bool $taken
 * @property string|null $taken_date
 * @property int|null $taken_user_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Liquidation whereTaken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Liquidation whereTakenDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Liquidation whereTakenUserId($value)
 * @property-read \App\Models\Users\User $takingUser
 */
	class Liquidation extends \Eloquent {}
}

namespace App\Models\BEA{
/**
 * App\Models\BEA\Discount
 *
 * @method static Builder|Discount newModelQuery()
 * @method static Builder|Discount newQuery()
 * @method static Builder|Discount query()
 * @mixin Eloquent
 * @property int $id
 * @property int $discount_type_id
 * @property int $route_id
 * @property int $trajectory_id
 * @property int $vehicle_id
 * @property int $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DiscountType $discountType
 * @property-read Route $route
 * @property-read Trajectory $trajectory
 * @property-read Vehicle $vehicle
 * @method static Builder|Discount whereCreatedAt($value)
 * @method static Builder|Discount whereDiscountTypeId($value)
 * @method static Builder|Discount whereId($value)
 * @method static Builder|Discount whereRouteId($value)
 * @method static Builder|Discount whereTrajectoryId($value)
 * @method static Builder|Discount whereUpdatedAt($value)
 * @method static Builder|Discount whereValue($value)
 * @method static Builder|Discount whereVehicleId($value)
 * @property int $mark_id
 * @method static Builder|MarkDiscount whereMarkId($value)
 * @property-read Mark $mark
 */
	class MarkDiscount extends \Eloquent {}
}

namespace App\Models\Vehicles{
/**
 * App\Models\Vehicles\VehicleStatusReport
 *
 * @property int $id
 * @property string $date
 * @property string $time
 * @property int|null $vehicle_id
 * @property int|null $vehicle_status_id
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $orientation
 * @property float|null $speed
 * @property int|null $odometer
 * @property string|null $frame
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereFrame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereVehicleStatusId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Vehicles\VehicleStatus|null $status
 * @property-read \App\Models\Vehicles\Vehicle|null $vehicle
 * @property int|null $dispatch_register_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport query()
 */
	class VehicleStatusReport extends \Eloquent {}
}

namespace App\Models\Vehicles{
/**
 * App\Models\Vehicles\VehicleSeatDistribution
 *
 * @property int $id
 * @property int $vehicle_id
 * @property string $json_distribution
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|VehicleSeatDistribution whereCreatedAt($value)
 * @method static Builder|VehicleSeatDistribution whereId($value)
 * @method static Builder|VehicleSeatDistribution whereJsonDistribution($value)
 * @method static Builder|VehicleSeatDistribution whereUpdatedAt($value)
 * @method static Builder|VehicleSeatDistribution whereVehicleId($value)
 * @property int $vehicle_seat_topology_id
 * @method static Builder|VehicleSeatDistribution whereVehicleSeatTopologyId($value)
 * @method static Builder|VehicleSeatDistribution newModelQuery()
 * @method static Builder|VehicleSeatDistribution newQuery()
 * @method static Builder|VehicleSeatDistribution query()
 * @property-read VehicleSeatTopology $seatTopology
 * @property-read VehicleSeatTopology $topology
 * @mixin Eloquent
 */
	class VehicleSeatDistribution extends \Eloquent {}
}

namespace App\Models\Vehicles{
/**
 * App\Models\Vehicles\ParkingReport
 *
 * @property int $id
 * @property string $date
 * @property int|null $location_id
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $orientation
 * @property int|null $speed
 * @property float|null $odometer
 * @property int|null $report_id
 * @property int|null $dispatch_register_id
 * @property int|null $distancem
 * @property int|null $distancep
 * @property int|null $distanced
 * @property string|null $timem
 * @property string|null $timep
 * @property string|null $timed
 * @property float|null $status_in_minutes
 * @property float|null $control_point_id
 * @property float|null $fringe_id
 * @property int $vehicle_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereControlPointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereDistanced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereDistancep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereFringeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereTimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereTimem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereTimep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereVehicleId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Routes\ControlPoint|null $controlPoint
 * @property-read \App\Models\Routes\DispatchRegister|null $dispatchRegister
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport query()
 */
	class ParkingReport extends \Eloquent {}
}

namespace App\Models\Vehicles{
/**
 * App\Models\Vehicles\HistoryMarker
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereHora($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereIdGps($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereOrientacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereVelocidad($value)
 * @mixin \Eloquent
 * @property int|null $km_gps
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereKmGps($value)
 * @property string|null $frame
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereFrame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker query()
 */
	class HistoryMarker extends \Eloquent {}
}

namespace App\Models\Vehicles{
/**
 * App\Models\Vehicles\GpsVehicle
 *
 * @mixin Eloquent
 * @property int $id
 * @property int $vehicle_id
 * @property string $imei
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|GpsVehicle whereCreatedAt($value)
 * @method static Builder|GpsVehicle whereId($value)
 * @method static Builder|GpsVehicle whereImei($value)
 * @method static Builder|GpsVehicle whereUpdatedAt($value)
 * @method static Builder|GpsVehicle whereVehicleId($value)
 * @property-read Vehicle $vehicle
 * @property int|null $gps_type_id
 * @property int|null $report_period
 * @method static Builder|GpsVehicle whereGpsTypeId($value)
 * @method static Builder|GpsVehicle whereReportPeriod($value)
 * @method static Builder|GpsVehicle newModelQuery()
 * @method static Builder|GpsVehicle newQuery()
 * @method static Builder|GpsVehicle query()
 * @method static Builder|GpsVehicle findBySim($sim)
 * @property-read GPSType|null $type
 * @property string|null $tags
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GpsVehicle findByVehicleId($vehicle_id)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GpsVehicle whereTags($value)
 */
	class GpsVehicle extends \Eloquent {}
}

namespace App\Models\Vehicles{
/**
 * App\Models\Vehicles\CurrentLocation
 *
 * @property int $id
 * @property int|null $version
 * @property string|null $date
 * @property string $date_created
 * @property int|null $dispatch_register_id
 * @property float|null $distance
 * @property string $last_updated
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $odometer
 * @property bool|null $off_road
 * @property float|null $orientation
 * @property int|null $reference_location_id
 * @property int|null $speed
 * @property string|null $status
 * @property int|null $vehicle_id
 * @property int|null $vehicle_status_id
 * @method static Builder|CurrentLocation whereDate($value)
 * @method static Builder|CurrentLocation whereDateCreated($value)
 * @method static Builder|CurrentLocation whereDispatchRegisterId($value)
 * @method static Builder|CurrentLocation whereDistance($value)
 * @method static Builder|CurrentLocation whereId($value)
 * @method static Builder|CurrentLocation whereLastUpdated($value)
 * @method static Builder|CurrentLocation whereLatitude($value)
 * @method static Builder|CurrentLocation whereLongitude($value)
 * @method static Builder|CurrentLocation whereOdometer($value)
 * @method static Builder|CurrentLocation whereOffRoad($value)
 * @method static Builder|CurrentLocation whereOrientation($value)
 * @method static Builder|CurrentLocation whereReferenceLocationId($value)
 * @method static Builder|CurrentLocation whereSpeed($value)
 * @method static Builder|CurrentLocation whereStatus($value)
 * @method static Builder|CurrentLocation whereVehicleId($value)
 * @method static Builder|CurrentLocation whereVehicleStatusId($value)
 * @method static Builder|CurrentLocation whereVersion($value)
 * @method static Builder|CurrentLocation whereVehicle($vehicle)
 * @mixin Eloquent
 * @property-read CurrentDispatchRegister|null $dispatchRegister
 * @property-read Vehicle|null $vehicle
 * @property float|null $yesterday_odometer
 * @property float|null $current_mileage
 * @property-read VehicleStatus|null $vehicleStatus
 * @method static Builder|CurrentLocation whereCurrentMileage($value)
 * @method static Builder|CurrentLocation whereYesterdayOdometer($value)
 * @property bool|null $speeding
 * @method static Builder|CurrentLocation whereSpeeding($value)
 * @method static Builder|CurrentLocation newModelQuery()
 * @method static Builder|CurrentLocation newQuery()
 * @method static Builder|CurrentLocation query()
 * @property int|null $location_id
 * @property string|null $ard_off_road
 * @property int|null $jumps
 * @property int|null $total_locations
 * @method static Builder|CurrentLocation whereArdOffRoad($value)
 * @method static Builder|CurrentLocation whereJumps($value)
 * @method static Builder|CurrentLocation whereLocationId($value)
 * @method static Builder|CurrentLocation whereTotalLocations($value)
 */
	class CurrentLocation extends \Eloquent {}
}

namespace App\Models\Vehicles{
/**
 * App\Models\Vehicles\Speeding
 *
 * @property int $id
 * @property string|null $date
 * @property string|null $time
 * @property int|null $vehicle_id
 * @property int|null $speeed
 * @property float|null $latitude
 * @property float|null $longitude
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Speeding whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Speeding whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Speeding whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Speeding whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Speeding whereSpeeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Speeding whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Speeding whereVehicleId($value)
 * @mixin \Eloquent
 * @property int|null $speed
 * @property-read \App\Models\Vehicles\Vehicle|null $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Speeding whereSpeed($value)
 * @property int|null $dispatch_register_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Speeding whereDispatchRegisterId($value)
 * @property-read \App\Models\Routes\DispatchRegister|null $dispatchRegister
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Speeding newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Speeding newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Speeding query()
 */
	class Speeding extends \Eloquent {}
}

namespace App\Models\Vehicles{
/**
 * App\Models\Vehicles\ReportVehicleStatus
 *
 * @property int $id
 * @property string|null $date
 * @property string|null $time
 * @property string|null $date_time
 * @property int $vehicle_id
 * @property string $status
 * @property string $updated_by
 * @property string|null $observations
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus whereDateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus whereObservations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus whereVehicleId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus query()
 */
	class ReportVehicleStatus extends \Eloquent {}
}

namespace App\Models\Vehicles{
/**
 * App\Models\Vehicles\VehicleSeatTopology
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|VehicleSeatTopology whereCreatedAt($value)
 * @method static Builder|VehicleSeatTopology whereDescription($value)
 * @method static Builder|VehicleSeatTopology whereId($value)
 * @method static Builder|VehicleSeatTopology whereName($value)
 * @method static Builder|VehicleSeatTopology whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleSeatTopology newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleSeatTopology newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleSeatTopology query()
 */
	class VehicleSeatTopology extends \Eloquent {}
}

namespace App\Models\Vehicles{
/**
 * App\Models\Vehicles\CurrentParkingReport
 *
 * @property int $id
 * @property string $date
 * @property int|null $location_id
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $orientation
 * @property int|null $speed
 * @property float|null $odometer
 * @property int|null $report_id
 * @property int|null $dispatch_register_id
 * @property int|null $distancem
 * @property int|null $distancep
 * @property int|null $distanced
 * @property string|null $timem
 * @property string|null $timep
 * @property string|null $timed
 * @property float|null $status_in_minutes
 * @property float|null $control_point_id
 * @property float|null $fringe_id
 * @property int $vehicle_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereControlPointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereDistanced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereDistancep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereFringeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereTimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereTimem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereTimep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereVehicleId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport findByVehicleId($vehicle_id)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport query()
 */
	class CurrentParkingReport extends \Eloquent {}
}

namespace App\Models\Vehicles{
/**
 * App\Models\Vehicles\VehicleStatus
 *
 * @property int|null $id_status
 * @property string|null $des_status
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatus whereDesStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatus whereIdStatus($value)
 * @mixin \Eloquent
 * @property string|null $main_class
 * @property string|null $icon_class
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatus whereIconClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatus whereMainClass($value)
 * @property-read mixed $id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatus query()
 * @property int|null $order
 * @property bool|null $show_filter
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatus whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatus whereShowFilter($value)
 */
	class VehicleStatus extends \Eloquent {}
}

namespace App\Models\Vehicles{
/**
 * App\Models\Vehicles\Location
 *
 * @property int $id
 * @property int $version
 * @property string|null $date
 * @property Carbon $date_created
 * @property int|null $dispatch_register_id
 * @property float|null $distance
 * @property Carbon $last_updated
 * @property string|null $latitude
 * @property string|null $longitude
 * @property float|null $odometer
 * @property float|null $orientation
 * @property int|null $speed
 * @property string|null $status
 * @property int|null $vehicle_id
 * @property bool|null $off_road
 * @property-read Report $report
 * @method static Builder|Location whereDate($value)
 * @method static Builder|Location whereDateCreated($value)
 * @method static Builder|Location whereDispatchRegisterId($value)
 * @method static Builder|Location whereDistance($value)
 * @method static Builder|Location whereId($value)
 * @method static Builder|Location whereLastUpdated($value)
 * @method static Builder|Location whereLatitude($value)
 * @method static Builder|Location whereLongitude($value)
 * @method static Builder|Location whereOdometer($value)
 * @method static Builder|Location whereOffRoad($value)
 * @method static Builder|Location whereOrientation($value)
 * @method static Builder|Location whereSpeed($value)
 * @method static Builder|Location whereStatus($value)
 * @method static Builder|Location whereVehicleId($value)
 * @method static Builder|Location whereVersion($value)
 * @mixin Eloquent
 * @property int|null $vehicle_status_id
 * @method static Builder|Location whereVehicleStatusId($value)
 * @method static Builder|DispatchRegister witOffRoads()
 * @property bool|null $speeding
 * @property-read DispatchRegister|null $dispatchRegister
 * @method static Builder|Location validCoordinates()
 * @method static Builder|Location whereSpeeding($value)
 * @property float|null $current_mileage
 * @property-read mixed $time
 * @property-read Vehicle|null $vehicle
 * @property-read VehicleStatus|null $vehicleStatus
 * @method static Builder|Location whereCurrentMileage($value)
 * @method static Builder|Location witSpeeding()
 * @property-read AddressLocation $addressLocation
 * @method static Builder|Location newModelQuery()
 * @method static Builder|Location newQuery()
 * @method static Builder|Location query()
 * @property string|null $ard_off_road
 * @method static Builder|Location whereArdOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location withSpeeding()
 */
	class Location extends \Eloquent {}
}

namespace App\Models\Vehicles{
/**
 * App\Models\Vehicles\PeakAndPlate
 *
 * @property \Carbon\Carbon $date
 * @property int $id
 * @property int $week_day
 * @property int $vehicle_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PeakAndPlate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PeakAndPlate whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PeakAndPlate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PeakAndPlate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PeakAndPlate whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PeakAndPlate whereWeekDay($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PeakAndPlate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PeakAndPlate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PeakAndPlate query()
 */
	class PeakAndPlate extends \Eloquent {}
}

namespace App\Models\Vehicles{
/**
 * App\Models\Vehicles\AddressLocation
 *
 * @property int $id
 * @property int $location_id
 * @property string $address
 * @property int $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\AddressLocation whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\AddressLocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\AddressLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\AddressLocation whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\AddressLocation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\AddressLocation whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\AddressLocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\AddressLocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\AddressLocation query()
 */
	class AddressLocation extends \Eloquent {}
}

namespace App\Models\Vehicles{
/**
 * App\Models\Vehicles\SimGPS
 *
 * @property int $id
 * @property string $sim
 * @property string $operator
 * @property string $gps_type
 * @property int $vehicle_id
 * @property bool $active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS findByVehicleId($vehicle_id)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS findBySim($sim)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS whereGpsType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS whereOperator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS whereSim($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS whereVehicleId($value)
 * @mixin \Eloquent
 * @property-read mixed $date
 * @property-read \App\Models\Vehicles\GpsVehicle $gps
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS query()
 * @property-read \App\Models\Vehicles\GPSType $type
 */
	class SimGPS extends \Eloquent {}
}

namespace App\Models\Vehicles{
/**
 * App\Models\Vehicles\Vehicle
 *
 * @property int $id
 * @property string $plate
 * @property string $number
 * @property int $company_id
 * @property bool $active
 * @property bool $in_repair
 * @property int $bea_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @method static Builder|Vehicle active()
 * @method static Builder|Vehicle whereActive($value)
 * @method static Builder|Vehicle whereCompanyId($value)
 * @method static Builder|Vehicle whereCreatedAt($value)
 * @method static Builder|Vehicle whereId($value)
 * @method static Builder|Vehicle whereInRepair($value)
 * @method static Builder|Vehicle whereNumber($value)
 * @method static Builder|Vehicle wherePlate($value)
 * @method static Builder|Vehicle whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read SimGPS $simGPS
 * @property-read Collection|MaintenanceVehicle[] $maintenance
 * @property-read Collection|PeakAndPlate[] $peakAndPlate
 * @property-read mixed $number_and_plate
 * @property-read CurrentLocation $currentLocation
 * @property-read DispatcherVehicle $dispatcherVehicle
 * @property-read GpsVehicle $gpsVehicle
 * @property-read DispatcherVehicle $dispatcherVehicles
 * @method static Builder|Vehicle newModelQuery()
 * @method static Builder|Vehicle newQuery()
 * @method static Builder|Vehicle query()
 * @method static Builder|Vehicle whereBeaId($value)
 * @property-read VehicleSeatDistribution $seatDistribution
 * @property string|null $observations
 * @property int|null $proprietary_id
 * @property int|null $driver_id
 * @property string|null $tags
 * @method static Builder|Vehicle whereDriverId($value)
 * @method static Builder|Vehicle whereObservations($value)
 * @method static Builder|Vehicle whereProprietaryId($value)
 * @method static Builder|Vehicle whereTags($value)
 */
	class Vehicle extends \Eloquent {}
}

namespace App\Models\Vehicles{
/**
 * App\Models\Vehicles\GPSType
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $server_ip
 * @property string $server_port
 * @property string|null $reset_command
 * @property bool $active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType whereResetCommand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType whereServerIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType whereServerPort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $tags
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType whereTags($value)
 */
	class GPSType extends \Eloquent {}
}

namespace App\Models\Vehicles{
/**
 * App\Models\Vehicles\MaintenanceVehicle
 *
 * @property \Carbon\Carbon $date
 * @property int $id
 * @property int $week_day
 * @property int $vehicle_id
 * @property string $observations
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\MaintenanceVehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\MaintenanceVehicle whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\MaintenanceVehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\MaintenanceVehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\MaintenanceVehicle whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\MaintenanceVehicle whereWeekDay($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\MaintenanceVehicle whereObservations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\MaintenanceVehicle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\MaintenanceVehicle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\MaintenanceVehicle query()
 */
	class MaintenanceVehicle extends \Eloquent {}
}

namespace App\Models\Vehicles{
/**
 * App\Models\Vehicles\CurrentLocationsGPS
 *
 * @property int $id
 * @property string|null $date
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $orientation
 * @property float|null $speed
 * @property int|null $vehicle_status_id
 * @property string|null $date_vehicle_status
 * @property int|null $vehicle_id
 * @property string|null $vehicle_plate
 * @property-read \App\Models\Vehicles\VehicleStatus|null $vehicleStatus
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS findByVehicleId($vehicleId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereDateVehicleStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereVehiclePlate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereVehicleStatusId($value)
 * @mixin \Eloquent
 * @property string|null $time_period
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereTimePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS query()
 */
	class CurrentLocationsGPS extends \Eloquent {}
}

namespace App\Models\Routes{
/**
 * App\Models\Routes\Dispatch
 *
 * @property int $id
 * @property string $name
 * @property int $company_id
 * @property bool $active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Dispatch whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Dispatch whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Dispatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Dispatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Dispatch whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Dispatch whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property float $latitude
 * @property float $longitude
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Dispatch whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Dispatch whereLongitude($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Routes\DispatcherVehicle[] $dispatcherVehicles
 * @property int|null $radio_geofence
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Dispatch whereRadioGeofence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Dispatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Dispatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Dispatch query()
 */
	class Dispatch extends \Eloquent {}
}

namespace App\Models\Routes{
/**
 * App\Models\Routes\DispatcherVehicle
 *
 * @property int $id
 * @property string $date
 * @property int $day_type_id
 * @property int $dispatch_id
 * @property int $route_id
 * @property int $vehicle_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle whereDayTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle whereDispatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle whereVehicleId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Routes\Dispatch $dispatch
 * @property-read \App\Models\Routes\Route|null $route
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle active()
 * @property bool|null $default
 * @property bool|null $active
 * @property-read \App\Models\Routes\DispatcherVehicle $defaultDispatcherVehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle query()
 */
	class DispatcherVehicle extends \Eloquent {}
}

namespace App\Models\Routes{
/**
 * App\Models\Routes\Fringe
 *
 * @property int $id
 * @property string $name
 * @property string $from
 * @property string $to
 * @property bool $active
 * @property int $route_id
 * @property int $day_type_id
 * @property string $style_color
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DayType $dayType
 * @property-read Route $route
 * @method static Builder|Fringe whereActive($value)
 * @method static Builder|Fringe whereCreatedAt($value)
 * @method static Builder|Fringe whereDayTypeId($value)
 * @method static Builder|Fringe whereFrom($value)
 * @method static Builder|Fringe whereId($value)
 * @method static Builder|Fringe whereName($value)
 * @method static Builder|Fringe whereRouteId($value)
 * @method static Builder|Fringe whereTo($value)
 * @method static Builder|Fringe whereUpdatedAt($value)
 * @mixin Eloquent
 * @property int $sequence
 * @method static Builder|Fringe whereSequence($value)
 * @property-read Collection|ControlPointTime[] $controlPointTimes
 * @method static Builder|Fringe whereStyleColor($value)
 * @method static Builder|Fringe newModelQuery()
 * @method static Builder|Fringe newQuery()
 * @method static Builder|Fringe query()
 * @property string|null $uid
 * @method static Builder|Fringe whereUid($value)
 * @property string|null $time_from
 * @property string|null $time_to
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Fringe whereTimeFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Fringe whereTimeTo($value)
 */
	class Fringe extends \Eloquent {}
}

namespace App\Models\Routes{
/**
 * App\Models\Routes\RouteGoogle
 *
 * @property int $id_ruta
 * @property string|null $url
 * @property string|null $coordenadas
 * @method static Builder|RouteGoogle whereCoordenadas($value)
 * @method static Builder|RouteGoogle whereIdRuta($value)
 * @method static Builder|RouteGoogle whereUrl($value)
 * @mixin Eloquent
 * @method static Builder|RouteGoogle newModelQuery()
 * @method static Builder|RouteGoogle newQuery()
 * @method static Builder|RouteGoogle query()
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $file_name
 * @method static Builder|RouteGoogle whereCreatedAt($value)
 * @method static Builder|RouteGoogle whereFileName($value)
 * @method static Builder|RouteGoogle whereUpdatedAt($value)
 */
	class RouteGoogle extends \Eloquent {}
}

namespace App\Models\Routes{
/**
 * App\Models\Routes\ControlPointTimeReport
 *
 * @property-read \App\Models\Routes\ControlPoint $controlPoint
 * @property-read \App\Models\Routes\DispatchRegister $dispatchRegister
 * @property-read \App\Models\Routes\Fringe $fringe
 * @property-read \App\Models\Vehicles\Location $location
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 * @mixin \Eloquent
 * @property int $id
 * @property int $version
 * @property int|null $control_point_id
 * @property string $date
 * @property string $date_created
 * @property int $dispatch_register_id
 * @property int $distanced
 * @property int $distancem
 * @property int $distancep
 * @property int $fringe_id
 * @property string $last_updated
 * @property int $location_id
 * @property string $status
 * @property float $status_in_minutes
 * @property string $timed
 * @property string $timem
 * @property string $timep
 * @property int $vehicle_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereControlPointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereDistanced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereDistancep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereFringeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereTimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereTimem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereTimep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport query()
 */
	class ControlPointTimeReport extends \Eloquent {}
}

namespace App\Models\Routes{
/**
 * App\Models\Routes\DayType
 *
 * @property int $id
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DayType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DayType whereName($value)
 * @mixin \Eloquent
 * @property string|null $description
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DayType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DayType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DayType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DayType query()
 */
	class DayType extends \Eloquent {}
}

namespace App\Models\Routes{
/**
 * App\Models\Routes\ControlPoint
 *
 * @property-read \App\Models\Routes\Route $route
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereDistanceFromDispatch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereDistanceNextPoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereTrajectory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereUpdatedAt($value)
 * @property bool $reportable
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereReportable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint query()
 */
	class ControlPoint extends \Eloquent {}
}

namespace App\Models\Routes{
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
 */
	class DispatchRegister extends \Eloquent {}
}

namespace App\Models\Routes{
/**
 * App\Models\Routes\Route
 *
 * @property int $id
 * @property string $name
 * @property int $distance
 * @property int $road_time
 * @property string $url
 * @property int $company_id
 * @property int $dispatch_id
 * @property int $bea_id
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @property-read Collection|ControlPoint[] $controlPoints
 * @method static Builder|Route whereActive($value)
 * @method static Builder|Route whereCompanyId($value)
 * @method static Builder|Route whereCreatedAt($value)
 * @method static Builder|Route whereDispatchId($value)
 * @method static Builder|Route whereDistance($value)
 * @method static Builder|Route whereId($value)
 * @method static Builder|Route whereName($value)
 * @method static Builder|Route whereRoadTime($value)
 * @method static Builder|Route whereUpdatedAt($value)
 * @method static Builder|Route whereUrl($value)
 * @mixin Eloquent
 * @method static Builder|Route active()
 * @property-read Collection|Fringe[] $fringes
 * @property-read Collection|CurrentDispatchRegister[] $currentDispatchRegisters
 * @property-read Dispatch $dispatch
 * @property bool|null $as_group
 * @method static Builder|Route whereAsGroup($value)
 * @method static Builder|Route newModelQuery()
 * @method static Builder|Route newQuery()
 * @method static Builder|Route query()
 * @property string|null $min_route_time
 * @method static Builder|Route whereBeaId($value)
 * @method static Builder|Route whereMinRouteTime($value)
 * @property int|null $route_id
 * @property-read Collection|Route[] $subRoutes
 * @method static Builder|Route whereRouteId($value)
 * @property-read RouteGoogle $routeGoogle
 * @property int $distance_threshold
 * @property int $sampling_radius
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Route whereDistanceThreshold($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Route whereSamplingRadius($value)
 */
	class Route extends \Eloquent {}
}

namespace App\Models\Routes{
/**
 * App\Models\Routes\CurrentDispatchRegister
 *
 * @property int|null $dispatch_register_id
 * @property int|null $vehicle_id
 * @property string|null $plate
 * @property int|null $route_id
 * @property string|null $route_name
 * @property string|null $round_trip
 * @property string|null $departure_time
 * @property string|null $arrival_time
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister whereArrivalTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister whereDepartureTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister wherePlate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister whereRoundTrip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister whereRouteName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister whereVehicleId($value)
 * @mixin \Eloquent
 * @property string|null $date
 * @property string|null $driver_code
 * @property-read \App\Models\Vehicles\Vehicle|null $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister whereDriverCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister query()
 */
	class CurrentDispatchRegister extends \Eloquent {}
}

namespace App\Models\Routes{
/**
 * App\Models\Routes\ControlPointTime
 *
 * @property int $id
 * @property string $time
 * @property string $time_from_dispatch
 * @property string $time_next_point
 * @property int $day_type_id
 * @property int $control_point_id
 * @property int|null $fringe_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ControlPoint $controlPoint
 * @method static Builder|ControlPointTime whereControlPointId($value)
 * @method static Builder|ControlPointTime whereCreatedAt($value)
 * @method static Builder|ControlPointTime whereDayTypeId($value)
 * @method static Builder|ControlPointTime whereFringeId($value)
 * @method static Builder|ControlPointTime whereId($value)
 * @method static Builder|ControlPointTime whereTime($value)
 * @method static Builder|ControlPointTime whereTimeFromDispatch($value)
 * @method static Builder|ControlPointTime whereTimeNextPoint($value)
 * @method static Builder|ControlPointTime whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Fringe|null $fringe
 * @method static Builder|ControlPointTime newModelQuery()
 * @method static Builder|ControlPointTime newQuery()
 * @method static Builder|ControlPointTime query()
 * @property string|null $uid
 * @method static Builder|ControlPointTime whereUid($value)
 */
	class ControlPointTime extends \Eloquent {}
}

namespace App\Models\Routes{
/**
 * App\Models\Routes\Report
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
 * @property-read \App\Models\Routes\DispatchRegister $dispatchRegister
 * @property-read \App\Models\Vehicles\Location|null $location
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereDistanced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereDistancep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereTimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereTimem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereTimep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereVersion($value)
 * @mixin \Eloquent
 * @property int|null $control_point_id
 * @property int|null $fringe_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereControlPointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereFringeId($value)
 * @property-read \App\Models\Routes\ControlPoint|null $controlPoint
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report query()
 */
	class Report extends \Eloquent {}
}

namespace App\Models\Company{
/**
 * App\Models\Company\Company
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vehicles\Vehicle[] $activeVehicles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vehicles\Vehicle[] $vehicles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Drivers\Drivers[] $drivers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Drivers\Drivers[] $activeDrivers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Routes\Dispatch[] $dispatches
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
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Routes\Route[] $routes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereNit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company active()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company findAllActive()
 * @property string|null $timezone
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereTimezone($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Routes\Route[] $activeRoutes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Proprietaries\Proprietary[] $proprietaries
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company query()
 * @property int|null $speeding_threshold
 * @property int|null $max_speeding_threshold
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereMaxSpeedingThreshold($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereSpeedingThreshold($value)
 * @property string|null $default_kmz_url
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereDefaultKmzUrl($value)
 */
	class Company extends \Eloquent {}
}

