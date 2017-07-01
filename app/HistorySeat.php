<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistorySeat extends Model
{
    protected function getDateFormat()
    {
        return config('app.date_format');
    }
    /*protected $dates = [
      'date','time','active_time','inactive_time','busy_time'
    ];*/
}
