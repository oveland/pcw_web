<?php

namespace App\Models\Routes;

use App\Models\Company\Company;
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
 * @property int|null $station_fuel_id
 * @method static Builder|RouteTaking whereStationFuelId($value)
 * @property int|null $advance
 * @property int|null $balance
 * @method static Builder|RouteTaking whereAdvance($value)
 * @method static Builder|RouteTaking whereBalance($value)
 */
class RouteTaking extends Model
{
    const STATIONS_FUEL = [
        1 => 'EDS Alameda',
        2 => 'EDS Cerros',
        3 => 'Otra',
    ];

    protected $guarded = ['total_production', 'fuel_gallons', 'net_production'];
    protected $fillable = ["passenger_tariff", "control", "fuel_tariff", "fuel", "others", "bonus", "advance", "balance", "observations", "station_fuel_id"];

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

    public function getTotalProductionAttribute()
    {
        return $this->dispatchRegister->complete() ? intval($this->attributes['total_production']) : 0;
    }

    function setBalanceAttribute($value)
    {
        $this->attributes['balance'] = intval($this->net_production) - intval($this->advance);
    }

    function getBalanceAttribute()
    {
        return intval($this->net_production) - intval($this->advance);
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
            'advance' => $this->advance,
            'balance' => $this->balance,
            'netProduction' => $this->net_production,
            'observations' => $this->observations,
            'user' => $this->user,
            'isTaken' => $this->isTaken(),
            'stationFuel' => $this->stationFuel()
        ];
    }

    /**
     * @return string
     */
    public function stationFuel()
    {
        $stations = self::STATIONS_FUEL;

        if ($this->dispatchRegister->route && $this->dispatchRegister->route->company->id == Company::YUMBENOS) {
            $stations = ['Estación 1', 'Estación 2', 'Estación 3'];
        }

        if ($this->station_fuel_id) {
            return $stations[$this->station_fuel_id];
        }
        return "";
    }
}
