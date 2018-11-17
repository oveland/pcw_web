<?php

namespace App\Models\Passengers;

use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Passengers\CounterIssue
 *
 * @property int $id
 * @property string $date
 * @property int $total
 * @property int $total_prev
 * @property int $vehicle_id
 * @property int|null $dispatch_register_id
 * @property float $latitude
 * @property float $longitude
 * @property string $frame
 * @property string|null $items_issues
 * @property string|null $raspberry_cameras_issues
 * @property string|null $raspberry_check_counter_issue
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereFrame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereItemsIssues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereRaspberryCamerasIssues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereRaspberryCheckCounterIssue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereTotalPrev($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CounterIssue whereVehicleId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Routes\DispatchRegister|null $dispatchRegister
 */
class CounterIssue extends Model
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
}
