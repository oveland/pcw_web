<?php

namespace App\Models\Vehicles;

use App\Http\Controllers\Utils\Geolocation;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\Report;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\Location
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
 * @property-read \App\Models\Routes\Report $report
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location whereVersion($value)
 * @mixin \Eloquent
 * @property int|null $vehicle_status_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location whereVehicleStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatchRegister witOffRoads()
 * @property bool|null $speeding
 * @property-read \App\Models\Routes\DispatchRegister|null $dispatchRegister
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location validCoordinates()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location whereSpeeding($value)
 * @property float|null $current_mileage
 * @property-read mixed $time
 * @property-read \App\Models\Vehicles\Vehicle|null $vehicle
 * @property-read \App\Models\Vehicles\VehicleStatus|null $vehicleStatus
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location whereCurrentMileage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Location witSpeeding()
 * @property-read \App\Models\Vehicles\AddressLocation $addressLocation
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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function report()
    {
        return $this->hasOne(Report::class, 'location_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
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
        }

        return $addressLocation ? $addressLocation->address : $address;
    }
}
