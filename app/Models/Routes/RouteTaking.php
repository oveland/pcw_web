<?php

namespace App\Models\Routes;

use App\Models\Operation\FuelStation;
use App\Models\Users\User;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Routes\RouteTaking
 *
 * @property int $id
 * @property int|null $total_production
 * @property int|null $control
 * @property int|null $fuel
 * @property int|null $others
 * @property int|null $net_production
 * @property string|null $observations
 * @property int $dispatch_register_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|RouteTaking whereControl($value)
 * @method static Builder|RouteTaking whereCreatedAt($value)
 * @method static Builder|RouteTaking whereDispatchRegisterId($value)
 * @method static Builder|RouteTaking whereFuel($value)
 * @method static Builder|RouteTaking whereId($value)
 * @method static Builder|RouteTaking whereNetProduction($value)
 * @method static Builder|RouteTaking whereObservations($value)
 * @method static Builder|RouteTaking whereOthers($value)
 * @method static Builder|RouteTaking whereTotalProduction($value)
 * @method static Builder|RouteTaking whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read DispatchRegister $dispatchRegister
 * @property-read FuelStation $fuelStation
 * @property bool $taken
 * @method static Builder|RouteTaking newModelQuery()
 * @method static Builder|RouteTaking newQuery()
 * @method static Builder|RouteTaking query()
 * @method static Builder|RouteTaking whereTaken($value)
 * @property int $passenger_tariff
 * @property int|null $bonus
 * @property int $fuel_tariff
 * @property int|null $fuel_gallons
 * @method static Builder|RouteTaking whereBonus($value)
 * @method static Builder|RouteTaking whereFuelGallons($value)
 * @method static Builder|RouteTaking whereFuelTariff($value)
 * @method static Builder|RouteTaking wherePassengerTariff($value)
 * @property int|null $user_id
 * @method static Builder|RouteTaking whereUserId($value)
 * @property-read User|null $user
 * @property int|null $fuel_station_id
 * @method static Builder|RouteTaking whereStationFuelId($value)
 * @property int|null $advance
 * @property int|null $balance
 * @property string $counter
 * @property string $type
 * @property int|null $parent_takings_id
 * @property-read RouteTaking $parent
 * @method static Builder|RouteTaking whereAdvance($value)
 * @method static Builder|RouteTaking whereBalance($value)
 * @property-read mixed $passengers_advance
 * @property-read mixed $passengers_balance
 */
class RouteTaking extends Model
{
    const TAKING_BY_ROUND_TRIP = 1;
    const TAKING_BY_ALL = 2;

    protected $guarded = ['total_production', 'fuel_gallons', 'net_production'];
    protected $fillable = ["passenger_tariff", "control", "fuel_tariff", "fuel", "others", "bonus", "advance", "balance", "observations", "fuel_station_id", "counter", "type", "parent_takings_id"];

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    /**
     * @param DispatchRegister $dr
     * @return RouteTaking
     */
    static function findByDr(DispatchRegister $dr)
    {
        return self::where('dispatch_register_id', $dr->id)->get()->first();
    }

    function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class)->with('route');
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }

    function parent()
    {
        return $this->belongsTo(RouteTaking::class, 'parent_takings_id', 'id');
    }

    function hasParent()
    {
        return !!$this->parent_takings_id;
    }

    function isTaken()
    {
        return $this->taken;
    }

    function passengerTariff(Route $route = null)
    {
        if ($this->isTaken()) {
            return intval($this->passenger_tariff);
        }

        return $route ? $route->tariff->passenger : 0;
    }

    function fuelTariff(Route $route = null)
    {
        if ($this->isTaken() && intval($this->fuel_tariff)) {
            return intval($this->fuel_tariff);
        }

        return $route ? $route->tariff->fuel : 0;
    }

    function getTotalProductionAttribute()
    {
        return $this->dispatchRegister && $this->dispatchRegister->complete() ? intval($this->attributes['total_production']) : 0;
    }

    function getPassengersBalanceAttribute()
    {
        if ($this->dispatchRegister->vehicle->process_takings) return 0;

        $totalPassengers = $this->dispatchRegister->passengers->recorders->count;
        return intval($totalPassengers) - $this->passengers_advance;
    }

    function setBalanceAttribute($value)
    {
        $this->attributes['balance'] = intval($this->net_production) - intval($this->advance);
    }

    function getBalanceAttribute()
    {
        return intval($this->net_production) - intval($this->advance);
    }

    function getPassengersTaken()
    {
        $totalAmount = intval($this->advance) + $this->getTotalDiscounts();

        return $this->passenger_tariff ? $totalAmount / $this->passenger_tariff : 0;
    }

    function getTotalDiscounts()
    {
        return intval($this->control) + intval($this->fuel) + intval($this->bonus) + intval($this->others);
    }

    /**
     * @return object
     */
    function getAPIFields()
    {
        return (object)[
            'passengerTariff' => $this->passenger_tariff,
            'totalProduction' => $this->total_production,
            'control' => $this->control,
            'fuelTariff' => $this->fuel_tariff,
            'fuelGallons' => $this->fuel_gallons,
            'fuel' => $this->fuel,
            'others' => $this->others,
            'bonus' => $this->bonus,
            'advance' => $this->advance,
            'counter' => $this->counter,
            'totalDiscounts' => $this->getTotalDiscounts(),
            'passengersTaken' => $this->getPassengersTaken(),
            'passengersBalance' => $this->passengers_balance,
            'balance' => $this->balance,
            'netProduction' => $this->net_production,
            'observations' => $this->observations,
            'user' => $this->user ? $this->user->toArray(true) : null,
            'isTaken' => $this->isTaken(),
            'fuelStation' => $this->fuelStation->toArray(),
            'hasParent' => $this->hasParent(),
            'updatedAt' => $this->updated_at->toDateTimeString()
        ];
    }

    /**
     * @return BelongsTo|FuelStation
     */
    function fuelStation()
    {
        return $this->belongsTo(FuelStation::class);
    }
}
