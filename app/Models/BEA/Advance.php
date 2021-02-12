<?php

namespace App\Models\BEA;

use Eloquent;
use App\Models\Users\User;
use Illuminate\Support\Carbon;
use App\Models\Vehicles\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\BEA\Advance
 *
 * @property int $id
 * @property int $value
 * @property int $vehicle_id
 * @property bool $liquidated
 * @property int|null $liquidation_id
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Advance newModelQuery()
 * @method static Builder|Advance newQuery()
 * @method static Builder|Advance query()
 * @method static Builder|Advance whereCreatedAt($value)
 * @method static Builder|Advance whereId($value)
 * @method static Builder|Advance whereLiquidated($value)
 * @method static Builder|Advance whereLiquidationId($value)
 * @method static Builder|Advance whereUpdatedAt($value)
 * @method static Builder|Advance whereUserId($value)
 * @method static Builder|Advance whereValue($value)
 * @method static Builder|Advance whereVehicleId($value)
 * @mixin Eloquent
 * @property-read Liquidation|null $liquidation
 * @property-read User|null $user
 * @property-read Vehicle $vehicle
 * @method static Builder|Advance active()
 */
class Advance extends Model
{
    protected $table = 'bea_advances';

    /**
     * @return BelongsTo | Vehicle
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return BelongsTo | User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setValueAttribute($value)
    {
        $this->user()->associate(auth()->user());

        if (!$this->liquidation) {
            $this->attributes['value'] = $value;
        }
    }

    /**
     * @return BelongsTo | Liquidation
     */
    public function liquidation()
    {
        return $this->belongsTo(Liquidation::class);
    }

    /**
     * @param Builder | Advance $query
     * @return Builder | Advance
     */
    public function scopeActive($query)
    {
        return $query->where('liquidated', false);
    }


    /**
     * @param Vehicle $vehicle
     * @return Advance | Builder
     */
    public static function findByVehicle(Vehicle $vehicle)
    {
        $activeAdvance = Advance::where('vehicle_id', $vehicle->id)->active()->first();

        if (!$activeAdvance) {
            $activeAdvance = new Advance();
            $activeAdvance->vehicle()->associate($vehicle);
            $activeAdvance->value = 0;
        }


        return $activeAdvance;
    }
}
