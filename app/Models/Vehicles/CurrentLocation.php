<?php

namespace App\Models\Vehicles;

use App\Http\Controllers\Utils\Geolocation;
use App\Models\Routes\CurrentDispatchRegister;
use App\Models\Routes\DispatchRegister;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * App\Models\Vehicles\CurrentLocation
 *
 * @property int $id
 * @property int|null $version
 * @property Carbon|null $date
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
 * @property-read DispatchRegister|null $dispatchRegister
 * @property-read Vehicle|null $vehicle
 * @property float|null $yesterday_odometer
 * @property float|null $current_mileage
 * @property-read VehicleStatus|null $vehicleStatus
 * @method static Builder|CurrentLocation whereCurrentMileage($value)
 * @method static Builder|CurrentLocation whereYesterdayOdometer($value)
 * @property bool|null $speeding
 * @method static Builder|CurrentLocation whereSpeeding($value)
 * @property int|null $location_id
 * @property string|null $ard_off_road
 * @property int|null $jumps
 * @property int|null $total_locations
 * @method static Builder|CurrentLocation whereArdOffRoad($value)
 * @method static Builder|CurrentLocation whereJumps($value)
 * @method static Builder|CurrentLocation whereLocationId($value)
 * @method static Builder|CurrentLocation whereTotalLocations($value)
 * @property-read AddressLocation $addressLocation
 * @property-read AddressLocation $mileageRouteInKm
 * @property-read CurrentDispatchRegister|null $currentDispatchRegister
 * @property int|null $mileage_route
 * @method static Builder|CurrentLocation whereCurrentMileageRoute($value)
 * @property-read mixed $mileage_route_in_km
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocation whereMileageRoute($value)
 */
class CurrentLocation extends Model
{
    protected $dates = ['date'];
    public $timestamps = false;

    protected $fillable = ['vehicle_id', 'date', 'latitude', 'longitude', 'orientation', 'odometer', 'mileage_route', 'current_mileage', 'speed', 'vehicle_status_id', 'date_created', 'last_updated'];

    protected function getDateFormat()
    {
        return config('app.date_time_format');
    }

    /**
     * @param $date
     * @return Carbon
     */
    public function getDateAttribute($date)
    {
        if (Str::contains($date, '-')) {
            return Carbon::createFromFormat('Y-m-d H:i:s', explode('.', $date)[0]);
        }

        return Carbon::createFromFormat(config('app.simple_date_time_format'), explode('.', $date)[0]);
    }

    public function currentDispatchRegister()
    {
        return $this->belongsTo(CurrentDispatchRegister::class, 'dispatch_register_id', 'dispatch_register_id');
    }

    public function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class, 'dispatch_register_id', 'id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getAPIFields()
    {
        return (object)[
            'id' => $this->id,
            'speed' => $this->speed,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'orientation' => $this->orientation,
            'date' => $this->date->toDateTimeString(),
            'currentStatus' => $this->vehicleStatus->des_status,
            'currentMileage' => number_format($this->current_mileage / 1000, 2, ',', '.'),
        ];
    }

    public function vehicleStatus()
    {
        return $this->belongsTo(VehicleStatus::class, 'vehicle_status_id', 'id_status');
    }

    public function scopeWhereVehicle($query, Vehicle $vehicle)
    {
        $currentLocation = $query->where('vehicle_id', $vehicle->id)->get()->first();

        if (!$currentLocation) {
            $currentLocation = CurrentLocation::create([
                'vehicle_id' => $vehicle->id,
                'date' => Carbon::createFromFormat('Y-m-d H:i:s', '2020-01-01 00:00:00'),
                'latitude' => 3.4529876122683594,
                'longitude' => -76.51858507170815,
                'orientation' => 0,
                'odometer' => 0,
                'current_mileage' => 0,
                'speed' => 0,
                'vehicle_status_id' => 1,
                'date_created' => Carbon::now(),
                'last_updated' => Carbon::now()
            ]);
        }

        return $currentLocation;
    }

    /**
     * @return HasOne
     */
    public function addressLocation()
    {
        return $this->hasOne(AddressLocation::class, 'location_id', 'id');
    }

    public function getAddress($refresh = false)
    {
        $addressLocation = $this->addressLocation;

        $address = "";

        if ($refresh && !$addressLocation) {
            $address = Geolocation::getAddressFromCoordinates($this->latitude, $this->longitude);
            $this->addressLocation()->create([
                'address' => $address,
                'status' => 0,
            ]);
        }

        return $addressLocation ? $addressLocation->address : $address;
    }

    public function getMileageRouteInKmAttribute()
    {
        return intval($this->mileage_route / 1000);
    }
}
