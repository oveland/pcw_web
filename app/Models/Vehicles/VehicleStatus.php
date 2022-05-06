<?php

namespace App\Models\Vehicles;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\VehicleStatus
 *
 * @property int|null $id_status
 * @property string|null $des_status
 * @method static Builder|VehicleStatus whereDesStatus($value)
 * @method static Builder|VehicleStatus whereIdStatus($value)
 * @mixin Eloquent
 * @property string|null $main_class
 * @property string|null $icon_class
 * @method static Builder|VehicleStatus whereIconClass($value)
 * @method static Builder|VehicleStatus whereMainClass($value)
 * @property-read mixed $id
 * @property int|null $order
 * @method static Builder|VehicleStatus whereOrder($value)
 * @property bool|null $show_filter
 * @method static Builder|VehicleStatus visibleFilter()
 * @method static Builder|VehicleStatus whereShowFilter($value)
 */
class VehicleStatus extends Model
{
    const OK = 0;
    const NO_REPORT = 1;
    const WITHOUT_GPS_SIGNAL = 5;
    const POWER_OFF = 6;
    const PARKED = 3;
    const IN_REPAIR = 31;
    const PANIC = 4;

    protected $table = 'status_vehi';

    public function getIdAttribute()
    {
        return $this->id_status;
    }

    /*
     * Checks if Status vehicle is OK
     */
    public function isOK()
    {
        return $this->id == self::OK;
    }

    /*
     * Checks if Status vehicle is Power Off
     */
    public function isPowerOff()
    {
        return $this->id == self::POWER_OFF;
    }

    /*
     * Checks if Status vehicle is No Report
     */
    public function isNoReport()
    {
        return $this->id == self::NO_REPORT;
    }

    /*
     * Checks if Status vehicle is Without GPS Signal
     */
    public function isWithoutGPSSignal()
    {
        return $this->id == self::WITHOUT_GPS_SIGNAL;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeVisibleFilter($query)
    {
        return $query->where('show_filter', true);
    }
}
