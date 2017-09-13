<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Report
 *
 * @property-read \App\DispatchRegister $dispatchRegister
 * @property-read \App\Location $location
 * @mixin \Eloquent
 * @property int $id
 * @property int $version
 * @property string $date
 * @property \Carbon\Carbon $date_created
 * @property int $dispatch_register_id
 * @property int $distanced
 * @property int $distancem
 * @property int $distancep
 * @property \Carbon\Carbon $last_updated
 * @property string $status
 * @property string $timed
 * @property string $timem
 * @property string $timep
 * @property int|null $location_id
 * @property float|null $status_in_minutes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereDistanced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereDistancep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereTimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereTimem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereTimep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereVersion($value)
 */
class Report extends Model
{
    public function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class,'dispatch_register_id','id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class,'location_id','id');
    }

    protected function getDateFormat()
    {
        return config('app.date_format');
    }

    /*const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';*/
}
