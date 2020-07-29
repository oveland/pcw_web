<?php

namespace App\Models\Routes;

use App\Models\Vehicles\Location;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Routes\Report
 *
 * @property int $id
 * @property int $version
 * @property string $date
 * @property string $date_created
 * @property int $dispatch_register_id
 * @property int $distanced
 * @property int $distancem
 * @property int $distancep
 * @property string $last_updated
 * @property string $status
 * @property string $timed
 * @property string $timem
 * @property string $timep
 * @property int|null $location_id
 * @property float|null $status_in_minutes
 * @property-read DispatchRegister $dispatchRegister
 * @property-read Location|null $location
 * @method static Builder|Report whereDate($value)
 * @method static Builder|Report whereDateCreated($value)
 * @method static Builder|Report whereDispatchRegisterId($value)
 * @method static Builder|Report whereDistanced($value)
 * @method static Builder|Report whereDistancem($value)
 * @method static Builder|Report whereDistancep($value)
 * @method static Builder|Report whereId($value)
 * @method static Builder|Report whereLastUpdated($value)
 * @method static Builder|Report whereLocationId($value)
 * @method static Builder|Report whereStatus($value)
 * @method static Builder|Report whereStatusInMinutes($value)
 * @method static Builder|Report whereTimed($value)
 * @method static Builder|Report whereTimem($value)
 * @method static Builder|Report whereTimep($value)
 * @method static Builder|Report whereVersion($value)
 * @mixin Eloquent
 * @property int|null $control_point_id
 * @property int|null $fringe_id
 * @method static Builder|Report whereControlPointId($value)
 * @method static Builder|Report whereFringeId($value)
 * @property-read ControlPoint|null $controlPoint
 */
class Report extends Model
{
    protected function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    public function getDateAttribute($date)
    {
        return Carbon::createFromFormat(config('app.simple_date_time_format'),explode('.',$date)[0]);
    }

    public function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class,'dispatch_register_id','id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class,'location_id','id');
    }

    public function controlPoint()
    {
        return $this->belongsTo(ControlPoint::class);
    }

    /*const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';*/
}
