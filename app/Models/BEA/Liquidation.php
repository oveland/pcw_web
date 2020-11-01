<?php

namespace App\Models\BEA;

use App\Models\Users\User;
use App\Models\Vehicles\Vehicle;
use DateTime;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @method static Builder|Liquidation whereDate($column, $value)
 * @method static Builder|Liquidation whereId($value)
 * @method static Builder|Liquidation whereLiquidation($value)
 * @method static Builder|Liquidation whereTotals($value)
 * @method static Builder|Liquidation whereUpdatedAt($value)
 * @method static Builder|Liquidation whereUserId($value)
 * @method static Builder|Liquidation whereVehicleId($value)
 * @property-read mixed $total
 * @property-read Vehicle $vehicle
 * @property-read \Mark|null $first_mark
 * @property-read \Mark|null $last_mark
 * @property bool $taken
 * @method static Builder|Liquidation whereTaken($value)
 * @property string|null $taking_date
 * @property int|null $taking_user_id
 * @property-read User|null $takingUser
 * @method static Builder|Liquidation whereTakingDate($value)
 * @method static Builder|Liquidation whereTakingUserId($value)
 * @property-read int|null $marks_count
 */
class Liquidation extends Model
{
    protected $table = 'bea_liquidations';
    protected $dates = ['date', 'taking_date'];

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    /**
     * @return Mark[]|null|HasMany
     */
    function marks()
    {
        return $this->hasMany(Mark::class)->enabled()->orderBy('initial_time');
    }

    /**
     * @return Mark|null
     */
    function getFirstMarkAttribute()
    {
        return $this->marks->first();
    }

    /**
     * @return Mark|null
     */
    function getLastMarkAttribute()
    {
        return $this->marks->last();
    }

    /**
     * @return object
     */
    function getLiquidationAttribute()
    {
        $l = $this->attributes['liquidation'];
        return $l ? (object)json_decode($l, true) : (object)[];
    }

    /**
     * @return object
     */
    function getTotalsAttribute()
    {
        $t = $this->attributes['totals'];
        return $t ? (object)json_decode($t, true) : (object)[];
    }

    /**
     * @return Vehicle | BelongsTo
     */
    function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return User | BelongsTo
     */
    function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return User | BelongsTo
     */
    function takingUser()
    {
        return $this->belongsTo(User::class, 'taking_user_id', 'id');
    }

    function getTotalAttribute()
    {
        $liquidation = $this->liquidation;
        return $liquidation->totalBea - $liquidation->totalDiscounts - $liquidation->totalCommissions + $liquidation->totalPenalties;
    }

    public function setLiquidationAttribute($liquidation)
    {
        $liquidation = collect((object) $liquidation);

        $otherDiscounts = $liquidation['otherDiscounts'];

        foreach ($otherDiscounts as &$otherDiscount){
            $otherDiscount = (object)$otherDiscount;
            $otherDiscount->fileUrl = $otherDiscount->hasFile && $otherDiscount->fileUrl ? route('takings-passengers-search-file-discount', ['id' => $otherDiscount->id]) : '';
        }

        $liquidation['otherDiscounts'] = $otherDiscounts;

        $this->attributes['liquidation'] = $liquidation->toJson();
    }
}
