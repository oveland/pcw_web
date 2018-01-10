<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\PeakAndPlate
 *
 * @property \Carbon\Carbon $date
 * @property int $id
 * @property int $week_day
 * @property int $vehicle_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PeakAndPlate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PeakAndPlate whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PeakAndPlate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PeakAndPlate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PeakAndPlate whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PeakAndPlate whereWeekDay($value)
 * @mixin \Eloquent
 */
class PeakAndPlate extends Model
{
    protected function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    protected $fillable = ['date','week_day','vehicle_id'];

    protected $dates = ['date'];
}
