<?php

namespace App\Models\BEA;

use App\Models\Users\User;
use App\Models\Vehicles\Vehicle;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\BEA\Liquidation
 *
 * @method static Builder|Liquidation newModelQuery()
 * @method static Builder|Liquidation newQuery()
 * @method static Builder|Liquidation query()
 * @mixin Eloquent
 * @property int $id
 * @property string $date
 * @property int $vehicle_id
 * @property string $liquidation
 * @property string $totals
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Mark[] $marks
 * @property-read User|null $user
 * @method static Builder|Liquidation whereCreatedAt($value)
 * @method static Builder|Liquidation whereDate($value)
 * @method static Builder|Liquidation whereId($value)
 * @method static Builder|Liquidation whereLiquidation($value)
 * @method static Builder|Liquidation whereTotals($value)
 * @method static Builder|Liquidation whereUpdatedAt($value)
 * @method static Builder|Liquidation whereUserId($value)
 * @method static Builder|Liquidation whereVehicleId($value)
 * @property-read mixed $total
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 */
class Liquidation extends Model
{
    protected $table = 'bea_liquidations';
    protected $dates = ['date'];

    /**
     * @return Mark[] | null
     */
    function marks()
    {
        return $this->hasMany(Mark::class);
    }


    /**
     * @return object
     */
    function getLiquidationAttribute()
    {
        $l = $this->attributes['liquidation'];
        return $l ? (object) json_decode($l, true) : (object)[];
    }

    /**
     * @return object
     */
    function getTotalsAttribute()
    {
        $t = $this->attributes['totals'];
        return $t ? (object) json_decode($t, true) : (object)[];
    }

    /**
     * @return Vehicle | null
     */
    function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return User | null
     */
    function user()
    {
        return $this->belongsTo(User::class);
    }

    function getTotalAttribute(){
        $liquidation = $this->liquidation;
        return $liquidation->totalBea - $liquidation->totalDiscounts - $liquidation->totalCommissions + $liquidation->totalPenalties;
    }
}
