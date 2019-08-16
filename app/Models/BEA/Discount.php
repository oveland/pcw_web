<?php

namespace App\Models\BEA;

use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\BEA\Discount
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Discount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Discount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Discount query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $discount_type_id
 * @property int $route_id
 * @property int $trajectory_id
 * @property int $vehicle_id
 * @property int $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BEA\DiscountType $discountType
 * @property-read \App\Models\Routes\Route $route
 * @property-read \App\Models\BEA\Trajectory $trajectory
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Discount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Discount whereDiscountTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Discount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Discount whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Discount whereTrajectoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Discount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Discount whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Discount whereVehicleId($value)
 */
class Discount extends Model
{
    protected $table = 'bea_discounts';

    protected $fillable = ['vehicle_id', 'route_id', 'trajectory_id', 'discount_type_id', 'value'];

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    /**
     * @return BelongsTo
     */
    function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return BelongsTo
     */
    function route()
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * @return BelongsTo
     */
    function trajectory()
    {
        return $this->belongsTo(Trajectory::class);
    }

    /**
     * @return BelongsTo
     */
    function discountType()
    {
        return $this->belongsTo(DiscountType::class);
    }
}
