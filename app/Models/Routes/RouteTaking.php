<?php

namespace App\Models\Routes;

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
 */
class RouteTaking extends Model
{
    protected $guarded = [];
    protected $fillable = ["total_production", "control", "fuel", "others", "net_production", "observations"];

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

    public function isTaken()
    {
        return $this->total_production && $this->net_production;
    }

    /**
     * @return object
     */
    public function getAPIFields()
    {
        return (object)[
            'totalProduction' => $this->total_production,
            'control' => $this->control,
            'fuel' => $this->fuel,
            'others' => $this->others,
            'netProduction' => $this->net_production,
            'observations' => $this->observations,
        ];
    }
}
