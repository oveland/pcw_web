<?php

namespace App\Models\Routes;

use App\Models\Vehicles\Location;
use Carbon\Carbon;
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
 * @property-read \App\Models\Routes\DispatchRegister $dispatchRegister
 * @property-read \App\Models\Vehicles\Location|null $location
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereDistanced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereDistancep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereTimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereTimem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereTimep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereVersion($value)
 * @mixin \Eloquent
 * @property int|null $control_point_id
 * @property int|null $fringe_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereControlPointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Report whereFringeId($value)
 * @property-read \App\Models\Routes\ControlPoint|null $controlPoint
 */
class Report extends Model
{
    function getDateFormat()
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
