<?php

namespace App\Models\Apps\Rocket;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Apps\Rocket\PhotoEvent
 *
 * @method static Builder|PhotoEvent newModelQuery()
 * @method static Builder|PhotoEvent newQuery()
 * @method static Builder|PhotoEvent query()
 * @mixin Eloquent
 * @property int $id
 * @property string $date
 * @property string $imei
 * @property string $uid
 * @property string $side
 * @property bool $taken
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|PhotoEvent whereCreatedAt($value)
 * @method static Builder|PhotoEvent whereDate($value)
 * @method static Builder|PhotoEvent whereId($value)
 * @method static Builder|PhotoEvent whereImei($value)
 * @method static Builder|PhotoEvent whereSide($value)
 * @method static Builder|PhotoEvent whereTaken($value)
 * @method static Builder|PhotoEvent whereUid($value)
 * @method static Builder|PhotoEvent whereUpdatedAt($value)
 */
class PhotoEvent extends Model
{
    protected $table = 'app_photo_events';
    
    //protected $dates = ['date'];

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }
    
    function getDateAttribute($date)
    {
 	  return Carbon::createFromFormat(
        	$this->getDateFormat(),
        	$date
         );
    }
}
