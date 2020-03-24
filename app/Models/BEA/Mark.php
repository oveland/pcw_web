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
 * @method static Builder|Mark whereExtra($value)
 * @method static Builder|Mark whereGetFall($value)
 * @method static Builder|Mark wherePayFall($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|MarkCommission[] $markCommissions
 * @property-read \Illuminate\Database\Eloquent\Collection|MarkDiscount[] $markDiscounts
 * @property-read \Illuminate\Database\Eloquent\Collection|MarkPenalty[] $markPenalties
 * @property int|null $number
 * @method static Builder|Mark whereNumber($value)
 * @property-read Liquidation|null $liquidation
 * @property bool $duplicated
 * @property int|null $duplicated_mark_id
 * @property int|null $bea_id
 * @property int $company_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Mark enabled()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Mark whereBeaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Mark whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Mark whereDuplicated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Mark whereDuplicatedMarkId($value)
 * @property bool|null $ignore_trigger
 * @property-read mixed $pay_roll_cost
 * @property-read int|null $mark_commissions_count
 * @property-read int|null $mark_discounts_count
 * @property-read int|null $mark_penalties_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Mark whereIgnoreTrigger($value)
 */
class Mark extends Model
{
    const BEA_CTE = 1000; // TODO: Define real value from production!

    protected $table = 'bea_marks';

    protected $dates = ['date'];

    protected static function boot()
    {
        parent::boot();
        static::saving(function (Mark $mark) {
            if ($mark->liquidated && !$mark->taken) {
                $discounts = $mark->discounts;
                $markDiscounts = array();

                foreach ($discounts as $discount) {
                    $markDiscountType = MarkDiscountType::create($discount->discountType->toArray());

                    $markDiscount = new MarkDiscount();
                    $markDiscount->fill($discount->toArray());
                    $markDiscount->discountType()->associate($markDiscountType);
                    $markDiscount->mark()->associate($mark);

                    $markDiscounts[] = $markDiscount->attributesToArray();
                }

                $mark->markDiscounts()->createMany($markDiscounts);

                $commissionsByRoute = [$mark->getCommissionByRoute()];
                $markCommissions = [];
                foreach ($commissionsByRoute as $commissionByRoute) {
                    $markCommission = new MarkCommission();
                    $markCommission->fill($commissionByRoute->toArray());
                    $markCommission->mark()->associate($mark);

                    $markCommissions[] = $markCommission->attributesToArray();
                }
                $mark->markCommissions()->createMany($markCommissions);

                $penaltiesByRoute = [$mark->getPenaltyByRoute()];
                $markPenalties = array();
                foreach ($penaltiesByRoute as $penaltyByRoute) {
                    $markPenalty = new MarkPenalty();
                    $markPenalty->fill($penaltyByRoute->toArray());
                    $markPenalty->mark()->associate($mark);

                    $markPenalties[] = $markPenalty->attributesToArray();
                }
                $mark->markPenalties()->createMany($markPenalties);
            }
        });
    }

    public function getInitialTimeAttribute()
    {
        return Carbon::createFromFormat(config('app.simple_time_format'), $this->attributes['initial_time']);
    }

    public function getFinalTimeAttribute()
    {
        return Carbon::createFromFormat(config('app.simple_time_format'), $this->attributes['final_time']);
    }

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    public function markDiscounts()
    {
        return $this->hasMany(MarkDiscount::class, 'mark_id', 'id');
    }

    public function markCommissions()
    {
        return $this->hasMany(MarkCommission::class, 'mark_id', 'id');
    }

    public function markPenalties()
    {
        return $this->hasMany(MarkPenalty::class, 'mark_id', 'id');
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

    function getStatusAttribute()
    {
        $status = [
            0 => (object)[
                'icon' => 'fa fa-warning',
                'class' => 'yellow-crusta',
                'name' => __('Turn') . " $this->number > " . __('No liquidated'),
            ],
            1 => (object)[
                'icon' => 'fa fa-warning',
                'class' => 'blue',
                'name' => __('Turn') . " $this->number > " . __('Liquidated without taking'),
            ],
            2 => (object)[
                'icon' => 'fa fa-check-circle-o',
                'class' => 'green-meadow',
                'name' => __('Turn') . " $this->number > " . __('Taken'),
            ]
        ];

        if ($this->taken) return $status[2];
        if ($this->liquidated) return $status[1];
        return $status[0];
    }

    /**
     * @return Discount[] | Collection
     */
    function getDiscountsAttribute()
    {
        $turn = $this->turn;

        $markDiscounts = $this->markDiscounts;
        if ($this->liquidated && $markDiscounts->isNotEmpty()) return $markDiscounts;

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
     * @return Commission[]|Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getCommissionByRoute()
    {
        $markCommissions = $this->markCommissions;
        if ($this->liquidated && $markCommissions->isNotEmpty()) return $markCommissions->first();
        return Commission::with('route')->where('route_id', $this->turn->route->id)->first();
    }

    /**
     * @return Object
     */
    function getCommissionAttribute()
    {
        $commissionByRoute = $this->getCommissionByRoute();
        $commissionValue = 0;

        $totalGrossBea = $this->total_gross_bea;

        switch ($commissionByRoute->type) {
            case 'fixed':
                $commissionValue += $this->passengers_bea * $commissionByRoute->value;
                break;
            case 'percent':
                $commissionValue += ($totalGrossBea + $this->penalty->value - intval($this->pay_fall) + intval($this->get_fall)) * $commissionByRoute->value / 100;
                break;
        }

        $commission = (object)[
            'value' => $commissionValue,
            'type' => $commissionByRoute->type,
            'baseValue' => $commissionByRoute->value,
        ];

        return $commission;
    }

    /**
     * @return Commission[]|Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getPenaltyByRoute()
    {
        $markPenalties = $this->markPenalties;
        if ($this->liquidated && $markPenalties->isNotEmpty()) return $markPenalties->first();

        return Penalty::where('route_id', $this->turn->route->id)->where('vehicle_id', $this->turn->vehicle->id)->first();
    }

    public function liquidation()
    {
        return $this->belongsTo(Liquidation::class, 'liquidation_id', 'id');
    }

    /**
     * @return Object
     */
    function getPenaltyAttribute()
    {
        $penaltyByRoute = $this->getPenaltyByRoute();
        $penaltyValue = $this->boarded >= 4 ? $this->boarded * $penaltyByRoute->value : 0;
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

    public function getPayRollCostAttribute()
    {
        $turn = $this->turn;
        if ($turn && $turn->vehicle_id) {
            $payRollCost = ManagementCost::where('vehicle_id', $turn->vehicle_id)
                ->where('uid', ManagementCost::PAYROLL_ID)
                ->first();

            return $payRollCost ? $payRollCost->value : 0;
        }
        return 0;
    }

    function getAPIFields()
    {
        return (object)[
            'id' => $this->id,
            'turn' => $this->turn->getAPIFields(),
            'number' => $this->number,
            'date' => $this->date->toDateString(),
            'initialTime' => $this->initial_time->toTimeString(),
            'finalTime' => $this->final_time->toTimeString(),
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
            'payFall' => $this->pay_fall ? $this->pay_fall : 0,
            'getFall' => $this->get_fall ? $this->get_fall : 0,
            'payRollCost' => $this->payRollCost,
        ];
    }

    public function scopeEnabled($query)
    {
        return $query->where('trajectory_id', '<>', null)->where('duplicated', false);
    }
}
