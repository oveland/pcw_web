<?php

namespace App\Models\Vehicles\Binnacles;

use App\LastLocation;
use App\Models\Users\User;
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
 * @method static Builder|Binnacle whereMileage($value)
 * @property int|null $mileage
 * @property int|null $mileage_completed
 * @property int|null $mileage_odometer
 * @property int|null $mileage_odometer_completed
 * @property int|null $mileage_route
 * @property int|null $mileage_route_completed
 * @method static Builder|Binnacle whereMileageOdometer($value)
 * @method static Builder|Binnacle whereMileageOdometerCompleted($value)
 * @method static Builder|Binnacle whereMileageRoute($value)
 * @method static Builder|Binnacle whereMileageRouteCompleted($value)
 * @property int|null $mileage_expiration
 * @property-read int $mileage_traveled
 * @property-read int $mileage_traveled_odometer
 * @property-read int $mileage_traveled_route
 */
class Binnacle extends Model
{
    protected $table = 'vehicle_binnacles';

    protected $fillable = ['date', 'prev_date', 'type_id', 'vehicle_id', 'user_id', 'observations', 'mileage', 'mileage_expiration'];

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
        switch ($type) {
            case 'route':
                return $this->mileage_traveled_route;
                break;
            case 'odometer':
                return $this->mileage_traveled_odometer;
                break;
            default:
                return $this->mileage_traveled;
                break;
        }
    }

    function getMileageTraveledAttribute()
    {
        return (($this->completed ? $this->mileage_completed : $this->yesterdayLocation()->mileage) - $this->mileage) / 1000;
    }

    function getMileageTraveledOdometerAttribute()
    {
        return (($this->completed ? $this->mileage_odometer_completed : $this->yesterdayLocation()->odometer) - $this->mileage_odometer) / 1000;
    }

    function getMileageTraveledRouteAttribute()
    {
        return (($this->completed ? $this->mileage_route_completed : $this->yesterdayLocation()->mileage_route) - $this->mileage_route) / 1000;
    }

    private function yesterdayLocation(): LastLocation
    {
        return LastLocation::whereDate('date', '<', Carbon::now()->toDateString())
            ->where('vehicle_id', $this->vehicle->id)
            ->orderByDesc('date')
            ->first();
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
            //$currentLocation = $this->vehicle->currentLocation;
            //$this->mileage_completed = $currentLocation->mileage_route;
            //$this->mileage_odometer_completed = $currentLocation->odometer;
            //$this->mileage_route_completed = $currentLocation->mileage_route;
            $this->mileage_completed = $this->yesterdayLocation()->mileage;
            $this->mileage_odometer_completed = $this->yesterdayLocation()->odometer;
            $this->mileage_route_completed = $this->yesterdayLocation()->mileage_route;

            $this->completed = true;
        }

        return $this;
    }

    function isNotifiableByMileage()
    {
        return !$this->notification->date && $this->mileage_expiration && $this->getMileageTraveled() >= $this->notification->mileage;
    }
}
