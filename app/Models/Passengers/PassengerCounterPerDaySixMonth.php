<?php

namespace App\Models\Passengers;

use Illuminate\Database\Eloquent\Model;

class PassengerCounterPerDaySixMonth extends Model
{
    protected function getDateFormat()
    {
        return config('app.date_format');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
