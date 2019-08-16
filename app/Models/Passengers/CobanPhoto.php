<?php

namespace App\Models\Passengers;

use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Location;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
class CobanPhoto extends Model
{
    function getDateFormat()
    {
        return config('app.date_time_format');
    }

    public function getDateAttribute($date)
    {
        return Carbon::createFromFormat(config('app.simple_date_time_format'), explode('.', $date)[0])->setTimezone('-10:00');
    }

    public function getCreatedAtAttribute($created)
    {
        return Carbon::createFromFormat(config('app.simple_date_time_format'), explode('.', $created)[0]);
    }

    /**
     * @return Location | BelongsTo
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * @return Vehicle | BelongsTo
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return DispatchRegister | BelongsTo
     */
    public function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class);
    }

    /**
     * @return CobanPhotoPackage[] | HasMany
     */
    public function packages()
    {
        return $this->hasMany(CobanPhotoPackage::class, 'photo_id')->orderBy('package_id');
    }
}
