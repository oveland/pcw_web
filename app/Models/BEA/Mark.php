<?php

namespace App\Models\BEA;

use DateTime;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use PhpParser\Node\Expr\Cast\Object_;
use test\Mockery\HasUnknownClassAsTypeHintOnMethod;

/**
 * App\Models\BEA\Mark
 *
 * @property int $id
 * @property int $turn_id
 * @property int $trajectory_id
 * @property string $date
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Mark whereLiquidationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Mark whereTaken($value)
 * @property-read int $boarding
 * @property-read Object $commission
 * @property-read \Discount[] $discounts
 * @property-read mixed $status
 * @property-read int $total_gross_bea
 */
class Mark extends Model
{
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
        return Carbon::createFromFormat(config('app.date_format'), $this->attributes['date']);
    }

    /**
     * @return DateTime
     */
    function getInitialTimeAttribute()
    {
        return Carbon::createFromFormat(config('app.simple_time_format'), $this->attributes['initial_time']);
    }

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
     * @return Discount[]
     */
    function getDiscountsAttribute(){
        $turn = $this->turn;
        $discounts = Discount::with(['vehicle', 'route', 'trajectory', 'discountType'])
            ->where('vehicle_id', $turn->vehicle->id)
            ->where('route_id', $turn->route->id)
            ->where('trajectory_id', $this->trajectory->id)
            ->get();

        return $discounts;
    }

    /**
     * @return int
     */
    function getTotalGrossBeaAttribute(){

        $discountByMobilityAuxilio = $this->discounts->filter(function($d){
            return $d->discountType->name == __('Mobility auxilio');
        })->first();

        return $discountByMobilityAuxilio ? $this->total_bea - $discountByMobilityAuxilio->value : $this->total_bea;
    }

    /**
     * @return Object
     */
    function getCommissionAttribute(){
        $commissionsByRoute = Commission::with('route')->where('route_id', $this->turn->route->id)->limit(1)->get();
        $commissionValue = 0;

        $totalGrossBea = $this->total_gross_bea;

        foreach ($commissionsByRoute as $commissionByRoute){
            switch ($commissionByRoute->type){
                case 'fixed':
                    $commissionValue += $this->passengers_bea * $commissionByRoute->value;
                    break;
                case 'percent':
                    $commissionValue += $totalGrossBea * $commissionByRoute->value / 100;
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
     * @return int
     */
    function getBoardingAttribute(){
        return $this->passengers_up > $this->passengers_down ? ($this->passengers_up - $this->passengers_down) : 0;
    }
}
