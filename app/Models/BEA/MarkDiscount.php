<?php

namespace App\Models\BEA;

use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\BEA\Discount
 *
 * @method static Builder|Discount newModelQuery()
 * @method static Builder|Discount newQuery()
 * @method static Builder|Discount query()
 * @mixin Eloquent
 * @property int $id
 * @property int $discount_type_id
 * @property int $route_id
 * @property int $trajectory_id
 * @property int $vehicle_id
 * @property int $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DiscountType $discountType
 * @property-read Route $route
 * @property-read Trajectory $trajectory
 * @property-read Vehicle $vehicle
 * @method static Builder|Discount whereCreatedAt($value)
 * @method static Builder|Discount whereDiscountTypeId($value)
 * @method static Builder|Discount whereId($value)
 * @method static Builder|Discount whereRouteId($value)
 * @method static Builder|Discount whereTrajectoryId($value)
 * @method static Builder|Discount whereUpdatedAt($value)
 * @method static Builder|Discount whereValue($value)
 * @method static Builder|Discount whereVehicleId($value)
 * @property int $mark_id
 * @method static Builder|MarkDiscount whereMarkId($value)
 * @property-read Mark $mark
 * @property bool $required
 * @property bool $optional
 * @method static Builder|MarkDiscount whereOptional($value)
 * @method static Builder|MarkDiscount whereRequired($value)
 */
class MarkDiscount extends Model
{
    protected $table = 'bea_mark_discounts';

    protected $fillable = ['vehicle_id', 'route_id', 'trajectory_id', 'discount_type_id', 'value', 'required', 'optional'];

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
        return $this->belongsTo(MarkDiscountType::class, 'discount_type_id', 'id');
    }

    public function mark()
    {
        return $this->belongsTo(Mark::class, 'mark_id', 'id');
    }
}
