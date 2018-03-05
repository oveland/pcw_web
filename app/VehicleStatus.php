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
 */
class VehicleStatus extends Model
{
    protected $table = 'status_vehi';
}
