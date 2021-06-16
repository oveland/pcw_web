<?php

namespace App\Models\Vehicles\Binnacles;

use App\Models\Users\User;
use App\Models\Vehicles\CurrentLocation;
use App\Models\Vehicles\Location;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Vehicles\Binnacles\Binnacle
 *
 * @property int $id
 * @property Carbon | string $date
 * @property Carbon | string $prev_date
 * @property int $type_id
 * @property int $vehicle_id
 * @property int $user_id
 * @property string|null $observations
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property boolean $completed
 * @method static Builder|Binnacle whereCreatedAt($value)
 * @method static Builder|Binnacle whereDate($value)
 * @method static Builder|Binnacle whereId($value)
 * @method static Builder|Binnacle whereObservations($value)
 * @method static Builder|Binnacle whereTypeId($value)
 * @method static Builder|Binnacle whereUpdatedAt($value)
 * @method static Builder|Binnacle whereUserId($value)
 * @method static Builder|Binnacle whereVehicleId($value)
 * @mixin Eloquent
 * @property-read Type $type
 * @property-read User $user
 * @property-read Vehicle $vehicle
 * @property Notification $notification
 * @property int|null $mileage
 * @method static Builder|Binnacle whereMileage($value)
 * @property int|null $mileage_odometer
 * @property int|null $mileage_odometer_completed
 * @property int|null $mileage_route
 * @property int|null $mileage_route_completed
 * @method static Builder|Binnacle whereMileageOdometer($value)
 * @method static Builder|Binnacle whereMileageOdometerCompleted($value)
 * @method static Builder|Binnacle whereMileageRoute($value)
 * @method static Builder|Binnacle whereMileageRouteCompleted($value)
 * @property-read mixed $mileage_traveled_odometer
 * @property-read mixed $mileage_traveled_route
 */
class Binnacle extends Model
{
    protected $table = 'vehicle_binnacles';

    protected $fillable = ['date', 'prev_date', 'type_id', 'vehicle_id', 'user_id', 'observations', 'mileage'];

    protected function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    function getCreatedAtAttribute($date)
    {
        return Carbon::createFromFormat(config('app.simple_date_time_format'), explode('.', $date)[0]);
    }

    function getUpdatedAtAttribute($date)
    {
        return Carbon::createFromFormat(config('app.simple_date_time_format'), explode('.', $date)[0]);
    }

    function getDateAttribute($date)
    {
        if (!$date) return null;
        return Carbon::createFromFormat(config('app.simple_date_time_format'), explode('.', $date)[0]);
    }

    function getPrevDateAttribute($date)
    {
        if (!$date) return null;

        if ($date instanceof Carbon) return $date;

        return Carbon::createFromFormat(config('app.simple_date_time_format'), explode('.', $date)[0]);
    }

    /**
     * @return BelongsTo | Type
     */
    function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    /**
     * @return BelongsTo | Vehicle
     */
    function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    function getMileageTraveled($type = 'greater')
    {
        $mileageByOdometer = $this->mileage_traveled_odometer;
        $mileageByRoute = $this->mileage_traveled_route;

        switch ($type) {
            case 'route':
                return $mileageByRoute;
                break;
            case 'odometer':
                return $mileageByOdometer;
                break;
            default:
                return $mileageByOdometer > $mileageByRoute ? $mileageByOdometer : $mileageByRoute;
                break;
        }
    }

    function getMileageTraveledOdometerAttribute()
    {
        $currentLocationOdometer = $this->vehicle->currentLocation->odometer;
        return (($this->completed ? $this->mileage_odometer_completed : $currentLocationOdometer) - $this->mileage_odometer) / 1000;
    }

    function getMileageTraveledRouteAttribute()
    {
        $currentLocationMileageRoute = $this->vehicle->currentLocation->mileage_route;

        return (($this->completed ? $this->mileage_route_completed : $currentLocationMileageRoute) - $this->mileage_route) / 1000;
    }

    /**
     * @return BelongsTo | User
     */
    function user()
    {
        return $this->belongsTo(User::class);
    }

    function notification()
    {
        return $this->hasOne(Notification::class, 'binnacle_id', 'id');
    }

    function complete()
    {
        if (!$this->completed) {
            $currentLocation = $this->vehicle->currentLocation;

            $this->mileage_odometer_completed = $currentLocation->odometer;
            $this->mileage_route_completed = $currentLocation->mileage_route;

            $this->completed = true;
        }

        return $this;
    }

    function isNotifiableByMileage()
    {
        return $this->mileage && $this->getMileageTraveled() >= $this->notification->mileage;
    }
}
