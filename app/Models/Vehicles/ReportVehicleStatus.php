<?php

namespace App\Models\Vehicles;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\ReportVehicleStatus
 *
 * @property int $id
 * @property string|null $date
 * @property string|null $time
 * @property string|null $date_time
 * @property int $vehicle_id
 * @property string $status
 * @property string $updated_by
 * @property string|null $observations
 * @method static Builder|ReportVehicleStatus whereDate($value)
 * @method static Builder|ReportVehicleStatus whereDateTime($value)
 * @method static Builder|ReportVehicleStatus whereId($value)
 * @method static Builder|ReportVehicleStatus whereObservations($value)
 * @method static Builder|ReportVehicleStatus whereStatus($value)
 * @method static Builder|ReportVehicleStatus whereTime($value)
 * @method static Builder|ReportVehicleStatus whereUpdatedBy($value)
 * @method static Builder|ReportVehicleStatus whereVehicleId($value)
 * @mixin Eloquent
 * @property int|null $updated_user_id
 * @method static Builder|ReportVehicleStatus whereUpdatedUserId($value)
 */
class ReportVehicleStatus extends Model
{
    protected $table = 'report_vehicle_status';

    public function getParsedStatus()
    {
        switch ($this->status) {
            case 'ACTIVADO':
            case 'EN TRANSITO':
                return __('Active');
                break;
            case 'EN TALLER':
                return __('In repair');
                break;
            case 'DESACTIVADO':
                return __('Inactive');
                break;
        }
    }
}
