<?php

namespace App\Models\Routes;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Routes\Dispatch
 *
 * @property int $id
 * @property string $name
 * @property int $company_id
 * @property bool $active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Dispatch whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Dispatch whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Dispatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Dispatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Dispatch whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Dispatch whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property float $latitude
 * @property float $longitude
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Dispatch whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Dispatch whereLongitude($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Routes\DispatcherVehicle[] $dispatcherVehicles
 * @property int|null $radio_geofence
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Dispatch whereRadioGeofence($value)
 */
class Dispatch extends Model
{
    public function dispatcherVehicles()
    {
        return $this->hasMany(DispatcherVehicle::class)->active();
    }
}
