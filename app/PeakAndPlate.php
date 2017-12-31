<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Carbon\Carbon $date
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
