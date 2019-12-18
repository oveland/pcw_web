<?php

namespace App\Models\Vehicles;

use App\Models\Drivers\Driver;
use App\Models\Routes\DispatchRegister;
use Carbon\Carbon;
use Doctrine\DBAL\Query\QueryBuilder;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use const http\Client\Curl\VERSIONS;

/**
 * App\Models\Vehicles\CurrentVehicleIssue
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
 * @method static Builder|CurrentVehicleIssue whereCreatedAt($value)
 * @method static Builder|CurrentVehicleIssue whereDate($value)
 * @method static Builder|CurrentVehicleIssue whereDispatchRegisterId($value)
 * @method static Builder|CurrentVehicleIssue whereDriverId($value)
 * @method static Builder|CurrentVehicleIssue whereId($value)
 * @method static Builder|CurrentVehicleIssue whereIssueTypeId($value)
 * @method static Builder|CurrentVehicleIssue whereIssueUid($value)
 * @method static Builder|CurrentVehicleIssue whereObservations($value)
 * @method static Builder|CurrentVehicleIssue whereUpdatedAt($value)
 * @method static Builder|CurrentVehicleIssue whereUserId($value)
 * @method static Builder|CurrentVehicleIssue whereVehicleId($value)
 * @mixin Eloquent
 * @property-read DispatchRegister|null $dispatchRegister
 * @property-read Driver|null $driver
 * @method static Builder|CurrentVehicleIssue withActiveIssue()
 */
class CurrentVehicleIssue extends Model
{
    public function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    /*public function getDateAttribute()
    {
        return Carbon::createFromFormat( $this->getDateFormat(), explode('.', $this->date)[0].".0" );
    }*/

    protected $fillable = ['date', 'issue_type_id', 'issue_uid', 'vehicle_id', 'dispatch_register_id', 'driver_id', 'user_id', 'observations'];

    public function readyForIn()
    {
        return $this->issue_type_id == VehicleIssueType::OUT || $this->issue_type_id == null;
    }

    public function readyForUpdate()
    {
        return $this->issue_type_id == VehicleIssueType::IN;
    }

    public function readyForOut()
    {
        return $this->issue_type_id == VehicleIssueType::IN || $this->issue_type_id == VehicleIssueType::UPDATE;
    }

    public function getColor()
    {
        $nexIssueType = VehicleIssueType::IN;

        if ($this->readyForOut() || $this->readyForUpdate()) $nexIssueType = VehicleIssueType::OUT;

        return VehicleIssueType::getColor($nexIssueType);
    }

    public function type()
    {
        return $this->belongsTo(VehicleIssueType::class, 'issue_type_id', 'id');
    }

    public function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function generateUid()
    {
        if ($this->issue_type_id == VehicleIssueType::IN) $this->issue_uid = "$this->vehicle_id-" . Carbon::now()->micro;
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @param QueryBuilder $query
     * @return mixed
     */
    public function scopeWithActiveIssue($query)
    {
        return $query->where('issue_type_id', '<', 3);
    }
}
