<?php

namespace App\Models\Vehicles;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\VehicleIssue
 *
 * @property int $id
 * @property string $date
 * @property int $issue_type_id
 * @property string $issue_uid
 * @property int $vehicle_id
 * @property int|null $dispatch_register_id
 * @property int|null $driver_id
 * @property int $user_id
 * @property string $observations
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|VehicleIssue whereCreatedAt($value)
 * @method static Builder|VehicleIssue whereDate($value)
 * @method static Builder|VehicleIssue whereDispatchRegisterId($value)
 * @method static Builder|VehicleIssue whereDriverId($value)
 * @method static Builder|VehicleIssue whereId($value)
 * @method static Builder|VehicleIssue whereIssueTypeId($value)
 * @method static Builder|VehicleIssue whereIssueUid($value)
 * @method static Builder|VehicleIssue whereObservations($value)
 * @method static Builder|VehicleIssue whereUpdatedAt($value)
 * @method static Builder|VehicleIssue whereUserId($value)
 * @method static Builder|VehicleIssue whereVehicleId($value)
 * @mixin Eloquent
 */
class VehicleIssue extends Model
{
    public function getDateFormat()
    {
        return config('app.date_time_format');
    }

    protected $fillable = ['date', 'issue_type_id', 'issue_uid', 'vehicle_id', 'dispatch_register_id', 'driver_id', 'user_id', 'observations'];
}
