<?php

namespace App\Models\Apps\Rocket;

use App\Models\Vehicles\Vehicle;
use Doctrine\DBAL\Query\QueryBuilder;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Apps\Rocket\Photo
 *
 * @method static Builder|Photo newModelQuery()
 * @method static Builder|Photo newQuery()
 * @method static Builder|Photo query()
 * @mixin Eloquent
 * @property int $id
 * @property Carbon $date
 * @property int $vehicle_id
 * @property int $dispatch_register_id
 * @property int $location_id
 * @property string $path
 * @property string $side
 * @property string $type
 * @property string $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Photo whereCreatedAt($value)
 * @method static Builder|Photo whereDate($value)
 * @method static Builder|Photo whereData($value)
 * @method static Builder|Photo whereDispatchRegisterId($value)
 * @method static Builder|Photo whereId($value)
 * @method static Builder|Photo whereLocationId($value)
 * @method static Builder|Photo wherePath($value)
 * @method static Builder|Photo whereSide($value)
 * @method static Builder|Photo whereType($value)
 * @method static Builder|Photo whereUpdatedAt($value)
 * @method static Builder|Photo whereVehicleId($value)
 * @property-read Vehicle $vehicle
 * @method static Builder|CurrentPhoto findByVehicle(Vehicle $vehicle)
 */
class CurrentPhoto extends Model
{
    protected $table = 'app_current_photos';

    protected $dates = ['date'];

    public function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    protected $fillable = ['date', 'vehicle_id', 'dispatch_register_id', 'location_id', 'path', 'type', 'data', 'side'];

    /**
     * @return BelongsTo | Vehicle
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @param Builder $query
     * @param Vehicle $vehicle
     * @return Builder
     */
    function scopeFindByVehicle(Builder $query, Vehicle $vehicle)
    {
        $currentPhoto = $query->where('vehicle_id', $vehicle->id)->first();
        $currentPhoto = $currentPhoto ? $currentPhoto : new CurrentPhoto();
        $currentPhoto->vehicle()->associate($vehicle);
        return $currentPhoto;
    }
}
