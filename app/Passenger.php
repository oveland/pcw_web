<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    public function getDateAttribute($date)
    {
        return Carbon::createFromFormat(config(str_contains($date, ":") ? 'app.simple_date_time_format' : 'app.date_format'), explode('.', $date)[0]);
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

    public function scopeFindAllByRoundTrip($query, $vehicleId, $routeId, $roundTrip, $date)
    {
        return $query
            ->select('passengers.*')
            ->leftJoin('dispatch_registers', 'passengers.dispatch_register_id', '=', 'dispatch_registers.id')
            ->where('dispatch_registers.round_trip', $roundTrip)
            ->where('dispatch_registers.route_id', $routeId)
            ->where('passengers.vehicle_id', '=', $vehicleId)
            ->whereBetween('passengers.date', ["$date 00:00:00", "$date 23:59:59"]);
    }

    public function scopeFindAllByDateRange($query, $vehicleId, $initialDate, $finalDate)
    {
        return $query
            ->where('vehicle_id', $vehicleId)
            ->whereBetween('date', [$initialDate, $finalDate]);
    }

    public function getHexSeatsAttribute()
    {
        return explode(' ',$this->frame)[3];
    }
}
