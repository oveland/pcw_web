<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    public function getDateAttribute($date)
    {
        return Carbon::createFromFormat(config('app.simple_date_time_format'),explode('.',$date)[0]);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class);
    }

    public function counterIssue()
    {
        return $this->belongsTo(CounterIssue::class);
    }
}
