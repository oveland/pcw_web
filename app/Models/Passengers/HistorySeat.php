<?php

namespace App\Models\Passengers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Passengers\HistorySeat
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereActiveKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereActiveLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereActiveLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereActiveTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereBusyKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereBusyTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereComplete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereInactiveKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereInactiveLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereInactiveLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereInactiveTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat wherePlate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereSeat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\HistorySeat whereVehicleId($value)
 */
class HistorySeat extends Model
{
    public $cpFrom = null;
    public $cpTo = null;

    public $isAscent = false;
    public $isDescent = false;

    /*protected $dates = [
      'date','time','active_time','inactive_time','busy_time'
    ];*/

    protected function getDateFormat()
    {
        return config('app.date_time_format');
    }

    public function getBusyKmAttribute($value)
    {
        return intval($value);
    }

    public function getActiveKmAttribute($value)
    {
        return intval($value);
    }

    public function getInactiveKmAttribute($value)
    {
        return intval($value);
    }

    function getTime($type, $short = false)
    {
        switch ($type) {
            case 'active':
                $date = $this->parseDate($this->active_time);
                return $short ? $date->toTimeString() : $date->toDateTimeString();
                break;
            case 'inactive':
                $date = $this->parseDate($this->inactive_time);
                return $short ? $date->toTimeString() : $date->toDateTimeString();
                break;
            default:
                return '';
        }
    }

    /**
     * @param $value
     * @return Carbon
     */
    function parseDate($value)
    {

        if (!$value) return Carbon::now();
        return Carbon::createFromFormat(config('app.simple_date_time_format'), explode('.', $value)[0]);
    }
}
