<?php

namespace App\Models\Apps\Concox;

use App\Models\Vehicles\Vehicle;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Apps\Concox\PhotoRequest
 *
 * @method static Builder|PhotoRequest newModelQuery()
 * @method static Builder|PhotoRequest newQuery()
 * @method static Builder|PhotoRequest query()
 * @mixin Eloquent
 * @property int $id
 * @property string $date
 * @property int $vehicle_id
 * @property string $type
 * @property string|null $params
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|PhotoRequest whereCreatedAt($value)
 * @method static Builder|PhotoRequest whereDate($value)
 * @method static Builder|PhotoRequest whereId($value)
 * @method static Builder|PhotoRequest whereParams($value)
 * @method static Builder|PhotoRequest whereType($value)
 * @method static Builder|PhotoRequest whereUpdatedAt($value)
 * @method static Builder|PhotoRequest whereVehicleId($value)
 * @property-read Vehicle $vehicle
 */
class PhotoRequest extends Model
{
    protected $table = 'app_photo_requests';

    protected $dates = ['date'];

    protected $fillable = ['date', 'vehicle_id', 'type', 'params'];

    public function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
