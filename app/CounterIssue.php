<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\CounterIssue
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
 * @property-read \App\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CounterIssue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CounterIssue whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CounterIssue whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CounterIssue whereFrame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CounterIssue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CounterIssue whereItemsIssues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CounterIssue whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CounterIssue whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CounterIssue whereRaspberryCamerasIssues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CounterIssue whereRaspberryCheckCounterIssue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CounterIssue whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CounterIssue whereTotalPrev($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CounterIssue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CounterIssue whereVehicleId($value)
 * @mixin \Eloquent
 * @property-read \App\DispatchRegister|null $dispatchRegister
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
