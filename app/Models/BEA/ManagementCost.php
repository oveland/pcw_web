<?php

namespace App\Models\BEA;

use App\Models\Vehicles\Vehicle;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\BEA\ManagementCost
 *
 * @method static Builder|ManagementCost newModelQuery()
 * @method static Builder|ManagementCost newQuery()
 * @method static Builder|ManagementCost query()
 * @mixin Eloquent
 * @property int $id
 * @property int $vehicle_id
 * @property string $name
 * @property string|null $description
 * @property int $value
 * @property int $uid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ManagementCost whereCreatedAt($value)
 * @method static Builder|ManagementCost whereDescription($value)
 * @method static Builder|ManagementCost whereId($value)
 * @method static Builder|ManagementCost whereName($value)
 * @method static Builder|ManagementCost whereUid($value)
 * @method static Builder|ManagementCost whereUpdatedAt($value)
 * @method static Builder|ManagementCost whereValue($value)
 * @method static Builder|ManagementCost whereVehicleId($value)
 * @property-read Vehicle $vehicle
 */
class ManagementCost extends Model
{
    public const PAYROLL_ID = 0;

    # Road Safety
    public const ADMIN_ID = 1;
    public const SOCIAL_CONTRIBUTIONS_ID = 2;
    public const ACCIDENT_FUND_ID = 3;
    public const INSURANCE_POLICY_ID = 4;
    public const ADMIN_COMPLEMENT_ID = 5;
    public const SOCIAL_SECURITY_CONTRIBUTION_ID = 6;
    public const CAR_WASHING_ID = 7;

    protected $table = 'bea_management_costs';

    protected $fillable = ['uid', 'name', 'concept','description', 'value', 'vehicle_id'];

    public function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
