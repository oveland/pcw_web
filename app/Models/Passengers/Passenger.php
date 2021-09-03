<?php

namespace App\Models\Passengers;

use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Vehicle;
use App\Models\Vehicles\VehicleStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Passengers\Passenger
 *
 * @property int $id
 * @property Carbon $date
 * @property int $total
 * @property int $total_prev
 * @property int $vehicle_id
 * @property int|null $dispatch_register_id
 * @property int|null $location_id
 * @property int|null $counter_issue_id
 * @property float $latitude
 * @property float $longitude
 * @property string $frame
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $total_platform
 * @property int|null $vehicle_status_id
 * @property-read CounterIssue|null $counterIssue
 * @property-read DispatchRegister|null $dispatchRegister
 * @property-read mixed $hex_seats
 * @property-read Vehicle $vehicle
 * @property-read VehicleStatus|null $vehicleStatus
 * @method static Builder|Passenger findAllByDateRange($vehicleId, $initialDate, $finalDate)
 * @method static Builder|Passenger findAllByRoundTrip($vehicleId, $routeId, $roundTrip, $date)
 * @method static Builder|Passenger whereCounterIssueId($value)
 * @method static Builder|Passenger whereCreatedAt($value)
 * @method static Builder|Passenger whereDate($value)
 * @method static Builder|Passenger whereDispatchRegisterId($value)
 * @method static Builder|Passenger whereFrame($value)
 * @method static Builder|Passenger whereId($value)
 * @method static Builder|Passenger whereLatitude($value)
 * @method static Builder|Passenger whereLocationId($value)
 * @method static Builder|Passenger whereLongitude($value)
 * @method static Builder|Passenger whereTotal($value)
 * @method static Builder|Passenger whereTotalPlatform($value)
 * @method static Builder|Passenger whereTotalPrev($value)
 * @method static Builder|Passenger whereUpdatedAt($value)
 * @method static Builder|Passenger whereVehicleId($value)
 * @method static Builder|Passenger whereVehicleStatusId($value)
 * @mixin \Eloquent
 * @property int|null $total_sensor_recorder
 * @property int|null $total_front_sensor
 * @property int|null $total_back_sensor
 * @method static Builder|Passenger whereTotalBackSensor($value)
 * @method static Builder|Passenger whereTotalFrontSensor($value)
 * @method static Builder|Passenger whereTotalSensorRecorder($value)
 * @property int|null $fringe_id
 * @method static Builder|Passenger whereFringeId($value)
 * @method static Builder|Passenger newModelQuery()
 * @method static Builder|Passenger newQuery()
 * @method static Builder|Passenger query()
 * @property mixed hexSeats
 * @property int|null $history_seat_id
 * @method static Builder|Passenger whereHistorySeatId($value)
 * @property int|null $in_round_trip
 * @property int|null $out_round_trip
 * @property int|null $total_ascents
 * @property int|null $total_descents
 * @property int|null $ascents_in_round_trip
 * @property int|null $descents_in_round_trip
 * @property int $counted
 * @property int $tariff
 * @property int $charge
 * @property int $total_charge
 * @method static Builder|Passenger whereAscentsInRoundTrip($value)
 * @method static Builder|Passenger whereCharge($value)
 * @method static Builder|Passenger whereCounted($value)
 * @method static Builder|Passenger whereDescentsInRoundTrip($value)
 * @method static Builder|Passenger whereInRoundTrip($value)
 * @method static Builder|Passenger whereOutRoundTrip($value)
 * @method static Builder|Passenger whereTariff($value)
 * @method static Builder|Passenger whereTotalAscents($value)
 * @method static Builder|Passenger whereTotalCharge($value)
 * @method static Builder|Passenger whereTotalDescents($value)
 */
class Passenger extends Model
{
    protected $fillable = [
        'date',
        'dispatch_register_id',
        'location_id',
        'vehicle_id',
        'total',
        'total_prev',
        'in_round_trip',
        'ascents_in_round_trip',
        'descents_in_round_trip',
        'frame',
        'tags'
    ];

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

    /**
     * @param $query
     * @param $date
     * @return mixed
     */
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
        $arrayFrame = explode(' ', $this->frame);

        $hexFromFrame = '000000';
        foreach ($arrayFrame as $a) {
            if (strlen($a) == 6) {
                $hexFromFrame = $a;
                if ($this->vehicle_id != 1086) {
                    break;
                }
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
