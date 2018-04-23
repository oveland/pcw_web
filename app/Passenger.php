<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Passenger
 *
 * @property int $id
 * @property string $date
 * @property int $total
 * @property int $total_prev
 * @property int $vehicle_id
 * @property int|null $dispatch_register_id
 * @property int|null $location_id
 * @property int|null $counter_issue_id
 * @property float $latitude
 * @property float $longitude
 * @property string $frame
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int|null $total_platform
 * @property int|null $vehicle_status_id
 * @property-read \App\CounterIssue|null $counterIssue
 * @property-read \App\DispatchRegister|null $dispatchRegister
 * @property-read mixed $hex_seats
 * @property-read \App\Vehicle $vehicle
 * @property-read \App\VehicleStatus|null $vehicleStatus
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Passenger findAllByDateRange($vehicleId, $initialDate, $finalDate)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Passenger findAllByRoundTrip($vehicleId, $routeId, $roundTrip, $date)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Passenger whereCounterIssueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Passenger whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Passenger whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Passenger whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Passenger whereFrame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Passenger whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Passenger whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Passenger whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Passenger whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Passenger whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Passenger whereTotalPlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Passenger whereTotalPrev($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Passenger whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Passenger whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Passenger whereVehicleStatusId($value)
 * @mixin \Eloquent
 */
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
        return explode(' ',$this->frame)[3] ?? '000000';
    }

    public function vehicleStatus()
    {
        return $this->belongsTo(VehicleStatus::class, 'vehicle_status_id', 'id_status');
    }
}
