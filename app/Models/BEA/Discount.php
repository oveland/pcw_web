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
 * @property bool $required
 * @method static Builder|Discount whereRequired($value)
 * @property bool $optional
 * @method static Builder|Discount whereOptional($value)
 */
class Discount extends Model
{
    protected $table = 'bea_discounts';

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
        return $this->belongsTo(DiscountType::class);
    }

    public function getAPIFields()
    {
        return (object)[
            'id' => $this->id,
            'discount_type_id' => $this->discount_type_id,
            'discount_type' => $this->discountType->getAPIFields(),
            'discountType' => $this->discountType->getAPIFields(),
            'value' => $this->value,
            'required' => $this->required,
            'optional' => $this->optional,
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString()
        ];
    }
}
