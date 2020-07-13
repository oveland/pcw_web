<?php


namespace App\Models\Apps\Rocket\Traits;


use App\Models\Apps\Rocket\Photo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Interface PhotoInterface
 * @package App\Models\Apps\Rocket\Traits
 * @method static Builder|Photo newModelQuery()
 * @method static Builder|Photo newQuery()
 * @method static Builder|Photo query()
 * @mixin Eloquent
 * @property int $id
 * @property Carbon $date
 * @property int $vehicle_id
 * @property int $dispatch_register_id
 * @property int $location_id
 * @property string $path
 * @property string $side
 * @property string $type
 * @property string $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Photo whereCreatedAt($value)
 * @method static Builder|Photo whereData($value)
 * @method static Builder|Photo whereDispatchRegisterId($value)
 * @method static Builder|Photo whereId($value)
 * @method static Builder|Photo whereLocationId($value)
 * @method static Builder|Photo wherePath($value)
 * @method static Builder|Photo whereSide($value)
 * @method static Builder|Photo whereType($value)
 * @method static Builder|Photo whereUpdatedAt($value)
 * @method static Builder|Photo whereVehicleId($value)
 * @property-read Vehicle $vehicle
 * @method static Builder|Photo whereDate($value)
 * @property int|null $persons
 * @property-read DispatchRegister|null $dispatchRegister
 * @method static Builder|Photo wherePersons($value)
 * @property object $effects
 * @method static Builder|Photo whereEffects($value)
 * @property string $disk
 * @method static Builder|Photo whereDisk($value)
 * @property string $rekognition
 * @method static Builder|Photo whereRekognition($value)
 * @property string|null $data_persons
 * @method static Builder|Photo whereDataPersons($value)
 * @property string|null $data_faces
 * @method static Builder|Photo whereDataFaces($value)
 * @property string|null $data_properties
 * @method static Builder|Photo whereDataProperties($value)
 */

interface PhotoInterface
{
    /**
     * @param $date
     * @return Carbon
     */
    public function getDateAttribute($date);

    /**
     * @return string
     */
    public function getDateFormat();

    /**
     * @return BelongsTo | Vehicle
     */
    public function vehicle();

    /**
     * @return BelongsTo | DispatchRegister
     */
    public function dispatchRegister();

    /**
     * @param string $encodeImage
     * @return object
     */
    public function getAPIFields($encodeImage = 'url');

    /**
     * @param $data
     * @return void
     */
    public function setDataAttribute($data);

    /**
     * @param $effects
     * @return void
     */
    public function setEffectsAttribute($effects);

    /**
     * @param $data
     * @return object
     */
    function getDataAttribute($data);

    /**
     * @param $effects
     * @return object
     */
    function getEffectsAttribute($effects);
}