<?php

namespace App\Models\Passengers;

use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Vehicle;
use App\Models\Vehicles\VehicleStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Passengers\Passenger
 *
 * @property int $id
 * @property \Carbon\Carbon $date
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
 * @property-read \App\Models\Passengers\CounterIssue|null $counterIssue
 * @property-read \App\Models\Routes\DispatchRegister|null $dispatchRegister
 * @property-read mixed $hex_seats
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 * @property-read \App\Models\Vehicles\VehicleStatus|null $vehicleStatus
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\Passenger findAllByDateRange($vehicleId, $initialDate, $finalDate)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\Passenger findAllByRoundTrip($vehicleId, $routeId, $roundTrip, $date)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\Passenger whereCounterIssueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\Passenger whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\Passenger whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\Passenger whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\Passenger whereFrame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\Passenger whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\Passenger whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\Passenger whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\Passenger whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\Passenger whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\Passenger whereTotalPlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\Passenger whereTotalPrev($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\Passenger whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\Passenger whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\Passenger whereVehicleStatusId($value)
 * @mixin \Eloquent
 * @property int|null $total_sensor_recorder
 * @property int|null $total_front_sensor
 * @property int|null $total_back_sensor
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\Passenger whereTotalBackSensor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\Passenger whereTotalFrontSensor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\Passenger whereTotalSensorRecorder($value)
 * @property int|null $fringe_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\Passenger whereFringeId($value)
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

    public function scopeWhereDate($query, $date)
    {
        return $query->whereBetween('date', ["$date 00:00:00", "$date 23:59:59"]);
    }

    public function scopeFindAllByDateRange($query, $vehicleId, $initialDate, $finalDate)
    {
        return $query
            ->where('vehicle_id', $vehicleId)
            ->whereBetween('date', [$initialDate, $finalDate]);
    }

    public function getHexSeatsAttribute()
    {
        $arrayFrame = explode(' ',$this->frame);

        $hexFromFrame = '000000';
        foreach ($arrayFrame as $a){
            if( strlen($a) == 6 ){
                $hexFromFrame = $a;
                break;
            }
        }

        return $hexFromFrame;
    }

    public function vehicleStatus()
    {
        return $this->belongsTo(VehicleStatus::class, 'vehicle_status_id', 'id_status');
    }

    public function totalCount()
    {
        return ($this->total - $this->total_prev);
    }
}