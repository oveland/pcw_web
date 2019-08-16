<?php

namespace App\Models\Vehicles;

use App\Http\Controllers\Utils\Geolocation;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\Report;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
 */
class Location extends Model
{
    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';

    protected $fillable = ['vehicle_id', 'date', 'latitude', 'longitude', 'orientation', 'odometer', 'status', 'speed', 'speeding', 'vehicle_status_id', 'distance', 'dispatch_register_id', 'off_road'];

    function getDateFormat()
    {
        return config('app.date_time_format');
    }

    public function getDateAttribute($date)
    {
        return Carbon::createFromFormat(config('app.simple_date_time_format'), explode('.', $date)[0]);
    }

    public function getTimeAttribute()
    {
        return $this->getDateAttribute($this->attributes['date']);
    }

    /**
     * @return HasOne
     */
    public function report()
    {
        return $this->hasOne(Report::class, 'location_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class);
    }

    /**
     * Check valid coordinates
     *
     * @return bool
     */
    public function isValid()
    {
        return ($this->latitude != 0 && $this->longitude != 0) ? true : false;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeValidCoordinates($query)
    {
        return $query->where('latitude', '<>', 0)->where('longitude', '<>', 0);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeWitOffRoads($query)
    {
        return $query->where('off_road', true);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeWitSpeeding($query)
    {
        return $query->where('speeding', true);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function vehicleStatus()
    {
        return $this->belongsTo(VehicleStatus::class, 'vehicle_status_id', 'id_status');
    }

    public function getSpeedAttribute($speed)
    {
        $thresholdTruncateSpeeding = config('vehicle.threshold_truncate_speeding');
        return intval(($speed > $thresholdTruncateSpeeding) ? $thresholdTruncateSpeeding : $speed);
    }

    public function isTruncated()
    {
        $speed = $this->speed;
        $thresholdTruncateSpeeding = config('vehicle.threshold_truncate_speeding');
        return ($speed > $thresholdTruncateSpeeding);
    }

    public function isFakeOffRoad()
    {
        return $this->status == 'FOR';
    }

    public function isTrueOffRoad()
    {
        return (!$this->isFakeOffRoad() && $this->off_road);
    }

    /**
     * @return HasOne
     */
    public function addressLocation()
    {
        return $this->hasOne(AddressLocation::class);
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
            sleep(0.1);
        }

        return $addressLocation ? $addressLocation->address : $address;
    }

    public function getAPIFields()
    {
        return (object)[
            'id' => $this->id,
            'date' => $this->date->toDateTimeString(),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'orientation' => $this->orientation,
            'speed' => $this->speed,
            'odometer' => $this->odometer,
            'offRoad' => $this->off_road,
            'speeding' => $this->speeding,
            'vehicleStatus' => $this->vehicleStatus,
        ];
    }
}
