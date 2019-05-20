<?php

namespace App\Models\BEA;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BEA\Trajectory
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory whereUpdatedAt($value)
 */
class Trajectory extends Model
{
    protected $table = 'bea_trajectories';
}
