<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\GPSType
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $server_ip
 * @property string $server_port
 * @property string|null $reset_command
 * @property bool $active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType whereResetCommand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType whereServerIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType whereServerPort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $tags
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\GPSType whereTags($value)
 */
class GPSType extends Model
{
    protected $table = 'gps_types';
}
