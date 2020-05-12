<?php

namespace App\Models\Vehicles;

use App\Models\Routes\CurrentDispatchRegister;
use Carbon\Carbon;
use Dompdf\Exception;
use Eloquent;
use Facade\Ignition\Exceptions\ViewException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Psr\Log\InvalidArgumentException;

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
class CurrentLocation extends Model
{
    protected $dates = ['date'];

    function getDateFormat()
    {
        return config('app.date_time_format');
    }

    public function getDateAttribute($date)
    {
        return Carbon::createFromFormat(config('app.simple_date_time_format'),explode('.',Str::replaceArray('T', [' '], $date))[0]);
    }

    public function dispatchRegister()
    {
        return $this->belongsTo(CurrentDispatchRegister::class, 'dispatch_register_id', 'dispatch_register_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getAPIFields()
    {
        return (object)[
            'id' => $this->id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'orientation' => $this->orientation,
            'current_mileage' => number_format($this->current_mileage/1000, 2, ',', '.'),
            'speed' => $this->speed,
        ];
    }

    public function vehicleStatus()
    {
        return $this->belongsTo(VehicleStatus::class, 'vehicle_status_id', 'id_status');
    }

    public function scopeWhereVehicle($query, Vehicle $vehicle)
    {
        return $query->where('vehicle_id', $vehicle->id)->get()->first();
    }
}
