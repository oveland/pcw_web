<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\HistorySeat
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string|null $plate
 * @property int|null $seat
 * @property string $date
 * @property string $time
 * @property float|null $active_latitude
 * @property float|null $active_longitude
 * @property string $active_time
 * @property string|null $inactive_time
 * @property int|null $active_km
 * @property int|null $inactive_km
 * @property string|null $busy_time
 * @property int|null $busy_km
 * @property int|null $complete
 * @property float|null $inactive_latitude
 * @property float|null $inactive_longitude
 * @property int|null $vehicle_id
 * @property int|null $dispatch_register_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereActiveKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereActiveLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereActiveLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereActiveTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereBusyKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereBusyTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereComplete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereInactiveKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereInactiveLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereInactiveLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereInactiveTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat wherePlate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereSeat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorySeat whereVehicleId($value)
 */
class HistorySeat extends Model
{
    protected function getDateFormat()
    {
        return config('app.date_time_format');
    }
    /*protected $dates = [
      'date','time','active_time','inactive_time','busy_time'
    ];*/
}
