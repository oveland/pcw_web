<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AlamedaJulyReport
 *
 * @property int $id
 * @property int|null $version
 * @property string|null $date
 * @property string|null $date_created
 * @property int|null $dispatch_register_id
 * @property int|null $distanced
 * @property int|null $distancem
 * @property int|null $distancep
 * @property string|null $last_updated
 * @property string|null $status
 * @property string|null $timed
 * @property string|null $timem
 * @property string|null $timep
 * @property int|null $location_id
 * @property float|null $status_in_minutes
 * @property-read \App\DispatchRegister|null $dispatchRegister
 * @property-read \App\AlamedaJulyLocation|null $location
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereDistanced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereDistancep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereTimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereTimem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereTimep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyReport whereVersion($value)
 * @mixin \Eloquent
 */
class AlamedaJulyReport extends Model
{
    public function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class,'dispatch_register_id','id');
    }

    public function location()
    {
        return $this->belongsTo(AlamedaJulyLocation::class,'location_id','id');
    }

    protected function getDateFormat()
    {
        return config('app.date_time_format');
    }

    /*const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';*/
}
