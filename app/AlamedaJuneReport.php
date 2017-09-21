<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AlamedaJuneReport
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
 * @property-read \App\AlamedaJuneLocation|null $location
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereDistanced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereDistancep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereTimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereTimem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereTimep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneReport whereVersion($value)
 * @mixin \Eloquent
 */
class AlamedaJuneReport extends Model
{
    public function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class,'dispatch_register_id','id');
    }

    public function location()
    {
        return $this->belongsTo(AlamedaJuneLocation::class,'location_id','id');
    }

    protected function getDateFormat()
    {
        return config('app.date_time_format');
    }

    /*const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';*/
}
