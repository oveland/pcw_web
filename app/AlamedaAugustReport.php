<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AlamedaAugustReport
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
 * @property-read \App\AlamedaAugustLocation|null $location
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereDistanced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereDistancep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereTimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereTimem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereTimep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustReport whereVersion($value)
 * @mixin \Eloquent
 */
class AlamedaAugustReport extends Model
{
    public function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class,'dispatch_register_id','id');
    }

    public function location()
    {
        return $this->belongsTo(AlamedaAugustLocation::class,'location_id','id');
    }

    protected function getDateFormat()
    {
        return config('app.date_time_format');
    }

    /*const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';*/
}
