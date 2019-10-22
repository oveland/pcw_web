<?php

namespace App\Models\Vehicles;

use const Grpc\CALL_OK;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\VehicleStatus
 *
 * @property int|null $id_status
 * @property string|null $des_status
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatus whereDesStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatus whereIdStatus($value)
 * @mixin \Eloquent
 * @property string|null $main_class
 * @property string|null $icon_class
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatus whereIconClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatus whereMainClass($value)
 * @property-read mixed $id
 * @property int|null $order
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatus whereOrder($value)
 */
class VehicleStatus extends Model
{
    const OK = 0;
    const NO_REPORT = 1;
    const WITHOUT_GPS_SIGNAL = 5;
    const POWER_OFF = 6;
    const PARKED = 3;
    const IN_REPAIR = 31;

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
}
