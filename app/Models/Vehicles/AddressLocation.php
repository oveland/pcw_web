<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\AddressLocation
 *
 * @property int $id
 * @property int $location_id
 * @property string $address
 * @property int $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\AddressLocation whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\AddressLocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\AddressLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\AddressLocation whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\AddressLocation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\AddressLocation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AddressLocation extends Model
{
    protected $fillable = ['address', 'status'];
}
