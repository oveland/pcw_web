<?php

namespace App\Models\Routes;

use App\Models\Users\User;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
 */
class RouteTaking extends Model
{
    protected $guarded = ['total_production', 'fuel_gallons', 'net_production'];
    protected $fillable = ["passenger_tariff", "control", "fuel_tariff", "fuel", "others", "bonus", "observations"];

    public function getDateFormat()
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

    public function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isTaken()
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

    /**
     * @return object
     */
    public function getAPIFields()
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
            'netProduction' => $this->net_production,
            'observations' => $this->observations,
            'user' => $this->user,
            'isTaken' => $this->isTaken()
        ];
    }
}
