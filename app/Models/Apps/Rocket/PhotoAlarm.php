<?php

namespace App\Models\Apps\Rocket;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Apps\Rocket\PhotoAlarm
 *
 * @property int $id
 * @property string $date
 * @property int $app_photo_id
 * @property bool $detected
 * @property string $type
 * @property mixed $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|PhotoAlarm newModelQuery()
 * @method static Builder|PhotoAlarm newQuery()
 * @method static Builder|PhotoAlarm query()
 * @method static Builder|PhotoAlarm whereAppPhotoId($value)
 * @method static Builder|PhotoAlarm whereCreatedAt($value)
 * @method static Builder|PhotoAlarm whereData($value)
 * @method static Builder|PhotoAlarm whereDate($value)
 * @method static Builder|PhotoAlarm whereDetected($value)
 * @method static Builder|PhotoAlarm whereId($value)
 * @method static Builder|PhotoAlarm whereType($value)
 * @method static Builder|PhotoAlarm whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Photo $photo
 */
class PhotoAlarm extends Model
{
    protected $table = 'app_photo_alarms';

    function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class, 'app_photo_id');
    }

    public function getDataAttribute($data)
    {
        return collect(json_decode($data ?? "[]"));
    }

    public function setDataAttribute($data)
    {
        $this->attributes['data'] = collect($data)->toJson();
    }
}
