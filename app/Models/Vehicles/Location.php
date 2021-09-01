<?php

namespace App\Models\Vehicles;

use App\Http\Controllers\Utils\Geolocation;
use App\Models\Passengers\Passenger;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\Report;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait BindsDynamically
{
    protected $connection = null;
    protected $table = null;

    public function bind(string $connection, string $table)
    {
        $this->setConnection($connection);
        $this->setTable($table);
    }

    public function newInstance($attributes = [], $exists = false)
    {
        // Overridden in order to allow for late table binding.

        $model = parent::newInstance($attributes, $exists);
        $model->setTable($this->table);

        return $model;
    }

    public function scopeForDate(Builder $query, $withDate)
    {
        $tableName = 'locations';

        $withDate = explode(' ', $withDate)[0];

        $format = Str::contains($withDate, "-") ? 'Y-m-d' : 'd/m/Y';
        $date = Carbon::createFromFormat($format, $withDate);

        $diffDays = Carbon::now()->diffInDays($date);

        if ($diffDays == 0) {
//            $tableName .= "_$diffDays";
        } else {
            $indexView = floor(($diffDays - 1) / 5) + 1;

            if ($indexView <= 6) {
                $tableName .= "_$indexView";
            }
        }

        $this->setTable($tableName);

        return $query->from($tableName);
    }
}

/**
 * App\Models\Vehicles\Location
 *
 * @property int $id
 * @property int $version
 * @property string|null| Carbon $date
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
 * @method static Builder|Location witOffRoads()
 * @method static Builder|Location forDate($date)
 * @property bool|null $speeding
 * @property-read DispatchRegister|null $dispatchRegister
 * @method static Builder|Location validCoordinates()
 * @method static Builder|Location whereSpeeding($value)
 * @property float|null $current_mileage
 * @property-read mixed $time
 * @property-read Vehicle|null $vehicle
 * @property-read VehicleStatus|null $vehicleStatus
 * @method static Builder|Location whereCurrentMileage($value)
 * @method static Builder|Location withSpeeding()
 * @property-read AddressLocation $addressLocation
 * @property-read Passenger $passenger
 * @property string|null $ard_off_road
 * @method static Builder|Location whereArdOffRoad($value)
 * @property-read PhotoLocation $photo
 * @property-read PhotoLocation[]|Collection $photos
 */
class Location extends Model
{
    use BindsDynamically;

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';

    protected $fillable = ['vehicle_id', 'date', 'latitude', 'longitude', 'orientation', 'odometer', 'status', 'speed', 'speeding', 'vehicle_status_id', 'distance', 'dispatch_register_id', 'off_road'];

    protected function getDateFormat()
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
        return $this->belongsTo(DispatchRegister::class, 'dispatch_register_id', 'id')->active();
    }

    /**
     * Check valid coordinates
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->latitude != 0 && $this->longitude != 0;
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
    public function scopeWithSpeeding($query)
    {
        return $query->where('speeding', true);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
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
        return $this->hasOne(AddressLocation::class, 'location_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function passenger()
    {
        return $this->hasOne(Passenger::class, 'location_id', 'id')->orderByDesc('id');
    }

    public function getAddress($refresh = false, $force = false)
    {
        $addressLocation = $this->addressLocation;
//        return "";

        if ($refresh || !$addressLocation || !$addressLocation->address) {
            $address = Geolocation::getAddressFromCoordinates($this->latitude, $this->longitude, $force);
            if ($addressLocation) {
                $addressLocation->address = $address;
                $addressLocation->save();
            } else {
                $this->addressLocation()->create([
                    'address' => $address,
                    'status' => 0,
                ]);
            }
        }

        return $addressLocation ? $addressLocation->address : $address;
    }

    public function getTotalOffRoad($routeId)
    {
        $routeId = $routeId ? $routeId : ($this->dispatch_register_id ? $this->dispatchRegister->route->id : 'empty');

        $ardOffRoad = $this->ard_off_road ? json_decode($this->ard_off_road, true) : [];

        return isset($ardOffRoad[$routeId]) ? $ardOffRoad[$routeId]['tt'] : 0;
    }

    public function photo()
    {
        return $this->hasOne(PhotoLocation::class);
    }

    public function photos()
    {
        return $this->hasMany(PhotoLocation::class)->orderBy('side');
    }
}
