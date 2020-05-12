<?php

namespace App\Models\Vehicles;

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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus whereDateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus whereObservations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus whereVehicleId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus query()
 * @property int|null $updated_user_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ReportVehicleStatus whereUpdatedUserId($value)
 */
class ReportVehicleStatus extends Model
{
    protected $table = 'report_vehicle_status';
}
