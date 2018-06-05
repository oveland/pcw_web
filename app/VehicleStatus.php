<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\VehicleStatus
 *
 * @property int|null $id_status
 * @property string|null $des_status
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleStatus whereDesStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleStatus whereIdStatus($value)
 * @mixin \Eloquent
 * @property string|null $main_class
 * @property string|null $icon_class
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleStatus whereIconClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleStatus whereMainClass($value)
 * @property-read mixed $id
 */
class VehicleStatus extends Model
{
    protected $table = 'status_vehi';

    public function getIdAttribute()
    {
        return $this->id_status;
    }
}
