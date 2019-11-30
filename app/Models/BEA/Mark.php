<?php

namespace App\Models\BEA;

use DateTime;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use PhpParser\Node\Expr\Cast\Object_;

/**
 * App\Models\BEA\Mark
 *
 * @property int $id
 * @property int $turn_id
 * @property int $trajectory_id
 * @property Carbon|string $date
 * @property string $initial_time
 * @property string $final_time
 * @property int $passengers_up
 * @property int $passengers_down
 * @property int $locks
 * @property int $auxiliaries
 * @property int $boarded
 * @property int $im_bea_max
 * @property int $im_bea_min
 * @property int $total_bea
 * @property int $passengers_bea
 * @property bool $liquidated
 * @property string|null $liquidation_date
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Trajectory $trajectory
 * @method static Builder|Mark newModelQuery()
 * @method static Builder|Mark newQuery()
 * @method static Builder|Mark query()
 * @method static Builder|Mark whereAuxiliaries($value)
 * @method static Builder|Mark whereBoarded($value)
 * @method static Builder|Mark whereCreatedAt($value)
 * @method static Builder|Mark whereDate($value)
 * @method static Builder|Mark whereFinalTime($value)
 * @method static Builder|Mark whereId($value)
 * @method static Builder|Mark whereImBeaMax($value)
 * @method static Builder|Mark whereImBeaMin($value)
 * @method static Builder|Mark whereInitialTime($value)
 * @method static Builder|Mark whereLiquidated($value)
 * @method static Builder|Mark whereLiquidationDate($value)
 * @method static Builder|Mark whereLocks($value)
 * @method static Builder|Mark wherePassengersBea($value)
 * @method static Builder|Mark wherePassengersDown($value)
 * @method static Builder|Mark wherePassengersUp($value)
 * @method static Builder|Mark whereTotalBea($value)
 * @method static Builder|Mark whereTrajectoryId($value)
 * @method static Builder|Mark whereTurnId($value)
 * @method static Builder|Mark whereUpdatedAt($value)
 * @method static Builder|Mark whereUserId($value)
 * @mixin Eloquent
 * @property string|null $liquidated_date
 * @property-read Turn $turn
 * @method static Builder|Mark whereLiquidatedDate($value)
 * @property int|null $liquidation_id
 * @property bool $taken
 * @method static Builder|Mark whereLiquidationId($value)
 * @method static Builder|Mark whereTaken($value)
 * @property-read int $boarding
 * @property-read Object $commission
 * @property-read Discount[]|Collection $discounts
 * @property-read mixed $status
 * @property-read int $total_gross_bea
 * @property-read mixed $duration
 * @property-read Carbon initialTime
 * @property-read Carbon finalTime
 * @property-read Object $penalty
 * @property int $pay_fall
 * @property int $get_fall
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Mark whereExtra($value)
 */
class Mark extends Model
{
    const BEA_CTE = 1000; // TODO: Define real value from production!

    protected $table = 'bea_marks';

    protected $dates = ['date'];

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    /**
     * @return DateTime
     */
    function getDateAttribute()
    {
        return Carbon::createFromFormat(config('app.simple_date_time_format'), $this->attributes['date']);
    }

    /**
     * @return DateTime
     */
    function getInitialTimeAttribute()
    {
        return Carbon::createFromFormat(config('app.simple_time_format'), $this->attributes['initial_time']);
    }

    /**
     * @return DateTime
     */
    function getFinalTimeAttribute()
    {
        return Carbon::createFromFormat(config('app.simple_time_format'), $this->attributes['final_time']);
    }

    /**
     * @return BelongsTo
     */
    function turn()
    {
        return $this->belongsTo(Turn::class);
    }

    /**
     * @return BelongsTo
     */
    function trajectory()
    {
        return $this->belongsTo(Trajectory::class);
    }

    public function getBoardedAttribute()
    {
        switch ($this->id){
            case 8771:
                return 10;
                break;
            case 8840:
                return 7;
                break;
        }
    }

    function getStatusAttribute()
    {
        $status = [
            0 => (object)[
                'icon' => 'fa fa-file-o',
                'class' => 'green-sharp',
                'name' => __('No liquidated'),
            ],
            1 => (object)[
                'icon' => 'fa fa-file-text',
                'class' => 'yellow-crusta',
                'name' => __('Liquidated'),
            ],
            2 => (object)[
                'icon' => 'fa fa-suitcase',
                'class' => 'red-thunderbird',
                'name' => __('Taken'),
            ]
        ];

        if ($this->liquidated) return $status[1];
        if ($this->taken) return $status[2];
        return $status[0];
    }

    /**
     * @return Discount[] | Collection
     */
    function getDiscountsAttribute()
    {
        $turn = $this->turn;
        $discounts = Discount::with(['vehicle', 'route', 'trajectory', 'discountType'])
            ->where('vehicle_id', $turn->vehicle->id)
            ->where('route_id', $turn->route->id)
            ->where('trajectory_id', $this->trajectory ? $this->trajectory->id : null)
            ->get();

        return $discounts;
    }

    /**
     * @return int
     */
    function getTotalBeaAttribute()
    {
        switch ($this->id){
            case 8771:
                return 193800 + 3000;
                break;
            case 8840:
                return 109200 + 3000;
                break;
        }

        return (($this->im_bea_max + $this->im_bea_min) / 2) * self::BEA_CTE;
    }

    /**
     * @return int
     */
    function getTotalGrossBeaAttribute()
    {

        $discountByMobilityAuxilio = $this->discounts->filter(function ($d) {
            return $d->discountType->name == __('Mobility auxilio');
        })->first();

        return $discountByMobilityAuxilio ? $this->total_bea - $discountByMobilityAuxilio->value : $this->total_bea;
    }

    /**
     * @return Object
     */
    function getCommissionAttribute()
    {
        $commissionsByRoute = Commission::with('route')->where('route_id', $this->turn->route->id)->limit(1)->get();
        $commissionValue = 0;

        $totalGrossBea = $this->total_gross_bea;

        foreach ($commissionsByRoute as $commissionByRoute) {
            switch ($commissionByRoute->type) {
                case 'fixed':
                    $commissionValue += $this->passengers_bea * $commissionByRoute->value;
                    break;
                case 'percent':
                    $commissionValue += ($totalGrossBea + $this->penalty->value) * $commissionByRoute->value / 100;
                    break;
            }
        }

        $commission = (object)[
            'value' => $commissionValue,
            'type' => $commissionsByRoute->first()->type,
            'baseValue' => $commissionsByRoute->first()->value,
        ];

        return $commission;
    }

    /**
     * @return Object
     */
    function getPenaltyAttribute()
    {
        $penaltyByRoute = Penalty::where('route_id', $this->turn->route->id)->get()->first();
        $penaltyValue = $this->boarded > 5 ? $this->boarded * $penaltyByRoute->value : 0;
        return (object)[
            'value' => $penaltyValue,
            'type' => $penaltyByRoute->type,
            'baseValue' => $penaltyByRoute->value,
        ];
    }

    function getDurationAttribute()
    {
        $duration = $this->initialTime->diff($this->finalTime);
        return $duration->h . "h " . $duration->i . " m";
    }

    function getAPIFields()
    {
        return (object)[
            'id' => $this->id,
            'turn' => $this->turn,
            'date' => $this->date->toDateString(),
            'initialTime' => $this->initialTime->toTimeString(),
            'finalTime' => $this->finalTime->toTimeString(),
            'duration' => $this->duration,
            'trajectory' => $this->trajectory,
            'passengersUp' => $this->passengers_up,
            'passengersDown' => $this->passengers_down,
            'locks' => $this->locks,
            'auxiliaries' => $this->auxiliaries,
            'boarded' => $this->boarded,
            'imBeaMax' => $this->im_bea_max,
            'imBeaMin' => $this->im_bea_min,
            'totalBEA' => $this->total_bea,
            'totalGrossBEA' => $this->total_gross_bea,
            'passengersBEA' => $this->passengers_bea,
            'discounts' => $this->discounts->toArray(),
            'commission' => $this->commission,
            'penalty' => $this->penalty,
            'status' => $this->status,
            'liquidated' => $this->liquidated,
            'liquidation_id' => $this->liquidation_id,
            'taken' => $this->taken,
            'payFall' => $this->pay_fall,
            'getFall' => $this->get_fall
        ];
    }
}
