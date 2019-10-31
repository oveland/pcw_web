<?php

namespace App\Models\Vehicles;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\VehicleIssueType
 *
 * @property int $id
 * @property bool $date
 * @property string $name
 * @property string|null $description
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|VehicleIssueType whereActive($value)
 * @method static Builder|VehicleIssueType whereCreatedAt($value)
 * @method static Builder|VehicleIssueType whereDate($value)
 * @method static Builder|VehicleIssueType whereDescription($value)
 * @method static Builder|VehicleIssueType whereId($value)
 * @method static Builder|VehicleIssueType whereName($value)
 * @method static Builder|VehicleIssueType whereUpdatedAt($value)
 * @mixin Eloquent
 */
class VehicleIssueType extends Model
{
    const IN = 1;
    const UPDATE = 2;
    const OUT = 3;

    public static function getColor($type)
    {
        switch ($type) {
            case self::IN:
                return 'warning';
                break;
            case self::OUT:
                return 'success';
                break;
            default:
                return 'default';
                break;
        }
    }

    protected $fillable = ['name', 'description', 'active'];
}
